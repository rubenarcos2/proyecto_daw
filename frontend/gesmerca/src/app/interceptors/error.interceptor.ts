import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import { catchError, Observable, throwError } from 'rxjs';
import { AuthService } from '../services/auth.service';

@Injectable()
export class ErrorInterceptor implements HttpInterceptor {
  private isLoggedOut: boolean = false;

  constructor(private authService: AuthService) {}

  intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    return next.handle(request).pipe(
      catchError(error => {
        if (!this.isLoggedOut) {
          this.isLoggedOut = true;
          if (error?.status === 401 || error?.status === 400) {
            this.authService.logout().subscribe({
              next: () => {
                location.replace('/login');
              },
              error: error => {
                localStorage.removeItem('authUser');
                return throwError(() => error?.error?.error);
              },
            });
          }
          if (error?.status === 422) {
            return throwError(() => error?.error?.error);
          }
        }
        return throwError(() => error?.error?.error);
      })
    );
  }
}
