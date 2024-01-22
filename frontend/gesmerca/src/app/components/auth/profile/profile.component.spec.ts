import { ComponentFixture, TestBed } from '@angular/core/testing';
import { HttpClient, HttpHandler } from '@angular/common/http';
import { ToastrModule } from 'ngx-toastr';
import { ProfileComponent } from './profile.component';
import TokenUtils from 'src/app/utils/tokenUtils';

describe('ProfileComponent', () => {
  let component: ProfileComponent;
  let fixture: ComponentFixture<ProfileComponent>;
  window.sessionStorage.setItem(
    'authUser',
    '{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL3Zwcy5yYXJjb3MuY29tOjEwNDQ5L2FwaS9hdXRoL2xvZ2luIiwiaWF0IjoxNzA1OTI3Njk4LCJleHAiOjE3MDU5MzEyOTgsIm5iZiI6MTcwNTkyNzY5OCwianRpIjoiYk1VdDVWejYxOGFUcHpPTCIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.pqrdiddqpUjTc7PX34ilp99uQMf_sF10bmu4M8wRCHc","token_type":"bearer","expires_in":3600,"user":{"id":1,"name":"Administrador","email":"admin@admin.com","email_verified_at":null,"created_at":"2023-12-14T20:21:33.000000Z","updated_at":"2023-12-14T20:21:33.000000Z"}}'
  );

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [
        ToastrModule.forRoot({
          positionClass: 'toast-top-right',
        }),
      ],
      declarations: [ProfileComponent],
      providers: [HttpClient, HttpHandler, TokenUtils],
    }).compileComponents();

    fixture = TestBed.createComponent(ProfileComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
