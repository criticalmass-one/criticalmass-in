PositionSender = function(parentPage)
{
    this.parentPage = parentPage;

    this.catchPosition();
};

PositionSender.prototype.parentPage = null;

PositionSender.prototype.catchPosition = function()
{
    if (navigator.geolocation)
    {
        var this2 = this;

        function processError2(positionError)
        {
            this2.processError(positionError);
        }

        function processPosition2(positionResult)
        {
            this2.processPosition(positionResult);
        }

        navigator.geolocation.getCurrentPosition(processPosition2,
                                                 processError2);
    }
    else
    {
        var notificationLayer = new NotificationLayer('Schade: Dein Browser unterstützt leider noch keine Positionsbestimmung.');
        this.showNotificationLayer(notificationLayer);
    }
}

PositionSender.prototype.processPosition = function(positionResult)
{
    alert(positionResult);
}

PositionSender.prototype.processError = function(positionError)
{
    switch(positionError.code)
    {
        case positionError.PERMISSION_DENIED:
            var notificationLayer = new NotificationLayer('User denied the request for Geolocation.');
            break;
        case positionError.POSITION_UNAVAILABLE:
            var notificationLayer = new NotificationLayer('Location information is unavailable.');
            break;
        case positionError.TIMEOUT:
            var notificationLayer = new NotificationLayer('The request to get user location timed out.');
            break;
        case positionError.UNKNOWN_ERROR:
            var notificationLayer = new NotificationLayer('An unknown error occurred.');
            break;
    }

    this.parentPage.showNotificationLayer(notificationLayer);
}