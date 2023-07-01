import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { AuthService } from 'src/app/services/auth.service';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent implements OnInit {

  registerForm!: FormGroup;
  isSubmitted: boolean = false;

  constructor(
    private formBuilder: FormBuilder,
    private authService: AuthService,
    private toastr: ToastrService,
    private router: Router
  ) { }

  ngOnInit(): void {
    this.registerForm = this.formBuilder.group({
      name: ['', Validators.required],
      email: ['', Validators.required],
      password: ['', [Validators.required, Validators.pattern(/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/)]]
    });
  }

  onSubmit() {
    this.isSubmitted = true;
    this.authService.register(this.registerForm.value).subscribe({
      next: (result) => {
        this.router.navigate(['/']).then(() => {
          let msg = JSON.parse(JSON.stringify(result));
          this.toastr.success(msg.message);
        });
      },
      error: (error) => {
        this.toastr.error(error ? error : "No se puede conectar con el servidor");
      }
    })
    .add(() => {
      this.isSubmitted = false;
    });
  }

  get registerFormControls() {
    return this.registerForm.controls;
  }
}
