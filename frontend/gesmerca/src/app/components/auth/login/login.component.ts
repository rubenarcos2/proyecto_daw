import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AuthService } from 'src/app/services/auth.service';
import { ToastrService } from 'ngx-toastr';
import { ActivatedRoute, Router } from '@angular/router';
import { ConfigService } from 'src/app/services/config.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
})
export class LoginComponent implements OnInit {
  loginForm!: FormGroup;
  returnUrl!: string;
  isSubmitted: boolean = false;

  constructor(
    private formBuilder: FormBuilder,
    private authService: AuthService,
    private configService: ConfigService,
    private toastr: ToastrService,
    private route: ActivatedRoute,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.loginForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
    });
    this.returnUrl = this.route.snapshot.queryParams['returnUrl'];
  }

  onSubmit() {
    this.isSubmitted = true;
    this.authService
      .login(this.loginForm.value)
      .subscribe({
        next: result => {
          let res = JSON.parse(JSON.stringify(result));
          this.configService.getAllConfigsOfUser(res.user.id).subscribe();
          this.authService.profile().subscribe();
          this.toastr.info('Bienvenido ' + res.user.name);
          this.router.navigate([this.returnUrl || '/perfil']);
        },
        error: error => {
          this.toastr.error(error ? error : 'No se puede conectar con el servidor');
        },
      })
      .add(() => {
        this.isSubmitted = false;
      });
  }

  get loginFormControls() {
    return this.loginForm.controls;
  }
}
