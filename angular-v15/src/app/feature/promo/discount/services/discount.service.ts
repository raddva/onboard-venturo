import { Injectable } from "@angular/core";
import { LandaService } from "src/app/core/services/landa.service";

@Injectable({
  providedIn: "root",
})
export class DiscountService {
  constructor(private landaService: LandaService) {}

  getDiscount(arrParameter = {}) {
    return this.landaService.DataGet("/v1/discounts", arrParameter);
  }

  getDiscountByCustomer(customerId) {
    return this.landaService.DataGet("/v1/discounts/" + customerId);
  }

  createDiscount(payload) {
    return this.landaService.DataPost("/v1/discounts", payload);
  }

  updateDiscount(payload) {
    return this.landaService.DataPut("/v1/discounts", payload);
  }
}
