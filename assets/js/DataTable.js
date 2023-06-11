import dataTables from 'dataTables'; // @todo: replace jQuery here

export default class DataTable {
    constructor(element, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        this.init();
    }

    init() {
        const dataTable = document.getElementsByClassName('data-table');

        if (dataTable) {
            $('.data-table').DataTable({ // @todo: replace jQuery here
                'paging': false,
                'searching': false,
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const element = document.querySelector('.data-table');

    new DataTable(element);
});
