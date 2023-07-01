import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { Product } from 'src/app/models/product';
import { ProductService } from 'src/app/services/product.service';

@Component({
  selector: 'app-edit',
  templateUrl: './edit.component.html',
  styleUrls: ['./edit.component.css'],
})
export class ProductEditComponent implements OnInit {
  productForm!: FormGroup;
  dataForm!: FormData;
  returnUrl!: string;
  isSubmitted: boolean = false;
  private _product?: Product;

  constructor(
    private formBuilder: FormBuilder,
    private productService: ProductService,
    private toastr: ToastrService,
    private route: ActivatedRoute,
    private router: Router
  ) {}

  ngOnInit(): void {
    let id;
    this.dataForm = new FormData();
    this.route.params.subscribe(param => (id = parseInt(param['id'])));
    this.productService.getById(id).subscribe({
      next: result => {
        this._product = result;
        this.productForm = this.formBuilder.group({
          id: [this.product?.id],
          name: [this.product?.name, [Validators.required, Validators.minLength(3)]],
          description: [this.product?.description, [Validators.required, Validators.minLength(3)]],
          image: [],
          price: [
            this.product?.price,
            [Validators.required, Validators.pattern('^[0-9]*[,.][0-9]*$')],
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

  onSubmit() {
    this.isSubmitted = true;
    this.dataForm.append('id', this.productForm.get('id')?.value);
    this.dataForm.append('name', this.productForm.get('name')?.value);
    this.dataForm.append('description', this.productForm.get('description')?.value);
    this.dataForm.append('price', this.productForm.get('price')?.value.replace(/,/g, '.'));
    if (this.dataForm.get('image') !== null)
      this.dataForm.append('image', this.productForm.get('image')?.value);
    this.dataForm.append('stock', this.productForm.get('stock')?.value);
    this.productService
      .update(this.dataForm)
      .subscribe({
        next: result => {
          let res = JSON.parse(JSON.stringify(result));
          res.error ? this.toastr.error(res.error) : this.toastr.success(res.message);
          this.router.navigate([this.returnUrl || '/productos']);
        },
        error: error => {
          this.toastr.error(error.error ? error.error : 'No se puede conectar con el servidor');
        },
      })
      .add(() => {
        this.isSubmitted = false;
      });
  }

  onChangeInput(event: any) {
    //TODO
    let input = event.target.id;
    this.isSubmitted = true;
    switch (input) {
      case 'inputName':
        this.isSubmitted = this.productForm.get(input)?.value !== this.product?.name;
        break;
      case 'inputDescription':
        this.isSubmitted = this.productForm.get(input)?.value !== this.product?.description;
        break;
      case 'inputPrice':
        this.isSubmitted = this.productForm.get(input)?.value !== this.product?.price;
        break;
      case 'inputStock':
        this.isSubmitted = this.productForm.get(input)?.value !== this.product?.stock;
        break;
    }
  }

  onChangeFile(file: any) {
    this.dataForm.append('image', file.target.files[0], file.name);
    this.isSubmitted = true;
  }

  onLoadImg(event: any) {
    event.srcElement.classList.remove('spinner-border');
  }

  get productFormControls() {
    return this.productForm.controls;
  }

  get product() {
    return this._product;
  }
}
