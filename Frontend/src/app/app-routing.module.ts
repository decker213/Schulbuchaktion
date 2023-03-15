import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { OrderlistComponent } from './orderlist/orderlist.component';

const routes: Routes = [
    {path: '', redirectTo: '/bestelluebersicht', pathMatch:'full'},
    {path: 'bestelluebersicht', component: OrderlistComponent},
]

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
