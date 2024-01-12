import { CommonModule } from "@angular/common";
import { NgModule } from "@angular/core";
import { FormsModule } from "@angular/forms";
import { CKEditorModule } from "@ckeditor/ckeditor5-angular";
import { NgbModule } from "@ng-bootstrap/ng-bootstrap";
import { NgSelectModule } from "@ng-select/ng-select";
import { DataTablesModule } from "angular-datatables";
import { DndModule } from "ngx-drag-drop";
import { CoreModule } from "src/app/core/core.module";
import { SharedModule } from "src/app/shared/shared.module";
import { ListSaleComponent } from "./components/list-sale/list-sale.component";
import { FormSaleComponent } from "./components/form-sale/form-sale.component";
import { CustomerModule } from "../customer/customer.module";
import { ProductModule } from "../product/product.module";

@NgModule({
  declarations: [ListSaleComponent, FormSaleComponent],
  imports: [
    CommonModule,
    FormsModule,
    NgbModule,
    DataTablesModule,
    SharedModule,
    CoreModule,
    CKEditorModule,
    DndModule,
    NgSelectModule,
    CustomerModule,
    ProductModule,
  ],
})
export class SalesModule {}
