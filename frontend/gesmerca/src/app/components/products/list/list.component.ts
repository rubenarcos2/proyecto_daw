import { Component, OnInit } from '@angular/core';
import { Product } from 'src/app/models/product';
import { ProductService } from 'src/app/services/product.service';
import { ToastrService } from 'ngx-toastr';
import { AuthService } from 'src/app/services/auth.service';
import { first } from 'rxjs';

@Component({ templateUrl: 'list.component.html' })
export class ProductListComponent implements OnInit {

    private _products?: Product[];
    private _links?:any[];

    constructor(
        private productService: ProductService,
        private toastr: ToastrService,
        public authService: AuthService,
    ) {}

    ngOnInit() {
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

    deleteProduct(name:any, id:any) {
        if(window.confirm('¿Seguro que desea borrar el producto ' + name + '?')){
            const product = this.products!.find(x => x.id === id);
            this.productService.delete(product)
            .subscribe({
                next: (result) => {
                    this._products = this.products!.filter(x => x.id !== id);
                    let msg = JSON.parse(JSON.stringify(result));
                    this.toastr.success(msg.message);
                },
                error: (error) => {
                    this.toastr.error(error ? error : "Operación no autorizada");
                }
            });
        }
    }

    onLoadImg(event:any){
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