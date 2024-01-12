import { NgModule } from "@angular/core";
import { CommonModule } from "@angular/common";
import { FormsModule } from "@angular/forms";

import { SalesPromoComponent } from "./components/sales-promo/sales-promo.component";
import { NgSelectModule } from "@ng-select/ng-select";
import { SharedModule } from "src/app/shared/shared.module";
import { SaleTransactionComponent } from "./components/sale-transaction/sale-transaction.component";
import { DataTablesModule } from "angular-datatables";
import { SalesMenuComponent } from "./components/sales-menu/sales-menu.component";
import { SaleCustomerComponent } from "./components/sale-customer/sale-customer.component";
import { ModalDetailComponent } from "./components/modal-detail/modal-detail.component";
import { NgbModule } from "@ng-bootstrap/ng-bootstrap";

@NgModule({
  declarations: [
    SalesPromoComponent,
    SaleTransactionComponent,
    SalesMenuComponent,
    SaleCustomerComponent,
    ModalDetailComponent,
  ],
  imports: [
    FormsModule,
    NgSelectModule,
    CommonModule,
    SharedModule,
    DataTablesModule,
    NgbModule,
  ],
})
export class ReportModule {}
