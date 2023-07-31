import { Component, OnDestroy, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { Subscription, first } from 'rxjs';
import { Product } from '../../models/product';
import { ProductService } from '../../services/product.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css'],
})
export class HomeComponent implements OnInit, OnDestroy {
  private _products?: Product[];
  private _links?: any[];
  private subs: Subscription = new Subscription();
  private subs2: Subscription = new Subscription();

  constructor(private productService: ProductService, private toastr: ToastrService) {}

  ngOnInit(): void {
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

  onLoadImg(event: any): void {
    event.srcElement.classList = '';
  }

  onChangePagination(event: any): void {
    event.preventDefault();
    this.subs2 = this.productService
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
  }

  get products() {
    return this._products;
  }

  get links() {
    return this._links;
  }
}
