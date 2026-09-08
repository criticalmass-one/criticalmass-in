import { Controller } from '@hotwired/stimulus';
import DataTable from 'datatables.net-bs5';

// Erst holen, wenn die Seite das braucht: Dieser Controller zieht
// DataTables herein, und das gehoert nicht in jede Seite, die es nie
// benutzt.
//
// Die Schreibweise ist vorgegeben — der lazy-controller-loader
// erkennt genau diesen einzeiligen Kommentar.
/* stimulusFetch: 'lazy' */
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
