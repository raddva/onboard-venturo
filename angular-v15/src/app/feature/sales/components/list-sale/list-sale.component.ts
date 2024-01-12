import { Component, OnInit } from "@angular/core";
import { NgbModal } from "@ng-bootstrap/ng-bootstrap";

import { CustomerService } from "src/app/feature/customer/services/customer.service";
import { ProductService } from "src/app/feature/product/product/services/product.service";
import { VoucherService } from "src/app/feature/promo/voucher/services/voucher.service";

@Component({
  selector: "app-list-sale",
  templateUrl: "./list-sale.component.html",
  styleUrls: ["./list-sale.component.scss"],
})
export class ListSaleComponent implements OnInit {
  showLoading: boolean;
  voucherId: number;
  showForm: boolean;
  products: any;
  customers: any;
  productId: any;
  titleModal: string;
  selectedProducts: any[] = [];

  selectedCustomer: any;
  filter: {
    name: "";
  };

  setDefault() {
    this.filter = {
      name: "",
    };
  }
  constructor(
    private voucherService: VoucherService,
    private customerService: CustomerService,
    private productService: ProductService,
    private modalService: NgbModal
  ) {}

  ngOnInit(): void {
    this.getCustomers();
    this.getProducts();
  }

  addToSelectedProducts(product: any): void {
    const index = this.selectedProducts.findIndex((p) => p.id == product.id);

    if (index == -1) {
      this.selectedProducts.push(product);

      console.log("Selected Products:", this.selectedProducts);
    }
  }

  formUpdate(modalId, product) {
    this.titleModal = "Edit menu: " + product.name;
    this.showForm = true;
    this.productId = product.id;
    this.modalService.open(modalId, {
      size: "xl",
      backdrop: "static",
    });
  }

  getProducts(name = "") {
    this.showLoading = true;
    this.productService.getProducts({ name: name }).subscribe(
      (res: any) => {
        this.products = res.data.list;
        this.showLoading = false;
      },
      (err) => {
        console.log(err);
      }
    );
  }

  reloadProducts() {
    this.showLoading = true;
    this.productService.getProducts(this.filter).subscribe(
      (res: any) => {
        this.products = res.data.list;
        this.showLoading = false;
      },
      (err) => {
        console.log(err);
      }
    );
  }

  getCustomers(name = "") {
    this.showLoading = true;
    this.customerService.getCustomers({ name: name }).subscribe(
      (res: any) => {
        this.customers = res.data.list;
        this.showLoading = false;
        if (this.customers && this.customers.length > 0) {
          this.selectedCustomer = this.customers[0];
        }
      },
      (err) => {
        console.log(err);
      }
    );
  }
}
