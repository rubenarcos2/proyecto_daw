import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { AuthService } from 'src/app/services/auth.service';
import { first } from 'rxjs';
import { Supplier } from 'src/app/models/supplier';
import { SupplierService } from 'src/app/services/supplier.service';

@Component({ templateUrl: 'list.component.html' })
export class SupplierListComponent implements OnInit {
  private _suppliers?: Supplier[];
  private _links?: any[];

  constructor(
    private supplierService: SupplierService,
    private toastr: ToastrService,
    public authService: AuthService
  ) {}

  ngOnInit() {
    this.supplierService
      .getAll()
      .pipe(first())
      .subscribe({
        next: result => {
          let res = JSON.parse(JSON.stringify(result));
          this._links = res.links;
          this._suppliers = res.data;
        },
        error: error => {
          this.toastr.error(error ? error : 'No se puede conectar con el servidor');
        },
      });
  }

  deleteProduct(name: any, id: any) {
    if (window.confirm('¿Seguro que desea borrar el producto ' + name + '?')) {
      const supplier = this.suppliers!.find(x => x.id === id);
      this.supplierService.delete(supplier, id).subscribe({
        next: result => {
          this._suppliers = this.suppliers!.filter(x => x.id !== id);
          let msg = JSON.parse(JSON.stringify(result));
          this.toastr.success(msg.message);
        },
        error: error => {
          this.toastr.error(error ? error : 'Operación no autorizada');
        },
      });
    }
  }

  onLoadImg(event: any) {
    event.srcElement.classList = '';
  }

  onChangePagination(event: any): void {
    event.preventDefault();
    this.supplierService
      .getAll(event.target.href.split('?')[1])
      .pipe(first())
      .subscribe({
        next: result => {
          let res = JSON.parse(JSON.stringify(result));
          this._links = res.links;
          this._suppliers = res.data;
        },
        error: error => {
          this.toastr.error(error ? error : 'No se puede conectar con el servidor');
        },
      });
  }

  get suppliers() {
    return this._suppliers;
  }

  get links() {
    return this._links;
  }
}
