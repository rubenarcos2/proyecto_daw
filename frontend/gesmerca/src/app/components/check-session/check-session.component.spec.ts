import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CheckSessionComponent } from './check-session.component';

describe('CheckSessionComponent', () => {
  let component: CheckSessionComponent;
  let fixture: ComponentFixture<CheckSessionComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ CheckSessionComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CheckSessionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
