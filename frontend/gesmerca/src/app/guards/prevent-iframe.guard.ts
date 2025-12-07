import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Router, RouterStateSnapshot } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { ToastrService } from 'ngx-toastr';

@Injectable({
  providedIn: 'root',
})
export class PreventIframeGuard {
  constructor(
    private authService: AuthService,
    private router: Router,
    private toastr: ToastrService
  ) {}

  /*
   * Deny access from iframe on html
   *
   */
  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
    if (window.top != window.self) {
      window.top?.location.replace(window.location.href);
      return false;
    }
    return true;
  }
}
