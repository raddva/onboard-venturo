import { NgModule } from "@angular/core";
import { Routes, RouterModule } from "@angular/router";

import { LayoutComponent } from "./layouts/layout.component";
import { AuthGuard } from "./core/guards/auth.guard";

const routes: Routes = [
  {
    path: "auth",
    loadChildren: () =>
      import("./feature/auth/auth.module").then((m) => m.AuthModule),
  },
  {
    path: "",
    component: LayoutComponent,
    loadChildren: () =>
      import("./feature/feature.module").then((m) => m.FeatureModule),
    canActivate: [AuthGuard],
  },
];

@NgModule({
  imports: [RouterModule.forRoot(routes, { scrollPositionRestoration: "top" })],
  exports: [RouterModule],
})
export class AppRoutingModule {}
