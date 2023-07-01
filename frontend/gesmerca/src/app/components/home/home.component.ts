import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { first } from 'rxjs';
import { Product } from '../../models/product';
import { ProductService } from '../../services/product.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {

  private _products?: Product[];
  private _links?:any[];

  constructor(
    private productService:ProductService,
    private toastr: ToastrService,
    ) { }

  ngOnInit(): void {
    this.productService.getAll()
      .pipe(first())
      .subscribe({
        next: (result) => {
          let res = JSON.parse(JSON.stringify(result));
          this._links = res.links;
          this._products = res.data
        },
        error: (error) => {
          this.toastr.error(error ? error : "No se puede conectar con el servidor");
        }
      });
  }

  onLoadImg(event:any) :void {
    event.srcElement.classList = '';
  }

  onChangePagination(event:any) :void{
    event.preventDefault();
    this.productService.getAll(event.target.href.split('?')[1])
      .pipe(first())
      .subscribe({
        next: (result) => {
          let res = JSON.parse(JSON.stringify(result));
          this._links = res.links;
          this._products = res.data
        },
        error: (error) => {
          this.toastr.error(error ? error : "No se puede conectar con el servidor");
        }
      });
  }

  get products(){
    return this._products;
  }

  get links(){
    return this._links;
  }
}
