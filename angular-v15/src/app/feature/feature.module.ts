import { NgModule } from "@angular/core";
import { CommonModule } from "@angular/common";
import { FormsModule, ReactiveFormsModule } from "@angular/forms";
import { NgbAlertModule, NgbModule } from "@ng-bootstrap/ng-bootstrap";
import {
  PERFECT_SCROLLBAR_CONFIG,
  PerfectScrollbarModule,
  PerfectScrollbarConfigInterface,
} from "ngx-perfect-scrollbar";

import { FeatureRoutingModule } from "./feature-routing.module";
import { DashboardComponent } from "./dashboard/dashboard.component";
import { UserModule } from "./user/user.module";
import { ProductModule } from "./product/product.module";
import { CustomerModule } from "./customer/customer.module";
import { PromoModule } from "./promo/promo.module";
import { SalesModule } from "./sales/sales.module";
import { ReportModule } from "./report/report.module";
import { ChartsModule } from "ng2-charts";
import { SharedModule } from "../shared/shared.module";
import { NgSelectModule } from "@ng-select/ng-select";

const DEFAULT_PERFECT_SCROLLBAR_CONFIG: PerfectScrollbarConfigInterface = {
  suppressScrollX: true,
  wheelSpeed: 0.3,
};

@NgModule({
  declarations: [DashboardComponent],
  imports: [
    ReactiveFormsModule,
    NgbAlertModule,
    CommonModule,
    FeatureRoutingModule,
    PerfectScrollbarModule,
    UserModule,
    ProductModule,
    CustomerModule,
    PromoModule,
    SalesModule,
    ReportModule,
    ChartsModule,
    SharedModule,
    NgSelectModule,
    FormsModule,
    NgbModule,
  ],
  providers: [
    {
      provide: PERFECT_SCROLLBAR_CONFIG,
      useValue: DEFAULT_PERFECT_SCROLLBAR_CONFIG,
    },
  ],
})
export class FeatureModule {}
