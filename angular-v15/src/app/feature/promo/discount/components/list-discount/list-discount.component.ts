import { Component, OnInit, ViewEncapsulation } from "@angular/core";
import { CustomerService } from "src/app/feature/customer/services/customer.service";
import { DiscountService } from "../../services/discount.service";
import { PromoService } from "../../../promo/services/promo.service";
import { NgbModal } from "@ng-bootstrap/ng-bootstrap";
@Component({
  selector: "app-list-discount",
  templateUrl: "./list-discount.component.html",
  styleUrls: ["./list-discount.component.scss"],
  encapsulation: ViewEncapsulation.None,
})
export class ListDiscountComponent implements OnInit {
  discounts: any;
  promo: any;
  showLoading: boolean;
  listCustomers: any;
  selectedCustomers: any;

  titleForm: string;
  discountId: number;
  showForm: boolean;
  filter: {
    id: any;
  };
  titleModal: string;
  customerId: any;

  constructor(
    private discountService: DiscountService,
    private customerService: CustomerService,
    private promoService: PromoService,
    private modalService: NgbModal
  ) {}

  ngOnInit(): void {
    this.showForm = false;
    this.setDefaultFilter();
    this.getCustomers();
    this.getDiscountsPromo();
    this.getDiscounts();
  }

  setDefaultFilter() {
    this.filter = {
      id: "",
    };
  }

  toggleDiscountStatus(customerId: number, promoId: number): void {
    const existingDiscount = this.discounts.find(
      (d) => d.customer_id == customerId && d.promo_id == promoId
    );

    if (existingDiscount) {
      const newStatus = existingDiscount.status == 1 ? 0 : 1;
      existingDiscount.status = newStatus;
      this.updateDiscountStatus({ id: existingDiscount.id, status: newStatus });
    } else {
      const newDiscount = {
        customer_id: customerId,
        promo_id: promoId,
        status: 1,
      };
      this.discounts.push(newDiscount);
      this.createDiscount(newDiscount);
    }
  }

  isDiscountApplied(customerId: number, promoId: number): boolean {
    return !!this.discounts.find(
      (d) =>
        d.customer_id == customerId && d.promo_id == promoId && d.status == 1
    );
  }

  getDiscountsPromo(status = "diskon") {
    this.promoService.getPromos({ status: status }).subscribe(
      (res: any) => {
        this.promo = res.data.list;
        console.log(this.promo);
      },
      (err) => {
        console.log(err);
      }
    );
  }

  getDiscounts() {
    this.discountService.getDiscount().subscribe(
      (res: any) => {
        this.discounts = res.data.list;
        console.log(this.discounts);
      },
      (err) => {
        console.log(err);
      }
    );
  }

  filterByCustomer(customers) {
    const customerIdsString = customers.join(",");
    this.filter.id = customerIdsString;
    this.reloadTable();
  }

  reloadTable() {
    this.customerService.getCustomers(this.filter).subscribe((res: any) => {
      this.listCustomers = res.data.list;
      this.showLoading = false;
      console.log(res);
    });
  }

  getCustomers(name = "") {
    this.customerService.getCustomers({ name: name }).subscribe(
      (res: any) => {
        this.listCustomers = res.data.list;
        this.showLoading = false;
      },
      (err) => {
        console.log(err);
      }
    );
  }

  createDiscount(discount: any): void {
    this.discountService.createDiscount(discount).subscribe(
      (res: any) => {
        console.log(res);
      },
      (err) => {
        console.error(err);
      }
    );
  }

  updateDiscountStatus(payload: any): void {
    const existingDiscount = this.discounts.find((d) => d.id == payload.id);

    if (existingDiscount) {
      payload.customer_id = existingDiscount.customer_id;
      payload.promo_id = existingDiscount.promo_id;

      this.discountService.updateDiscount(payload).subscribe(
        (res: any) => {
          console.log(res);
        },
        (err) => {
          console.log(err);
          console.error(err);
        }
      );
    }
  }

  getTotalDiscountStatus(promoId: number, discounts: any[]) {
    let count = 0;
    discounts.forEach((discount) => {
      if (discount.status == 1 && discount.promo_id == promoId) {
        count++;
      }
    });
    return count !== 0 ? count : 0;
  }

  updateCustomer(modalId, customer) {
    this.titleModal = "Edit customer: " + customer.name;
    this.customerId = customer.id;
    this.modalService.open(modalId, { size: "lg", backdrop: "static" });
  }
}
