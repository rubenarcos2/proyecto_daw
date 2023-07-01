import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { ProductService } from 'src/app/services/product.service';

@Component({
  selector: 'app-add',
  templateUrl: './add.component.html',
  styleUrls: ['./add.component.css']
})
export class ProductAddComponent implements OnInit {

  protected productForm!: FormGroup;
  dataForm!: FormData;
  returnUrl!: string;
  isSubmitted: boolean = false;

  constructor(
    private formBuilder: FormBuilder,
    private productService: ProductService,
    private toastr: ToastrService,
    private router: Router
  ) { }

  ngOnInit(): void {
    this.dataForm = new FormData();
    this.productForm = this.formBuilder.group({
      name: ['', [Validators.required, Validators.minLength(3)]],
      description: ['', [Validators.required, Validators.minLength(3)]],
      image: [null],
      price: ['', Validators.required],
      stock: ['', Validators.required]
    });
    this.isSubmitted = true;
  }

  onSubmit() {
    this.isSubmitted = true;
    this.dataForm.append("name", this.productForm.get('name')?.value);
    this.dataForm.append("description", this.productForm.get('description')?.value);
    this.dataForm.append("price", this.productForm.get('price')?.value.replace(/,/g, '.'));
    this.dataForm.append("stock", this.productForm.get('stock')?.value);
    this.productService.create(this.dataForm).subscribe({
      next: (result) => {
        let res = JSON.parse(JSON.stringify(result));
        res.error ? this.toastr.error(res.error) : this.toastr.success(res.message);
        this.router.navigate([this.returnUrl || '/productos']);
      },
      error: (error) => {
        this.toastr.error(error ? error : "No se puede conectar con el servidor");
      }
    })
    .add(() => {
      this.isSubmitted = false;
    });
  }

  onChangeFile(file:any){
    this.dataForm.append("image", file.target.files[0], file.name);
    this.isSubmitted = true;
  }

  get productFormControls() {
    return this.productForm.controls;
  }
}
