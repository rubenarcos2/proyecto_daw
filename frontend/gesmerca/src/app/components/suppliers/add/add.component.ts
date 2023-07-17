import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { SupplierService } from 'src/app/services/supplier.service';

@Component({
  selector: 'app-add',
  templateUrl: './add.component.html',
  styleUrls: ['./add.component.css'],
})
export class SupplierAddComponent implements OnInit {
  protected supplierForm!: FormGroup;
  dataForm!: FormData;
  returnUrl!: string;
  isSubmitted: boolean = false;

  constructor(
    private formBuilder: FormBuilder,
    private supplierService: SupplierService,
    private toastr: ToastrService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.dataForm = new FormData();
    this.supplierForm = this.formBuilder.group({
      cif_nif: ['', Validators.required],
      name: ['', [Validators.required, Validators.minLength(3)]],
      address: ['', Validators.required],
      city: ['', Validators.required],
      phone: ['', Validators.required],
      email: [''],
      web: [''],
      notes: [''],
    });
    this.isSubmitted = true;
  }

  onSubmit() {
    this.isSubmitted = true;
    this.dataForm.append('id', this.supplierForm.get('id')?.value);
    this.dataForm.append('cif_nif', this.supplierForm.get('cif_nif')?.value);
    this.dataForm.append('name', this.supplierForm.get('name')?.value);
    this.dataForm.append('address', this.supplierForm.get('address')?.value);
    this.dataForm.append('city', this.supplierForm.get('city')?.value);
    this.dataForm.append('phone', this.supplierForm.get('phone')?.value);
    this.dataForm.append('email', this.supplierForm.get('email')?.value);
    this.dataForm.append('web', this.supplierForm.get('web')?.value);
    this.dataForm.append('notes', this.supplierForm.get('notes')?.value);
    this.supplierService
      .create(this.dataForm)
      .subscribe({
        next: result => {
          let res = JSON.parse(JSON.stringify(result));
          res.error ? this.toastr.error(res.error) : this.toastr.success(res.message);
          this.router.navigate([this.returnUrl || '/proveedores']);
        },
        error: error => {
          this.toastr.error(error ? error : 'No se puede conectar con el servidor');
        },
      })
      .add(() => {
        this.isSubmitted = false;
      });
  }

  get supplierFormControls() {
    return this.supplierForm.controls;
  }
}
