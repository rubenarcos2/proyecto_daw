import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from 'src/environments/environment';
import { Permissions } from '../models/permissions';

@Injectable({
  providedIn: 'root',
})
export class PermissionService {
  baseUrl = environment.baseUrl;

  constructor(private http: HttpClient) {}

  getAll() {
    return this.http.get<Permissions[]>(`${this.baseUrl}/permission/`);
  }

  getById(id: any) {
    return this.http.get<Permissions>(`${this.baseUrl}/permission/${id}`);
  }

  getPermissionsUser(id: any) {
    return this.http.get<Permissions>(`${this.baseUrl}/permission/user/${id}`);
  }

  setPermissionsUser(params: any, id: any) {
    return this.http.post(`${this.baseUrl}/permission/user/${id}`, params);
  }
}
