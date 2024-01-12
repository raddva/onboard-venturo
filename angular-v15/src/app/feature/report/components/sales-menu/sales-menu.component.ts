import { Component, OnInit } from "@angular/core";
import { LandaService } from "src/app/core/services/landa.service";
import { CategoryService } from "src/app/feature/product/category/services/category.service";
import Swal from "sweetalert2";
import { SalesService } from "../../services/sales.service";

@Component({
  selector: "app-sales-menu",
  templateUrl: "./sales-menu.component.html",
  styleUrls: ["./sales-menu.component.scss"],
})
export class SalesMenuComponent implements OnInit {
  filter: {
    start_date: string;
    end_date: string;
    category_id;
  };
  sales = [
    {
      category_name: "",
      category_total: 0,
      products: [
        {
          product_name: "",
          transactions_total: 0,
          transactions: [{ total_sales: 0 }],
        },
      ],
    },
  ];
  meta: {
    dates: [];
    total_per_date: [];
    grand_total: 0;
  };
  showLoading: boolean;
  categories: [];

  constructor(
    private salesService: SalesService,
    private categoryService: CategoryService,
    private landaService: LandaService
  ) {}

  ngOnInit(): void {
    this.resetFilter();
    this.getCategories();
  }

  getCategories(name = "") {
    this.showLoading = true;
    this.categoryService.getCategories({ name: name }).subscribe(
      (res: any) => {
        this.categories = res.data.list;
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
      category_id: null,
    };

    this.meta = {
      dates: [],
      total_per_date: [],
      grand_total: 0,
    };

    this.showLoading = false;
  }

  reloadSales() {
    this.runFilterValidation();

    this.salesService.getSalesMenu(this.filter).subscribe((res: any) => {
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

  setFilterCategory($event) {
    this.filter.category_id = $event.id;
  }

  downloadExcel() {
    this.runFilterValidation();
    let queryParams = {
      start_date: this.filter.start_date,
      end_date: this.filter.end_date,
      category_id: this.filter.category_id,
      is_export_excel: true,
    };
    this.landaService.DownloadLink("/v1/download/sales-category", queryParams);
  }

  salesNotEmpty(): boolean {
    return this.sales.some((category) =>
      category.products.some((product) =>
        product.transactions.some(
          (transaction) => transaction.total_sales !== 0
        )
      )
    );
  }
}
