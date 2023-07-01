import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { User } from 'src/app/models/user';
import { AuthService } from 'src/app/services/auth.service';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent implements OnInit {

  protected _user!: User;

  constructor(
    private authService : AuthService,
    private toastr: ToastrService,
  ) { }

  ngOnInit(): void {
    this.authService.profile().subscribe({
      complete: () => {
        this._user = this.authService.user;
      },
      error: (error) => {
        this.toastr.error(error ? error : "No se puede conectar con el servidor");
      }
    });
  }

  get user(){
    return this._user;
  }
}
