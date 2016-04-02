define(['chartjs'], function() {
    CityStatisticPage = function(context, options) {
    };

    CityStatisticPage.prototype._rideDates = [];
    CityStatisticPage.prototype._participantsData = [];
    CityStatisticPage.prototype._durationData = [];
    CityStatisticPage.prototype._distanceData = [];

    CityStatisticPage.prototype._participantsChart = null;

    CityStatisticPage.prototype.addRideData = function(rideDate, participants, duration, distance) {
        this._rideDates.push(rideDate);
        this._participantsData.push(participants);
        this._durationData.push(duration);
        this._distanceData.push(distance);
    };

    CityStatisticPage.prototype.createParticipantsChart = function($element) {
        var data = {
            labels: this._rideDates,
            datasets: [
                {
                    label: "Teilnehmer",

                    // Boolean - if true fill the area under the line
                    fill: false,

                    // String - the color to fill the area under the line with if fill is true
                    backgroundColor: "rgba(220,220,220,0.2)",

                    // The properties below allow an array to be specified to change the value of the item at the given index

                    // String or array - Line color
                    borderColor: "rgba(0,0,255,1)",

                    // String - cap style of the line. See https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/lineCap
                    borderCapStyle: 'butt',

                    // Array - Length and spacing of dashes. See https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/setLineDash
                    borderDash: [],

                    // Number - Offset for line dashes. See https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/lineDashOffset
                    borderDashOffset: 0.0,

                    // String - line join style. See https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/lineJoin
                    borderJoinStyle: 'miter',

                    // String or array - Point stroke color
                    pointBorderColor: "rgba(220,220,220,1)",

                    // String or array - Point fill color
                    pointBackgroundColor: "#fff",

                    // Number or array - Stroke width of point border
                    pointBorderWidth: 1,

                    // Number or array - Radius of point when hovered
                    pointHoverRadius: 5,

                    // String or array - point background color when hovered
                    pointHoverBackgroundColor: "rgba(220,220,220,1)",

                    // Point border color when hovered
                    pointHoverBorderColor: "rgba(220,220,220,1)",

                    // Number or array - border width of point when hovered
                    pointHoverBorderWidth: 2,

                    // Tension - bezier curve tension of the line. Set to 0 to draw straight Wlines connecting points
                    tension: 0.1,

                    // The actual data
                    data: this._participantsData,

                    // String - If specified, binds the dataset to a certain y-axis. If not specified, the first y-axis is used. First id is y-axis-0
                    yAxisID: "y-axis-0",
                }
            ]
        };

        this._participantsChart = new Chart($element, {
            type: 'line',
            data: data
        });
    };

    CityStatisticPage.prototype.createDistanceChart = function($element) {
        var data = {
            labels: this._rideDates,
            datasets: [
                {
                    label: "Tourlänge",

                    // Boolean - if true fill the area under the line
                    fill: false,

                    // String - the color to fill the area under the line with if fill is true
                    backgroundColor: "rgba(220,220,220,0.2)",

                    // The properties below allow an array to be specified to change the value of the item at the given index

                    // String or array - Line color
                    borderColor: "rgba(0,0,255,1)",

                    // String - cap style of the line. See https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/lineCap
                    borderCapStyle: 'butt',

                    // Array - Length and spacing of dashes. See https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/setLineDash
                    borderDash: [],

                    // Number - Offset for line dashes. See https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/lineDashOffset
                    borderDashOffset: 0.0,

                    // String - line join style. See https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/lineJoin
                    borderJoinStyle: 'miter',

                    // String or array - Point stroke color
                    pointBorderColor: "rgba(220,220,220,1)",

                    // String or array - Point fill color
                    pointBackgroundColor: "#fff",

                    // Number or array - Stroke width of point border
                    pointBorderWidth: 1,

                    // Number or array - Radius of point when hovered
                    pointHoverRadius: 5,

                    // String or array - point background color when hovered
                    pointHoverBackgroundColor: "rgba(220,220,220,1)",

                    // Point border color when hovered
                    pointHoverBorderColor: "rgba(220,220,220,1)",

                    // Number or array - border width of point when hovered
                    pointHoverBorderWidth: 2,

                    // Tension - bezier curve tension of the line. Set to 0 to draw straight Wlines connecting points
                    tension: 0.1,

                    // The actual data
                    data: this._distanceData,

                    // String - If specified, binds the dataset to a certain y-axis. If not specified, the first y-axis is used. First id is y-axis-0
                    yAxisID: "y-axis-0",
                }
            ]
        };

        this._participantsChart = new Chart($element, {
            type: 'line',
            data: data
        });
    };

    CityStatisticPage.prototype.createDurationChart = function($element) {
        var data = {
            labels: this._rideDates,
            datasets: [
                {
                    label: "Fahrtdauer",

                    // Boolean - if true fill the area under the line
                    fill: false,

                    // String - the color to fill the area under the line with if fill is true
                    backgroundColor: "rgba(220,220,220,0.2)",

                    // The properties below allow an array to be specified to change the value of the item at the given index

                    // String or array - Line color
                    borderColor: "rgba(0,0,255,1)",

                    // String - cap style of the line. See https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/lineCap
                    borderCapStyle: 'butt',

                    // Array - Length and spacing of dashes. See https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/setLineDash
                    borderDash: [],

                    // Number - Offset for line dashes. See https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/lineDashOffset
                    borderDashOffset: 0.0,

                    // String - line join style. See https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/lineJoin
                    borderJoinStyle: 'miter',

                    // String or array - Point stroke color
                    pointBorderColor: "rgba(220,220,220,1)",

                    // String or array - Point fill color
                    pointBackgroundColor: "#fff",

                    // Number or array - Stroke width of point border
                    pointBorderWidth: 1,

                    // Number or array - Radius of point when hovered
                    pointHoverRadius: 5,

                    // String or array - point background color when hovered
                    pointHoverBackgroundColor: "rgba(220,220,220,1)",

                    // Point border color when hovered
                    pointHoverBorderColor: "rgba(220,220,220,1)",

                    // Number or array - border width of point when hovered
                    pointHoverBorderWidth: 2,

                    // Tension - bezier curve tension of the line. Set to 0 to draw straight Wlines connecting points
                    tension: 0.1,

                    // The actual data
                    data: this._durationData,

                    // String - If specified, binds the dataset to a certain y-axis. If not specified, the first y-axis is used. First id is y-axis-0
                    yAxisID: "y-axis-0",
                }
            ]
        };

        this._participantsChart = new Chart($element, {
            type: 'line',
            data: data
        });
    };

    return CityStatisticPage;
});