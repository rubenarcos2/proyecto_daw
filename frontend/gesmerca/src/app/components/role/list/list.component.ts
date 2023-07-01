import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { Role } from 'src/app/models/role';
import { User } from 'src/app/models/user';
import { AuthService } from 'src/app/services/auth.service';
import { RoleService } from 'src/app/services/role.service';

@Component({
  selector: 'app-list',
  templateUrl: './list.component.html',
  styleUrls: ['./list.component.css'],
})
export class RoleListComponent implements OnInit {
  private _users!: User[];
  private _roles!: Role[];

  constructor(
    protected authService: AuthService,
    private roleService: RoleService,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.authService.getAllUsers().subscribe({
      next: result => {
        this._users = JSON.parse(JSON.stringify(result));
        this._users.forEach(u => {
          this.roleService.getRoleUser(u.id).subscribe({
            next: result => {
              let rol = JSON.parse(JSON.stringify(result));
              this.selectCmbRole(u, rol);
            },
            error: error => {
              this.toastr.error(error ? error : 'No se puede conectar con el servidor');
            },
          });
        });
      },
      error: error => {
        this.toastr.error(error ? error : 'No se puede conectar con el servidor');
      },
    });
    this.roleService.getAll().subscribe({
      next: result => {
        this._roles = JSON.parse(JSON.stringify(result));
      },
      error: error => {
        this.toastr.error(error ? error : 'No se puede conectar con el servidor');
      },
    });
  }

  selectCmbRole(u: User, rol: Role) {
    u.roles?.push(rol);
    let sel = document.getElementById('select-roles-' + u.id) as HTMLSelectElement;
    let op = document.getElementById(u.id + '-' + rol) as HTMLOptionElement;
    sel.removeAttribute('disabled');
    op.selected = true;
    document.getElementById('btn-' + u.id)?.removeAttribute('disabled');
  }

  updateRole(event: any) {
    let btnSave = event.target;
    let userId = btnSave.id.substring('btn-'.length);
    console.log(userId);
    let select = document.getElementById('select-roles-' + userId) as HTMLSelectElement;
    var roleId = select.value.substring(select.value.indexOf('-') + 1);
    console.log(roleId);
    if (window.confirm('¿Está seguro que desea cambiar el rol al usuario?')) {
      let param = new FormData();
      param.append('id', roleId);
      this.roleService.setRoleUser(param, userId).subscribe({
        next: result => {
          let res = JSON.parse(JSON.stringify(result));
          res.error ? this.toastr.error(res.error) : this.toastr.success(res.message);
        },
        error: error => {
          this.toastr.error(error ? error : 'No se puede conectar con el servidor');
        },
      });
    }
  }

  get users(): User[] {
    return this._users;
  }

  get roles(): Role[] {
    return this._roles;
  }
}
