import { Component, ViewChild } from "@angular/core";
import { NgbModal } from "@ng-bootstrap/ng-bootstrap";
import Swal from "sweetalert2";
import { DataTableDirective } from "angular-datatables";
import { CustomerService } from "../../services/customer.service";

@Component({
  selector: "app-list-customer",
  templateUrl: "./list-customer.component.html",
  styleUrls: ["./list-customer.component.scss"],
})
export class ListCustomerComponent {
  @ViewChild(DataTableDirective, { static: false })
  dtElement: DataTableDirective;
  dtInstance: Promise<DataTables.Api>;
  dtOptions: any;

  listCustomer: any;
  titleModal: string;
  customerId: any;
  formCustomer: any;
  filter = {
    name: "",
    is_verified: "",
  };
  constructor(
    private customerService: CustomerService,
    private modalService: NgbModal
  ) {}

  getCustomer() {
    console.log("Filter:", this.filter);

    this.dtOptions = {
      serverSide: true,
      processing: true,
      ordering: false,
      pageLength: 5,
      ajax: (dtParams: any, callback) => {
        const params = {
          ...this.filter,
          per_page: dtParams.length,
          page: dtParams.start / dtParams.length + 1,
        };

        this.customerService.getCustomers(params).subscribe(
          (res: any) => {
            const { list, meta } = res.data;

            let number = dtParams.start + 1;
            list.forEach((val) => {
              val.no = number++;
            });
            this.listCustomer = list;

            callback({
              recordsTotal: meta.total,
              recordsFiltered: meta.total,
              data: [],
            });
            console.log(res);
          },
          (err: any) => {
            console.error("Error fetching data:", err);
          }
        );
      },
    };
  }

  reloadDataTable(): void {
    this.dtElement.dtInstance.then((dtInstance: DataTables.Api) => {
      dtInstance.draw();
    });
  }

  createCustomer(modalId) {
    this.titleModal = "Tambah customer";
    this.customerId = 0;
    this.modalService.open(modalId, { size: "lg", backdrop: "static" });
  }

  updateCustomer(modalId, customer) {
    this.titleModal = "Edit customer: " + customer.name;
    this.customerId = customer.id;
    this.modalService.open(modalId, { size: "lg", backdrop: "static" });
  }

  deleteCustomer(customerId) {
    Swal.fire({
      title: "Apakah kamu yakin ?",
      text: "customer ini tidak dapat login setelah kamu menghapus datanya",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#34c38f",
      cancelButtonColor: "#f46a6a",
      confirmButtonText: "Ya, Hapus data ini !",
    }).then((result) => {
      if (!result.value) return false;

      this.customerService.deleteCustomer(customerId).subscribe((res: any) => {
        this.getCustomer();
      });
    });
  }

  setDefault() {
    this.filter = {
      name: "",
      is_verified: "",
    };
  }

  ngOnInit(): void {
    this.setDefault();
    this.getCustomer();
  }
}
