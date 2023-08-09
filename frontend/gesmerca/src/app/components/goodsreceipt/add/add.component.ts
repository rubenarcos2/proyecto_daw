import { DatePipe } from '@angular/common';
import { Component, OnDestroy, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { Subscription } from 'rxjs';
import { GoodsReceipt } from 'src/app/models/goodsreceipt';
import { GoodsReceiptProduct } from 'src/app/models/goodsreceiptproduct';
import { Product } from 'src/app/models/product';
import { Supplier } from 'src/app/models/supplier';
import { AuthService } from 'src/app/services/auth.service';
import { GoodsreceiptService } from 'src/app/services/goodsreceipt.service';
import { ProductService } from 'src/app/services/product.service';
import { SupplierService } from 'src/app/services/supplier.service';

@Component({
  selector: 'app-add',
  templateUrl: './add.component.html',
  styleUrls: ['./add.component.css'],
})
export class GoodsReceiptAddComponent implements OnInit, OnDestroy {
  goodsReceiptForm!: FormGroup;
  goodsReceiptProductForm!: FormGroup;
  dataForm!: FormData;
  dataProductForm!: FormData;
  isSubmitted: boolean = false;
  private _products?: Product[];
  private _goodsReceipt?: GoodsReceipt;
  private _suppliers?: Supplier[];
  private _goodsReceiptProducts?: GoodsReceiptProduct[];
  private subs: Subscription = new Subscription();
  private subs2: Subscription = new Subscription();
  private subs3: Subscription = new Subscription();
  private subs4: Subscription = new Subscription();
  private subs5: Subscription = new Subscription();
  private subs6: Subscription = new Subscription();
  private subs7: Subscription = new Subscription();

  constructor(
    private formBuilder: FormBuilder,
    private goodsReceiptService: GoodsreceiptService,
    private supplierService: SupplierService,
    private productService: ProductService,
    protected authService: AuthService,
    private toastr: ToastrService,
    private route: ActivatedRoute,
    private router: Router,
    private datePipe: DatePipe
  ) {}

  ngOnInit(): void {
    let id;
    this.dataForm = new FormData();
    this.dataProductForm = new FormData();
    this.router.routeReuseStrategy.shouldReuseRoute = () => false;
    this.router.onSameUrlNavigation = 'reload';

    this.route.params.subscribe(param => (id = parseInt(param['id'])));
    this.subs = this.supplierService.getAllNoPaginated().subscribe({
      next: result => {
        let res = JSON.parse(JSON.stringify(result));
        this._suppliers = res;
      },
      error: error => {
        this.toastr.error(error ? error : 'Operación no autorizada');
      },
    });
    this.subs2 = this.goodsReceiptService.getById(id).subscribe({
      next: result => {
        this._goodsReceipt = result;
        this.goodsReceiptForm = this.formBuilder.group({
          id: [this.goodsReceipt?.id],
          docnum: [this.goodsReceipt?.docnum, Validators.required],
          idsupplier: [this.goodsReceipt?.idsupplier, Validators.required],
          iduser: [this.goodsReceipt?.iduser, Validators.required],
          date: [
            this.goodsReceipt?.date,
            [Validators.required, Validators.pattern(/^(|[0-9])[0-9]\/[0-9][0-9]\/[0-9][0-9]$/)],
          ],
          time: [
            this.goodsReceipt?.time,
            [
              Validators.required,
              Validators.pattern(/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/),
            ],
          ],
        });

        this.goodsReceiptProductForm = this.formBuilder.group({
          idgoodsreceipt: [this.goodsReceipt?.id],
          idproduct: [null, Validators.required],
          quantity: [null, Validators.required],
          price: [null, Validators.required],
        });
      },
      error: error => {
        this.toastr.error(error ? error : 'Operación no autorizada');
      },
    });
    this.subs3 = this.goodsReceiptService.getProducts(id).subscribe({
      next: result => {
        let res = JSON.parse(JSON.stringify(result));
        this._goodsReceiptProducts = res;
        this._goodsReceiptProducts?.forEach(e => {
          this.subs4 = this.productService.getById(e.idproduct).subscribe({
            next: result => {
              let res = JSON.parse(JSON.stringify(result));
              e.nameproduct = res.name;
              this._products = this._products?.filter(el => el.id !== e.idproduct);
              if (this.products?.length == 0)
                document.getElementsByTagName('form')[1].setAttribute('hidden', 'true');
            },
            error: error => {
              this.toastr.error(error ? error : 'Operación no autorizada');
            },
          });
        });
      },
      error: error => {
        this.toastr.error(error ? error : 'Operación no autorizada');
      },
    });

    this.subs5 = this.productService.getAllNoPaginated().subscribe({
      next: result => {
        let res = JSON.parse(JSON.stringify(result));
        this._products = res;
        this._products = this._products?.filter(e => e.supplier == this.goodsReceipt?.idsupplier);
      },
      error: error => {
        this.toastr.error(error ? error : 'Operación no autorizada');
      },
    });
  }

  onSubmit() {
    this.isSubmitted = true;
    this.dataForm.append('id', this.goodsReceiptForm.get('id')?.value);
    this.dataForm.append('docnum', this.goodsReceiptForm.get('docnum')?.value);
    this.dataForm.append('idsupplier', this.goodsReceiptForm.get('idsupplier')?.value);
    this.dataForm.append('iduser', this.goodsReceiptForm.get('iduser')?.value);
    this.dataForm.append('date', this.changeFormatDate(this.goodsReceiptForm.get('date')?.value));
    this.dataForm.append('time', this.goodsReceiptForm.get('time')?.value);
    this.subs6 = this.goodsReceiptService
      .update(this.dataForm, this.goodsReceiptForm.get('id')?.value)
      .subscribe({
        next: result => {
          let res = JSON.parse(JSON.stringify(result));
          res.error ? this.toastr.error(res.error) : this.toastr.success(res.message);
          this.router.navigate(['/recepcion']);
        },
        error: error => {
          this.toastr.error(error.error ? error.error : 'No se puede conectar con el servidor');
        },
      });
    this.subs6.add(() => {
      this.isSubmitted = false;
    });
  }

  onSubmitProduct() {
    this.isSubmitted = true;
    this.dataProductForm.append(
      'idgoodsreceipt',
      this.goodsReceiptProductForm.get('idgoodsreceipt')?.value
    );
    this.dataProductForm.append(
      'idproduct',
      (document.getElementById('select-product') as HTMLSelectElement).value
    );
    this.dataProductForm.append('quantity', this.goodsReceiptProductForm.get('quantity')?.value);
    this.dataProductForm.append('price', this.goodsReceiptProductForm.get('price')?.value);
    this.subs7 = this.goodsReceiptService
      .addProduct(this.dataProductForm, this.goodsReceipt?.id)
      .subscribe({
        next: result => {
          let res = JSON.parse(JSON.stringify(result));
          res.error ? this.toastr.error(res.error) : this.toastr.success(res.message);
          this.router.navigate([`/recepcion/editar/${this.goodsReceipt?.id}`]);
        },
        error: error => {
          this.toastr.error(error.error ? error.error : 'No se puede conectar con el servidor');
        },
      });
    this.subs7.add(() => {
      this.isSubmitted = false;
    });
  }

  onChangeInput(event: any) {
    let input = event.target.id;
    this.isSubmitted = true;
    switch (input) {
      case 'inputDocNum':
        this.isSubmitted = this.goodsReceiptForm.get(input)?.value !== this.goodsReceipt?.docnum;
        break;
      case 'inputSupplier':
        this.isSubmitted =
          this.goodsReceiptForm.get(input)?.value !== this.goodsReceipt?.idsupplier;
        break;
      case 'inputUser':
        this.isSubmitted = this.goodsReceiptForm.get(input)?.value !== this.goodsReceipt?.iduser;
        break;
      case 'inputDate':
        this.isSubmitted = this.goodsReceiptForm.get(input)?.value !== this.goodsReceipt?.date;
        break;
      case 'inputTime':
        this.isSubmitted = this.goodsReceiptForm.get(input)?.value !== this.goodsReceipt?.time;
        break;
    }
  }

  onChangeCmbSupplier(event: any) {
    let sel = event.target as HTMLSelectElement;
    for (let i = 0; i < sel.options.length; i++) {
      if (Number(sel.options[i].value) == this.goodsReceipt?.idsupplier) {
        sel.options[i].selected = true;
      }
    }
  }

  changeFormatDate(date: string): any {
    let d = new Date(date);
    return this.datePipe.transform(d, 'yyyy/MM/dd');
  }

  deleteProduct(name: any, id: any) {
    if (
      window.confirm(
        '¿Seguro que desea borrar el producto albarán de recepción de mercancía ' + name + '?'
      )
    ) {
      let dataDeleteProdForm = new FormData();
      dataDeleteProdForm.append('idgoodsreceiptproduct', id);
      this.subs2 = this.goodsReceiptService.deleteProduct(dataDeleteProdForm, id).subscribe({
        next: result => {
          let msg = JSON.parse(JSON.stringify(result));
          this.toastr.success(msg.message);
          this.router.navigate([`/recepcion/editar/${this.goodsReceipt?.id}`]);
        },
        error: error => {
          this.toastr.error(error ? error : 'Operación no autorizada');
        },
      });
    }
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
    this.subs7.unsubscribe();
  }

  get goodsReceiptFormControls() {
    return this.goodsReceiptForm.controls;
  }

  get goodsReceiptProductFormControls() {
    return this.goodsReceiptProductForm.controls;
  }

  get goodsReceipt() {
    return this._goodsReceipt;
  }

  get goodsReceiptProducts() {
    return this._goodsReceiptProducts;
  }

  get products() {
    return this._products;
  }

  get suppliers() {
    return this._suppliers;
  }
}
