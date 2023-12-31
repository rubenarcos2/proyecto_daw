import { Component, HostListener, OnDestroy, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { Subscription } from 'rxjs';
import { Product } from 'src/app/models/product';
import { AuthService } from 'src/app/services/auth.service';
import { ProductService } from 'src/app/services/product.service';

@Component({
  selector: 'app-edit',
  templateUrl: './edit.component.html',
  styleUrls: ['./edit.component.css'],
})
export class ProductEditComponent implements OnInit, OnDestroy {
  productForm!: FormGroup;
  dataForm!: FormData;
  returnUrl!: string;
  isFormUpdating: boolean = false;
  private _product?: Product;
  private subs: Subscription = new Subscription();
  private subs2: Subscription = new Subscription();
  private subs3: Subscription = new Subscription();

  constructor(
    private formBuilder: FormBuilder,
    private productService: ProductService,
    protected authService: AuthService,
    private toastr: ToastrService,
    private route: ActivatedRoute,
    private router: Router
  ) {}

  /**
   * This function start on event page
   *
   */
  ngOnInit(): void {
    let id;
    this.dataForm = new FormData();
    this.subs = this.route.params.subscribe(param => (id = parseInt(param['id'])));

    //Get all products of backend
    this.subs2 = this.productService.getById(id).subscribe({
      next: result => {
        this._product = result;
        this.productForm = this.formBuilder.group({
          id: [this.product?.id],
          name: [this.product?.name, [Validators.required, Validators.minLength(3)]],
          description: [
            this.product?.description,
            [Validators.required, Validators.minLength(3), Validators.maxLength(255)],
          ],
          supplier: [this.product?.supplierName],
          image: [null, Validators.pattern(/[^\s]+(.*?).(jpg|jpeg|png|gif|JPG|JPEG|PNG|GIF)$/)],
          price: [
            this.product?.price,
            [Validators.required, Validators.pattern(/^(\d+)(,\d{1,2}|\.\d{1,2})?$/)],
          ],
          priceMin: [
            this.product?.priceMin == 0
              ? 'Sin histórico'
              : this.product?.priceMin?.toString().replace('.', ','),
          ],
          priceMax: [
            this.product?.priceMax == 0
              ? 'Sin histórico'
              : this.product?.priceMax?.toString().replace('.', ','),
          ],
          priceAvg: [
            this.product?.priceAvg == 0
              ? 'Sin histórico'
              : this.product?.priceAvg?.toString().replace('.', ','),
          ],
          stock: [this.product?.stock, [Validators.required, Validators.pattern('^[0-9]*$')]],
        });
        this.returnUrl = this.route.snapshot.queryParams['returnUrl'];
      },
      error: error => {
        this.toastr.error(error ? error : 'Operación no autorizada');
      },
    });
  }

  /**
   * This function execute on form submit
   *
   * Send form data to backend and update product data
   *
   */
  onSubmit() {
    this.dataForm.append('id', this.productForm.get('id')?.value);
    this.dataForm.append('name', this.productForm.get('name')?.value);
    this.dataForm.append('description', this.productForm.get('description')?.value);
    this.dataForm.append('supplier', this.product?.supplier?.toString()!);
    this.dataForm.append(
      'price',
      this.productForm.get('price')?.value.toString().replace(/,/g, '.')
    );
    if (this.dataForm.get('image') !== null)
      this.dataForm.append('image', this.productForm.get('image')?.value);
    this.dataForm.append('stock', this.productForm.get('stock')?.value);

    //Modify product data to backend
    this.subs3 = this.productService.update(this.dataForm).subscribe({
      next: result => {
        let res = JSON.parse(JSON.stringify(result));
        this.isFormUpdating = false;
        this.router.navigate([this.returnUrl || '/productos']);
        this.toastr.success(res.message);
      },
      error: error => {
        this.toastr.error(error ? error : 'No se puede conectar con el servidor');
      },
    });
  }

  /**
   * This function execute on change event input
   *
   * Detect if input value is changed and set submited value on true change
   *
   * @param  {Event} event The event change input
   */
  onChangeInput(event: any) {
    let input = event.target.id;
    switch (input) {
      case 'inputName':
        this.isFormUpdating = event.target.value != this.product?.name;
        break;
      case 'inputDescription':
        this.isFormUpdating = event.target.value != this.product?.description;
        break;
      case 'inputPrice':
        this.isFormUpdating = event.target.value != this.product?.price;
        break;
      case 'inputStock':
        this.isFormUpdating = event.target.value != this.product?.stock;
        break;
    }
  }

  /**
   * On event input file append a new image
   *
   * @param  {any} file The input file
   */
  onChangeFile(file: any) {
    if (file.target.files[0] !== undefined) {
      this.dataForm.append('image', file.target.files[0], file.name);
    }
    this.isFormUpdating = file.target.files[0] !== undefined;
  }

  /**
   * When load image remove spinner
   */
  onLoadImg(event: any) {
    event.srcElement.classList.remove('spinner-border');
  }

  /**
   * This function start on refresh or close window/tab navigator
   *
   * Detect if there are changes without save
   *
   * More info about behaviour: https://developer.mozilla.org/en-US/docs/Web/API/Window/beforeunload_event
   */
  @HostListener('window:beforeunload', ['$event'])
  handleClose(e: BeforeUnloadEvent): void {
    //e.preventDefault();
    if (this.isFormUpdating) e.returnValue = true;
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

  get productFormControls() {
    return this.productForm.controls;
  }

  get product() {
    return this._product;
  }
}
