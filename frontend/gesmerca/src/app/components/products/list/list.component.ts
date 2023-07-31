import { Component, OnDestroy, OnInit } from '@angular/core';
import { Product } from 'src/app/models/product';
import { ProductService } from 'src/app/services/product.service';
import { ToastrService } from 'ngx-toastr';
import { AuthService } from 'src/app/services/auth.service';
import { Subscription, first } from 'rxjs';

@Component({ templateUrl: 'list.component.html' })
export class ProductListComponent implements OnInit, OnDestroy {
  private _products?: Product[];
  private _links?: any[];
  private subs: Subscription = new Subscription();
  private subs2: Subscription = new Subscription();
  private subs3: Subscription = new Subscription();

  constructor(
    private productService: ProductService,
    private toastr: ToastrService,
    public authService: AuthService
  ) {}

  ngOnInit() {
    this.subs = this.productService
      .getAll()
      .pipe(first())
      .subscribe({
        next: result => {
          let res = JSON.parse(JSON.stringify(result));
          this._links = res.links;
          this._products = res.data;
        },
        error: error => {
          this.toastr.error(error ? error : 'No se puede conectar con el servidor');
        },
      });
  }

  deleteProduct(name: any, id: any) {
    if (window.confirm('¿Seguro que desea borrar el producto ' + name + '?')) {
      const product = this.products!.find(x => x.id === id);
      this.subs2 = this.productService.delete(product).subscribe({
        next: result => {
          this._products = this.products!.filter(x => x.id !== id);
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
    this.subs3 = this.productService
      .getAll(event.target.href.split('?')[1])
      .pipe(first())
      .subscribe({
        next: result => {
          let res = JSON.parse(JSON.stringify(result));
          this._links = res.links;
          this._products = res.data;
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
  }

  get products() {
    return this._products;
  }

  get links() {
    return this._links;
  }
}
