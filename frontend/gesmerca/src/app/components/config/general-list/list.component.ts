import { Component, OnInit } from '@angular/core';
import { Config } from 'src/app/models/config';
import { ConfigService } from 'src/app/services/config.service';
import { ToastrService } from 'ngx-toastr';
import { AuthService } from 'src/app/services/auth.service';
import { first } from 'rxjs';

@Component({
  templateUrl: 'list.component.html',
  styleUrls: ['./list.component.css'],
})
export class ConfigGeneralListComponent implements OnInit {
  private _configs?: Config[];

  constructor(
    private configService: ConfigService,
    private toastr: ToastrService,
    public authService: AuthService
  ) {}

  ngOnInit() {
    this.configService
      .getAll()
      .pipe(first())
      .subscribe({
        next: result => {
          let res = JSON.parse(JSON.stringify(result));
          this._configs = res;
        },
        error: error => {
          this.toastr.error(error ? error : 'No se puede conectar con el servidor');
        },
      });
  }

  get configs() {
    return this._configs;
  }
}
