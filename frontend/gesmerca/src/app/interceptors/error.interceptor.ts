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
          //Local logout if 400 expired session response returned from api
          if (error?.status === 400) {
            localStorage.removeItem('authUser');
            location.replace('/login');
            return throwError(() => 'Usuario no autorizado');
          }
          //Auto logout if 401 Unauthorized or 403 Forbidden response returned from api
          if (error?.status === 401 || error?.status === 403) {
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
            return throwError(() => 'No se ha podido procesar la petición');
          }
        }
        return throwError(() => error?.error?.error);
      })
    );
  }
}
