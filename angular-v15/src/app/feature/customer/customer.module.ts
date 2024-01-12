import { NgModule } from "@angular/core";
import { CommonModule } from "@angular/common";
import { FormCustomerComponent } from "./components/form-customer/form-customer.component";
import { ListCustomerComponent } from "./components/list-customer/list-customer.component";
import { FormsModule } from "@angular/forms";
import { NgbModule } from "@ng-bootstrap/ng-bootstrap";
import { SharedModule } from "src/app/shared/shared.module";
import { DataTablesModule } from "angular-datatables";

@NgModule({
  declarations: [FormCustomerComponent, ListCustomerComponent],
  imports: [
    CommonModule,
    FormsModule,
    NgbModule,
    DataTablesModule,
    SharedModule,
  ],
  exports: [FormCustomerComponent],
})
export class CustomerModule {}
