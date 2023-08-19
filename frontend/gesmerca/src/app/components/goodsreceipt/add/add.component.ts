import { Component, OnDestroy, OnInit } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { Subscription } from 'rxjs';
import { Supplier } from 'src/app/models/supplier';
import { User } from 'src/app/models/user';
import { AuthService } from 'src/app/services/auth.service';
import { ProductService } from 'src/app/services/product.service';
import { SupplierService } from 'src/app/services/supplier.service';
import { DatePipe } from '@angular/common';
import { GoodsReceiptProduct } from 'src/app/models/goodsreceiptproduct';
import { Product } from 'src/app/models/product';
import { GoodsReceipt } from 'src/app/models/goodsreceipt';
import { GoodsreceiptService } from 'src/app/services/goodsreceipt.service';

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
  isLoaded: boolean = false;
  today = new Date().toJSON().split('T')[0];

  private _user?: User;
  private _suppliers?: Supplier[];
  private _products?: Product[];
  private _goodsReceipt?: GoodsReceipt;
  private _goodsReceiptProducts?: GoodsReceiptProduct[] = [];

  private subs: Subscription = new Subscription();
  private subs2: Subscription = new Subscription();
  private subs3: Subscription = new Subscription();
  private subs4: Subscription = new Subscription();
  private subs5: Subscription = new Subscription();

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
    this.dataForm = new FormData();
    this.router.routeReuseStrategy.shouldReuseRoute = () => false;
    this.router.onSameUrlNavigation = 'reload';

    this._user = JSON.parse(JSON.stringify(this.authService.getAuthUser())).user;

    this.goodsReceiptForm = this.formBuilder.group({
      id: [null],
      docnum: [null, Validators.required],
      idsupplier: [null, Validators.required],
      suppliername: [null],
      iduser: [this._user?.id, Validators.required],
      username: [this._user?.name],
      date: [
        this.datePipe.transform(new Date(), 'YYYY-MM-dd'),
        [this.dateValidator, Validators.required],
      ],
      time: [
        this.datePipe.transform(new Date(), 'HH:mm:ss'),
        [Validators.required, Validators.pattern(/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/)],
      ],
    });

    this.goodsReceiptProductForm = this.formBuilder.group({
      idgoodsreceipt: [null],
      idproduct: [null, Validators.required],
      quantity: [null, Validators.required],
      price: [null, Validators.required],
    });

    this.subs = this.supplierService.getAllNoPaginated().subscribe({
      next: result => {
        let res = JSON.parse(JSON.stringify(result));
        this._suppliers = res;
        this.isLoaded = true;
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
    this.subs2 = this.goodsReceiptService.create(this.dataForm).subscribe({
      next: result => {
        let res = JSON.parse(JSON.stringify(result));
        res.error ? this.toastr.error(res.error) : this.toastr.success(res.message);
        //Add each product from goodsReceiptProducts to the new goodsReceipt
        this.goodsReceiptProducts?.forEach(e => {
          this.dataProductForm = new FormData();
          this.dataProductForm.append('idgoodsreceipt', res.id);
          this.dataProductForm.append('idproduct', String(e.idproduct));
          this.dataProductForm.append('nameproduct', String(e.nameproduct));
          this.dataProductForm.append('price', String(e.price));
          this.dataProductForm.append('quantity', String(e.quantity));

          this.subs3 = this.goodsReceiptService.addProduct(this.dataProductForm, res.id).subscribe({
            next: result => {
              let res2 = JSON.parse(JSON.stringify(result));
              res2.error ? this.toastr.error(res2.error) : this.toastr.success(res2.message);
            },
            error: error => {
              this.toastr.error(error.error ? error.error : 'No se puede conectar con el servidor');
            },
          });
        });
        //this.router.navigate(['/recepcion']);
      },
      error: error => {
        this.toastr.error(error.error ? error.error : 'No se puede conectar con el servidor');
      },
    });
    this.subs2.add(() => {
      this.isSubmitted = false;
    });
  }

  onSubmitProduct() {
    this.isSubmitted = true;
    this.dataProductForm = new FormData();
    this.dataProductForm.append(
      'idgoodsreceipt',
      this.goodsReceiptProductForm.get('idgoodsreceipt')?.value
    );
    this.dataProductForm.append(
      'idproduct',
      (document.getElementById('select-product') as HTMLSelectElement).value
    );
    this.dataProductForm.append(
      'nameproduct',
      (document.getElementById('select-product') as HTMLSelectElement).options[
        (document.getElementById('select-product') as HTMLSelectElement).selectedIndex
      ].text
    );
    this.goodsReceiptProductForm.get('quantity')?.value == null
      ? this.dataProductForm.append('quantity', '0')
      : this.dataProductForm.append(
          'quantity',
          this.goodsReceiptProductForm.get('quantity')?.value
        );
    this.goodsReceiptProductForm.get('price')?.value != null
      ? this.dataProductForm.append(
          'price',
          String(this.goodsReceiptProductForm.get('price')?.value).replace(/,/g, '.')
        )
      : this.dataProductForm.append('price', '0');

    this._goodsReceiptProducts?.push({
      idproduct: Number(this.dataProductForm.get('idproduct')),
      nameproduct: String(this.dataProductForm.get('nameproduct')),
      quantity: Number(this.dataProductForm.get('quantity')),
      price: Number(this.dataProductForm.get('price')),
    });

    this._products = this._products?.filter(
      e => e.id !== Number(this.dataProductForm.get('idproduct'))
    );

    if (this.products?.length == 0)
      document.getElementsByTagName('form')[1]?.setAttribute('hidden', 'true');
  }

  onChangeSuppliers(event: any) {
    let input = event.target.id;
    let index = (document.getElementById(input) as HTMLSelectElement).value;
    this._products = undefined;
    this.subs4 = this.productService.getAllNoPaginated().subscribe({
      next: result => {
        let res = JSON.parse(JSON.stringify(result));
        this._products = res;
        this._products = this._products?.filter(e => e.supplier == index);
        if (this.products?.length == 0)
          document.getElementsByTagName('form')[1]?.setAttribute('hidden', 'true');
        this.isLoaded = true;
      },
      error: error => {
        this.toastr.error(error ? error : 'Operación no autorizada');
      },
    });

    this._goodsReceiptProducts = [];
  }

  onChangeInput(event: any) {
    let input = event.target.id;
    this.isSubmitted = true;
    switch (input) {
      case 'inputDocNum':
        this.isSubmitted = this.goodsReceiptForm.get('docnum')?.value !== this.goodsReceipt?.docnum;
        break;
      case 'inputDate':
        this.isSubmitted = this.goodsReceiptForm.get('date')?.value !== this.goodsReceipt?.date;
        break;
      case 'inputTime':
        this.isSubmitted = this.goodsReceiptForm.get('time')?.value !== this.goodsReceipt?.time;
        break;
    }
  }

  dateValidator(control: FormControl): { [key: string]: any } | null {
    if (control.value) {
      let date = control.value;
      let today = new Date().toJSON().split('T')[0];

      if (new Date(date) > new Date(today)) {
        return { invalidDate: true };
      }
    }
    return null;
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
      this._goodsReceiptProducts = this._goodsReceiptProducts?.filter(e => e.idproduct != id);
      this._products = undefined;
      let supplier = (document.getElementById('select-supplier') as HTMLSelectElement).value;
      this.subs5 = this.productService.getAllNoPaginated().subscribe({
        next: result => {
          let res = JSON.parse(JSON.stringify(result));
          this._products = res;
          this._products = this._products?.filter(e => e.supplier == supplier);
          this._goodsReceiptProducts?.forEach(element => {
            this._products = this._products?.filter(e => e.id !== element.idproduct);
          });
          if (this.goodsReceiptProducts?.length == 0) this.isSubmitted = false;
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
