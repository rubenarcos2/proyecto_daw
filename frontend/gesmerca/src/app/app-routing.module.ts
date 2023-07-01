import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './guards/auth.guard';
//Login, profile, register and not found page
import { LoginComponent } from './components/auth/login/login.component';
import { ProfileComponent } from './components/auth/profile/profile.component';
import { RegisterComponent } from './components/auth/register/register.component';
import { NotFoundComponent } from './components/not-found/not-found.component';
//Home
import { HomeComponent } from './components/home/home.component';
//Products
import { ProductListComponent } from './components/products/list/list.component';
import { ProductEditComponent } from './components/products/edit/edit.component';
import { ProductAddComponent } from './components/products/add/add.component';
//Configurations of user and general
import { ConfigListComponent } from './components/config/list/list.component';
import { ConfigGeneralListComponent } from './components/config/general-list/list.component';
import { ConfigGeneralEditComponent } from './components/config/edit/edit.component';
//Roles and permissions
import { RoleListComponent } from './components/role/list/list.component';
import { PermissionsListComponent } from './components/permissions/list/list.component';

const routes: Routes = [
  {
    path: '',
    component: HomeComponent
  },
  {
    path: 'login',
    component: LoginComponent
  },
  {
    path: 'registro',
    component: RegisterComponent
  },
  {
    path: 'perfil',
    component: ProfileComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'productos',
    component: ProductListComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'productos/nuevo',
    component: ProductAddComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'productos/editar/:id',
    component: ProductEditComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'config',
    component: ConfigListComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'config/general',
    component: ConfigGeneralListComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'config/general/editar/:id',
    component: ConfigGeneralEditComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'roles',
    component: RoleListComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'permisos',
    component: PermissionsListComponent,
    canActivate: [AuthGuard]
  },
  {
    path: '**',
    component: NotFoundComponent,
  }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
