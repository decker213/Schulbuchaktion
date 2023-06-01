import { Component } from '@angular/core';
import { Datasource } from '../datasources/datasource';
import { SchoolclassService } from '../service/schoolclass.service';
import {OrderlistService} from "../service/orderlist.service";

@Component({
  selector: 'app-classes-overview',
  templateUrl: './classes-overview.component.html',
  styleUrls: ['./classes-overview.component.css']
})
export class ClassesOverviewComponent {
  title = 'Klassenuebersicht';
  dataSource: Datasource<SchoolclassService>;

  selectedItemKeys: any[] = [];

  constructor(private subjectService: SchoolclassService) {
    this.dataSource = new Datasource(subjectService);
  }

  selectionChanged(data: any) {
    this.selectedItemKeys = data.selectedRowKeys;
  }





}
