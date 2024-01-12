import { Component, EventEmitter, Input, OnInit, Output } from "@angular/core";
import { SalesService } from "../../services/sales.service";

@Component({
  selector: "app-modal-detail",
  templateUrl: "./modal-detail.component.html",
  styleUrls: ["./modal-detail.component.scss"],
})
export class ModalDetailComponent implements OnInit {
  @Input() customerId: any;
  @Input() selectedDate: any;
  @Output() afterSave = new EventEmitter<boolean>();
  ngOnInit(): void {
    this.getCustomerSale(this.customerId);
  }

  constructor(private saleService: SalesService) {}
  transactions: any;
  sales: any;
  meta: {
    dates: [];
    total_per_date: [];
    grand_total: 0;
  };

  getCustomerSale(customerId) {
    this.saleService.getCustomerSales(customerId).subscribe(
      (res: any) => {
        const { data, settings } = res;
        this.sales = data;
        this.meta = settings;
        this.filterTransactionsByDate();
        console.log(res);
      },
      (err) => {
        console.log(err);
      }
    );
  }

  filterTransactionsByDate() {
    if (this.selectedDate) {
      this.transactions = this.sales.reduce(
        (acc, customer) => [
          ...acc,
          ...customer.transactions.filter(
            (transaction) => transaction.date_transaction === this.selectedDate
          ),
        ],
        []
      );
    } else {
      this.transactions = this.sales.reduce(
        (acc, customer) => [...acc, ...customer.transactions],
        []
      );
    }
  }
}
