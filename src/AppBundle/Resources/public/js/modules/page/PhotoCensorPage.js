define(['CriticalService', 'jquery-areaselect'], function (CriticalService) {
    PhotoCensorPage = function () {
        alert('foo');
        this._CriticalService = CriticalService;
    };

    PhotoCensorPage.prototype._CriticalService = null;

    return PhotoCensorPage;
});
