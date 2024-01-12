import { Component, OnInit } from "@angular/core";
import { LandaService } from "src/app/core/services/landa.service";
import Swal from "sweetalert2";
import { SalesService } from "../../services/sales.service";
import { CustomerService } from "src/app/feature/customer/services/customer.service";
import { NgbModal } from "@ng-bootstrap/ng-bootstrap";

@Component({
  selector: "app-sale-customer",
  templateUrl: "./sale-customer.component.html",
  styleUrls: ["./sale-customer.component.scss"],
})
export class SaleCustomerComponent implements OnInit {
  filter: {
    start_date: string;
    end_date: string;
    customer_id: string;
  };
  sales = [
    {
      customer_name: "",
      customer_total: 0,
      transactions: [{ total_sales: 0 }],
    },
  ];
  meta: {
    dates: [];
    total_per_date: [];
    grand_total: 0;
  };

  selectedDate: any;
  customerId: any;
  showLoading: boolean;
  customers: [];
  titleModal: string;
  constructor(
    private salesService: SalesService,
    private customerService: CustomerService,
    private landaService: LandaService,
    private modalService: NgbModal
  ) {}

  ngOnInit(): void {
    this.resetFilter();
    this.getCustomers();
  }

  getCustomers(name = "") {
    this.showLoading = true;
    this.customerService.getCustomers({ name: name }).subscribe(
      (res: any) => {
        this.customers = res.data.list;
        this.showLoading = false;
      },
      (err) => {
        console.log(err);
      }
    );
  }

  resetFilter() {
    this.filter = {
      start_date: null,
      end_date: null,
      customer_id: null,
    };

    this.meta = {
      dates: [],
      total_per_date: [],
      grand_total: 0,
    };

    this.showLoading = false;
  }

  detailTransaction(modalId, customer, date) {
    this.titleModal = customer.customer_name + " / " + this.formatDate(date);
    this.customerId = customer.customer_id;
    this.selectedDate = date;
    this.modalService.open(modalId, { size: "lg", backdrop: "static" });
  }

  formatDate(date: string): string {
    const options: Intl.DateTimeFormatOptions = {
      day: "numeric",
      month: "long",
      year: "numeric",
    };

    const formattedDate = new Date(date).toLocaleDateString("id", options);
    return formattedDate;
  }

  reloadSales() {
    this.runFilterValidation();

    this.salesService.getSalesCustomer(this.filter).subscribe((res: any) => {
      const { data, settings } = res;
      this.sales = data;
      this.meta = settings;
    });
  }

  runFilterValidation() {
    if (!this.filter.start_date || !this.filter.end_date) {
      Swal.fire({
        title: "Terjadi Kesalahan",
        text: "Silahkan isi periode penjualan terlebih dahulu",
        icon: "warning",
        showCancelButton: false,
      });
      throw new Error("Start and End date is required");
    }
  }

  setFilterPeriod($event) {
    this.filter.start_date = $event.startDate;
    this.filter.end_date = $event.endDate;
  }

  setFilterCustomer(customers) {
    this.filter.customer_id = this.generateSafeParam(customers);
  }

  downloadExcel() {
    this.runFilterValidation();
    let queryParams = {
      start_date: this.filter.start_date,
      end_date: this.filter.end_date,
      customer_id: this.filter.customer_id,
      is_export_excel: true,
    };
    this.landaService.DownloadLink("/v1/download/sales-customer", queryParams);
  }

  generateSafeParam(list) {
    let paramId = [];
    list.forEach((val) => paramId.push(val.id));
    if (!paramId) return "";

    return paramId.join(",");
  }

  customersNotEmpty(): boolean {
    return this.sales.some(
      (customer) =>
        customer.transactions.some(
          (transaction) => transaction.total_sales !== 0
        ) || customer.customer_total !== 0
    );
  }
}
