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
            var notificationLayer = new NotificationLayer('Wir kommen leider nicht an deine Positionsdaten ran. Bitte erlaube dieser App den Zugriff auf deine Positionsdaten.');
            break;
        case positionError.POSITION_UNAVAILABLE:
            var notificationLayer = new NotificationLayer('Dein Gerät hat leider keine brauchbaren Positionsdaten zurückgeliefert.');
            break;
        case positionError.TIMEOUT:
            var notificationLayer = new NotificationLayer('Dein Gerät konnte die Anfrage an deine Positionsdaten leider nicht bearbeiten.');
            break;
        case positionError.UNKNOWN_ERROR:
            var notificationLayer = new NotificationLayer('Hmm, es ist ein unbekannter Fehler aufgetreten — mehr wissen wir leider auch nicht.');
            break;
    }

    this.parentPage.showNotificationLayer(notificationLayer);
}