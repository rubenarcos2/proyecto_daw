import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { map } from 'rxjs';
import { environment } from 'src/environments/environment';
import { ToastrService } from 'ngx-toastr';
import { User } from '../models/user';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private _baseUrl = environment.baseUrl;
  private _user!: User;

  constructor(private http: HttpClient, private toastr: ToastrService) {}

  login(data: User) {
    return this.http.post(`${this._baseUrl}/auth/login`, data).pipe(
      map(result => {
        sessionStorage.setItem('authUser', JSON.stringify(result));
        this._user = result;
        return result;
      })
    );
  }

  register(data: any) {
    return this.http.post(`${this._baseUrl}/auth/register`, data);
  }

  profile() {
    return this.http.get(`${this._baseUrl}/auth/user-profile`).pipe(
      map(result => {
        let res = JSON.parse(JSON.stringify(result));
        this._user = res.user;
        this._user.permissions = res.permissions;
        return result;
      })
    );
  }

  logout() {
    return this.http.get(`${this._baseUrl}/auth/logout`).pipe(
      map(result => {
        let msg = JSON.parse(JSON.stringify(result));
        sessionStorage.removeItem('authUser');
        sessionStorage.removeItem('userConfig');
        this.toastr.info(msg.message);
      })
    );
  }

  getAuthUser() {
    return JSON.parse(sessionStorage.getItem('authUser') as string);
  }

  getAllUsers() {
    return this.http.get(`${this._baseUrl}/auth/users`);
  }

  hasRole(rol: string) {
    if (this.user !== undefined) return this._user.roles?.find(r => r.name == rol);
    else return false;
  }

  hasPermission(permission: string) {
    if (this.user !== undefined) return this._user.permissions?.find(p => p.name == permission);
    else return false;
  }

  get isLoggedIn() {
    if (sessionStorage.getItem('authUser')) {
      return true;
    }
    return false;
  }

  get user() {
    return this._user;
  }
}