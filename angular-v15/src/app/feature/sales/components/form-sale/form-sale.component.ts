import {
  Component,
  EventEmitter,
  Input,
  OnInit,
  Output,
  SimpleChanges,
} from "@angular/core";
import { NgbModal } from "@ng-bootstrap/ng-bootstrap";
import { DndDropEvent } from "ngx-drag-drop";
import { LandaService } from "src/app/core/services/landa.service";
import { CustomerService } from "src/app/feature/customer/services/customer.service";
import { ProductService } from "src/app/feature/product/product/services/product.service";
import { DiscountService } from "src/app/feature/promo/discount/services/discount.service";
import { VoucherService } from "src/app/feature/promo/voucher/services/voucher.service";
import { SalesService } from "src/app/feature/report/services/sales.service";

@Component({
  selector: "app-form-sale",
  templateUrl: "./form-sale.component.html",
  styleUrls: ["./form-sale.component.scss"],
})
export class FormSaleComponent implements OnInit {
  @Input() selectedCustomer: any;
  @Input() selectedProducts: any[] = [];
  @Output() afterSave = new EventEmitter<boolean>();

  currentDate = new Date();
  year = this.currentDate.getFullYear();
  month = String(this.currentDate.getMonth() + 1).padStart(2, "0");
  day = String(this.currentDate.getDate()).padStart(2, "0");
  formattedDate = `${this.year}-${this.month}-${this.day}`;

  totalDiscPercent = 0;

  discountId: number | null = null;
  voucherId: number | null = null;
  voucherNominal: number | null = null;
  discountNominal: number | null = null;
  formDate: string;

  discount_nominal: number | null = null;
  voucher_nominal: number | null = null;

  customer: any;
  discounts: any;
  vouchers: any;
  formModel: {
    id: number;
    customer_id: string;
    voucher_id: number;
    voucher_nominal: number;
    discount_id: number;
    date: string;
    detail: any;
  };

  titleModal: string;
  customerId: any;
  constructor(
    private voucherService: VoucherService,
    private customerService: CustomerService,
    private productService: ProductService,
    private modalService: NgbModal,
    private discountService: DiscountService,
    private landaService: LandaService,
    private saleService: SalesService
  ) {}

  ngOnInit(): void {
    this.formDate = this.formattedDate;
  }

  ngOnChanges(changes: SimpleChanges) {
    this.resetForm();

    if (changes["selectedCustomer"]) {
      this.selectedCustomer = changes["selectedCustomer"].currentValue;
      this.totalDiscPercent = 0;
      if (this.selectedCustomer) {
        this.customerService.getCustomerById(this.selectedCustomer).subscribe(
          (res: any) => {
            this.customer = res.data;
            this.formModel.customer_id = this.selectedCustomer;
          },
          (err) => {
            console.log(err);
          }
        );

        this.discountService
          .getDiscountByCustomer(this.selectedCustomer)
          .subscribe(
            (res: any) => {
              this.discounts = res;
              this.discountId =
                this.discounts.length > 0 ? this.discounts[0].id : null;
              this.formModel.discount_id = this.discountId;
              for (const disc of this.discounts) {
                this.totalDiscPercent += disc.promo.nominal_percentage;
              }
            },
            (err) => {
              console.log(err);
            }
          );
        this.voucherService
          .getVoucherByCustomer(this.selectedCustomer)
          .subscribe(
            (res: any) => {
              const vouchers = res;
              console.log(vouchers);
              this.voucherId = vouchers.length > 0 ? vouchers[0].id : null;
              this.formModel.voucher_id = this.voucherId;
              if (this.voucherId) {
                this.formModel.voucher_nominal =
                  vouchers[0].promo.nominal_rupiah;
              }
            },
            (err) => {
              console.log(err);
            }
          );
      }
    }
  }

  updateCustomer(modalId, customer) {
    this.titleModal = "Edit customer: " + customer.name;
    this.customerId = customer.id;
    this.modalService.open(modalId, { size: "lg", backdrop: "static" });
  }

  isObject(obj: any): boolean {
    return obj != null && typeof obj == "object" && Object.keys(obj).length > 0;
  }

  incrementQuantity(product: any): void {
    const existingProductIndex = this.selectedProducts.findIndex(
      (p) => p.id == product.id
    );

    if (existingProductIndex != -1) {
      this.selectedProducts[existingProductIndex].quantity =
        (this.selectedProducts[existingProductIndex].quantity || 1) + 1;
    } else {
      product.quantity = 1;
      this.selectedProducts.push(product);
    }
  }

  decrementQuantity(product: any): void {
    const existingProductIndex = this.selectedProducts.findIndex(
      (p) => p.id == product.id
    );

    if (existingProductIndex != -1) {
      const newQuantity = Math.max(
        (this.selectedProducts[existingProductIndex].quantity || 1) - 1,
        0
      );

      if (newQuantity == 0) {
        this.selectedProducts.splice(existingProductIndex, 1);
      } else {
        this.selectedProducts[existingProductIndex].quantity = newQuantity;
      }
    }
  }

  resetForm() {
    this.formModel = {
      id: 0,
      customer_id: this.customer ? this.customer.id : "",
      voucher_id: this.voucherId || 0,
      voucher_nominal: this.voucher_nominal || 0,
      discount_id: this.discountId || 0,
      date: this.formDate || this.formattedDate,
      detail: [],
    };
  }

  resetFormAfterSave() {
    this.selectedCustomer = null;
    this.selectedProducts = [];
    this.totalDiscPercent = 0;
    this.discountId = null;
    this.voucherId = null;
    this.voucherNominal = null;
    this.discountNominal = null;
    this.formDate = this.formattedDate;
    this.discount_nominal = null;
    this.voucher_nominal = null;
    this.customer = null;
    this.discounts = null;
    this.vouchers = null;

    this.formModel = {
      id: 0,
      customer_id: "",
      voucher_id: 0,
      voucher_nominal: 0,
      discount_id: 0,
      date: this.formDate,
      detail: [],
    };
  }

  addDetail() {
    this.formModel.detail = [];

    for (const product of this.selectedProducts) {
      let val = {
        is_added: true,
        product_id: product.id,
        product_detail_id: 0,
        total_item: product.quantity || 1,
        price: product.price || 0,
        discount_nominal: this.calculateDiscNominal(product),
      };
      this.formModel.detail.push(val);
    }
  }

  subtotal(): number {
    let subtotal = 0;
    for (const product of this.selectedProducts) {
      subtotal += (product.price || 0) * (product.quantity || 1);
    }
    return subtotal;
  }

  tax(subtotal: number): number {
    const taxRate = 0.11;
    return subtotal * taxRate;
  }

  discNominal(subtotal: number): number {
    const tax = this.tax(subtotal);
    const preDiscountTotal = subtotal + tax;
    const totalDiscount = (100 - this.totalDiscPercent) / 100;
    const discountedTotal = preDiscountTotal * totalDiscount;
    const nominalDiscount = preDiscountTotal - discountedTotal;
    return nominalDiscount;
  }

  total(): number {
    const subtotal = this.subtotal();
    const tax = this.tax(subtotal);
    const discountNominal = this.discNominal(subtotal);
    const total = subtotal + tax - discountNominal;
    return total;
  }

  onDragged(event: any, list: any[], index: number) {
    list.splice(index, 1);
  }

  onDrop(event: DndDropEvent, list: any[]) {
    if (event.dropEffect == "move") {
      let index = event.index;

      if (typeof index == "undefined") {
        index = list.length;
      }
      list.splice(index, 0, event.data);
    }
  }

  insert() {
    console.log(this.formModel);
    this.saleService.createTransaction(this.formModel).subscribe(
      (res: any) => {
        this.landaService.alertSuccess("Berhasil", res.message);
        this.resetFormAfterSave();
        this.afterSave.emit();
      },
      (err) => {
        this.landaService.alertError("Mohon Maaf", err.error.errors);
      }
    );
  }

  calculateDiscNominal(product: any): number {
    let totalProductDiscount = 0;

    for (const disc of this.discounts) {
      const discountPercentage = disc.promo.nominal_percentage;
      const productDiscount = (discountPercentage / 100) * (product.price || 0);
      totalProductDiscount += productDiscount;
      break;
    }
    return totalProductDiscount;
  }
}
