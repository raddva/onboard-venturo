import {
  Component,
  EventEmitter,
  Input,
  OnInit,
  Output,
  SimpleChange,
} from "@angular/core";
import { UserService } from "../../services/user.service";
import { LandaService } from "src/app/core/services/landa.service";
@Component({
  selector: "app-form-user",
  templateUrl: "./form-user.component.html",
  styleUrls: ["./form-user.component.scss"],
})
export class FormUserComponent implements OnInit {
  readonly DEFAULT_ROLE = "2";
  readonly MODE_CREATE = "add";
  readonly MODE_UPDATE = "update";

  @Input() userId: any;
  @Output() afterSave = new EventEmitter<boolean>();

  activeMode: string;

  formModel: {
    id: any;
    name: string;
    email: string;
    password: string;
    photo: any;
    photo_url: any;
    phone_number: string;
    user_roles_id: string;
  };

  isDisabledForm: boolean = false;
  constructor(
    private userService: UserService,
    private landaService: LandaService
  ) {}

  roles: any;
  getRoles() {
    this.userService.getRoles([]).subscribe(
      (res: any) => {
        this.roles = res.data.list;
      },
      (err) => {
        console.log(err);
      }
    );
  }
  getUser(userId) {
    console.log(userId);
    this.userService.getUserById(userId).subscribe(
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
    this.getRoles();
    this.formModel = {
      id: "",
      name: "",
      email: "",
      password: "",
      phone_number: "",
      user_roles_id: this.DEFAULT_ROLE,
      photo: "",
      photo_url: "",
    };

    if (this.userId != 0) {
      this.activeMode = this.MODE_UPDATE;
      this.getUser(this.userId);
      return true;
    }

    this.activeMode = this.MODE_CREATE;
  }

  save() {
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
    this.userService.createUser(this.formModel).subscribe(
      (res: any) => {
        this.landaService.alertSuccess("Berhasil", res.message);
        this.afterSave.emit();
        this.isDisabledForm = false;
        console.log(this.formModel);

        console.log(res);
      },
      (err) => {
        this.landaService.alertError("Mohon Maaf", err.error.errors);
        this.isDisabledForm = false;
      }
    );
  }

  update() {
    this.isDisabledForm = true;
    this.userService.updateUser(this.formModel).subscribe(
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
