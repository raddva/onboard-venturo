import {
  Component,
  EventEmitter,
  Input,
  OnInit,
  Output,
  SimpleChange,
} from "@angular/core";
import { LandaService } from "src/app/core/services/landa.service";
import { CustomerService } from "../../services/customer.service";
@Component({
  selector: "app-form-customer",
  templateUrl: "./form-customer.component.html",
  styleUrls: ["./form-customer.component.scss"],
})
export class FormCustomerComponent implements OnInit {
  readonly MODE_CREATE = "add";
  readonly MODE_UPDATE = "update";

  @Input() customerId: any;
  @Output() afterSave = new EventEmitter<boolean>();

  activeMode: string;
  verified = [
    { value: 1, label: "Verifikasi" },
    { value: 0, label: "Belum Verifikasi" },
  ];

  formModel: {
    id: any;
    name: string;
    email: string;
    photo_url: any;
    photo: any;
    phone_number: string;
    date_of_birth: any;
    is_verified: number;
  };

  isDisabledForm: boolean = false;
  constructor(
    private customerService: CustomerService,
    private landaService: LandaService
  ) {}

  getCustomer(customerId) {
    this.customerService.getCustomerById(customerId).subscribe(
      (res: any) => {
        this.formModel = res.data;
        console.log(res);
      },
      (err) => {
        console.log(err);
      }
    );
  }
  getCroppedImage($event) {
    this.formModel.photo = $event;
  }
  resetForm() {
    this.formModel = {
      id: "",
      name: "",
      email: "",
      phone_number: "",
      photo: "",
      photo_url: "",
      date_of_birth: "",
      is_verified: 0,
    };

    if (this.customerId != 0) {
      this.activeMode = this.MODE_UPDATE;
      this.getCustomer(this.customerId);
      return true;
    }

    this.activeMode = this.MODE_CREATE;
  }
  save() {
    console.log(this.formModel);

    switch (this.activeMode) {
      case this.MODE_CREATE:
        this.insert();
        break;
      case this.MODE_UPDATE:
        this.update();
        break;
    }
  }

  insert() {
    this.isDisabledForm = true;
    console.log(this.formModel);
    this.customerService.createCustomer(this.formModel).subscribe(
      (res: any) => {
        this.landaService.alertSuccess("Berhasil", res.message);
        this.afterSave.emit();
        this.isDisabledForm = false;
      },
      (err) => {
        this.landaService.alertError("Mohon Maaf", err.error.errors);
        this.isDisabledForm = false;
      }
    );
  }

  update() {
    this.isDisabledForm = true;
    this.customerService.updateCustomer(this.formModel).subscribe(
      (res: any) => {
        this.landaService.alertSuccess("Berhasil", res.message);
        this.afterSave.emit();
        this.isDisabledForm = false;
      },
      (err) => {
        this.landaService.alertError("Mohon Maaf", err.error.errors);
        this.isDisabledForm = false;
      }
    );
  }

  ngOnChanges(changes: SimpleChange) {
    this.resetForm();
  }
  ngOnInit(): void {}
}
