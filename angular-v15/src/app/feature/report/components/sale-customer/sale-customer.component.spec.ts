import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SaleCustomerComponent } from './sale-customer.component';

describe('SaleCustomerComponent', () => {
  let component: SaleCustomerComponent;
  let fixture: ComponentFixture<SaleCustomerComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SaleCustomerComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SaleCustomerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
