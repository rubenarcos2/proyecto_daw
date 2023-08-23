import { Component, OnDestroy, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { Subscription, first } from 'rxjs';
import { GoodsreceiptService } from 'src/app/services/goodsreceipt.service';
import { GoodsReceipt } from 'src/app/models/goodsreceipt';
import { AuthService } from 'src/app/services/auth.service';
import { SupplierService } from 'src/app/services/supplier.service';
import { User } from 'src/app/models/user';
import { GoodsReceiptProduct } from 'src/app/models/goodsreceiptproduct';

@Component({ templateUrl: 'list.component.html' })
export class GoodsReceiptListComponent implements OnInit, OnDestroy {
  private _goodsReceipt?: GoodsReceipt[];
  private _links?: any[];
  private subs: Subscription = new Subscription();
  private subs2: Subscription = new Subscription();
  private subs3: Subscription = new Subscription();
  private subs4: Subscription = new Subscription();
  private subs5: Subscription = new Subscription();
  private subs6: Subscription = new Subscription();

  constructor(
    private goodsReceiptService: GoodsreceiptService,
    private supplierService: SupplierService,
    private toastr: ToastrService,
    public authService: AuthService
  ) {}

  /**
   * This function start on event page
   *
   */
  ngOnInit() {
    this.getSupliersAndUsers();
  }

  /**
   * Get all suppliers and users of backend
   *
   */
  getSupliersAndUsers() {
    //Get all goods receipt
    this.subs = this.goodsReceiptService
      .getAll()
      .pipe(first())
      .subscribe({
        next: result => {
          let res = JSON.parse(JSON.stringify(result));
          this._links = res.links;
          this._goodsReceipt = res.data;
          this.goodsreceipts!.forEach(e => {
            //Get all suppliers by id
            this.subs = this.supplierService.getById(e.idsupplier).subscribe({
              next: result => {
                e.supplierName = result.name;
              },
              error: error => {
                this.toastr.error(error ? error : 'Operación no autorizada');
              },
            });
          });

          //Get all users of backend
          this.subs2 = this.authService.getAllUsers().subscribe({
            next: result => {
              let res = JSON.parse(JSON.stringify(result));
              let users: User[] = res;
              this._goodsReceipt!.forEach(e => {
                e.userName = users.filter(el => e.iduser == el.id)[0]?.name;
              });
            },
            error: error => {
              this.toastr.error(error ? error : 'Operación no autorizada');
            },
          });
        },
        error: error => {
          this.toastr.error(error ? error : 'No se puede conectar con el servidor');
        },
      });
  }

  /**
   * This function execute on event delete button
   *
   * Detect if user confirm the action and proced to delete this goods receipt
   *
   */
  deleteProduct(name: any, id: any) {
    if (
      window.confirm('¿Seguro que desea borrar el albarán de recepción de mercancía ' + name + '?')
    ) {
      const supplier = this.goodsreceipts!.find(x => x.id === id);
      this.subs5 = this.goodsReceiptService.delete(supplier, id).subscribe({
        next: result => {
          this._goodsReceipt = this.goodsreceipts!.filter(x => x.id !== id);
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
   * Get a group of goods receipt of paginate selected
   */
  onChangePagination(event: any): void {
    event.preventDefault();

    //Get all goods receipt paginated
    this.subs6 = this.goodsReceiptService
      .getAll(event.target.href.split('?')[1])
      .pipe(first())
      .subscribe({
        next: result => {
          let res = JSON.parse(JSON.stringify(result));
          this._links = res.links;
          this._goodsReceipt = res.data;
          this.getSupliersAndUsers();
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
    this.subs3.unsubscribe();
    this.subs4.unsubscribe();
    this.subs5.unsubscribe();
    this.subs6.unsubscribe();
  }

  get goodsreceipts() {
    return this._goodsReceipt;
  }

  get links() {
    return this._links;
  }
}
