import { Controller } from '@hotwired/stimulus';
import DataTable from 'datatables.net-bs5';

export default class extends Controller {
    static values = {
        paging: { type: Boolean, default: false },
        searching: { type: Boolean, default: false }
    }

    connect() {
        this.dataTable = new DataTable(this.element, {
            paging: this.pagingValue,
            searching: this.searchingValue
        });
    }

    disconnect() {
        if (this.dataTable) {
            this.dataTable.destroy();
        }
    }
}
