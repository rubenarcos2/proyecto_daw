import { Injectable } from '@angular/core';
import { Supplier } from '../models/supplier';
import { environment } from 'src/environments/environment';
import { HttpClient } from '@angular/common/http';
import { map } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class SupplierService {
  baseUrl = environment.baseUrl;

  constructor(private http: HttpClient) {}

  getAll(param?: any) {
    if (param)
      return this.http.get<Supplier[]>(`${this.baseUrl}/supplier?${param}`).pipe(
        map(result => {
          return result;
        })
      );
    else
      return this.http.get<Supplier[]>(`${this.baseUrl}/supplier`).pipe(
        map(result => {
          return result;
        })
      );
  }

  getAllNoPaginated(param?: any) {
    return this.http.get<Supplier[]>(`${this.baseUrl}/supplier/all`).pipe(
      map(result => {
        return result;
      })
    );
  }

  getById(id: any) {
    return this.http.get<Supplier>(`${this.baseUrl}/supplier/${id}`);
  }

  create(params: any) {
    return this.http.post(`${this.baseUrl}/supplier/create`, params);
  }

  update(params: any, id: any) {
    return this.http.post(`${this.baseUrl}/supplier/update/${id}`, params);
  }

  delete(params: any, id: any) {
    return this.http.post(`${this.baseUrl}/supplier/delete/${id}`, params);
  }
}
