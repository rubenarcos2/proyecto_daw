import { Component, OnDestroy, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { AuthService } from 'src/app/services/auth.service';
import { Subscription, first } from 'rxjs';
import { Supplier } from 'src/app/models/supplier';
import { SupplierService } from 'src/app/services/supplier.service';

@Component({ templateUrl: 'list.component.html' })
export class SupplierListComponent implements OnInit, OnDestroy {
  private _suppliers?: Supplier[];
  private _links?: any[];
  private subs: Subscription = new Subscription();
  private subs2: Subscription = new Subscription();

  constructor(
    private supplierService: SupplierService,
    private toastr: ToastrService,
    public authService: AuthService
  ) {}

  /**
   * This function start on event page
   *
   */
  ngOnInit() {
    //Get all suppliers of backend
    this.subs = this.supplierService
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

  /**
   * This function execute on event delete button
   *
   * Detect if user confirm the action and proced to delete this supplier
   *
   */
  deleteSupplier(name: any, id: any) {
    if (window.confirm('¿Seguro que desea borrar el proveedor ' + name + '?')) {
      const supplier = this.suppliers!.find(x => x.id === id);

      //Remove this suplier of backend
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

  /**
   * When load image remove spinner
   */
  onLoadImg(event: any) {
    event.srcElement.classList = '';
  }

  /**
   * Get a group of suppliers of paginate selected
   */
  onChangePagination(event: any): void {
    event.preventDefault();
    this.subs2 = this.supplierService
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

  /**
   * This function start on destroy event page
   *
   * Unsuscribe all observable suscriptions
   *
   */
  ngOnDestroy() {
    this.subs.unsubscribe();
    this.subs2.unsubscribe();
  }

  get suppliers() {
    return this._suppliers;
  }

  get links() {
    return this._links;
  }
}
