NotificationLayer = function(notificationMessage)
{
    this.notificationMessage = notificationMessage;
}

NotificationLayer.prototype.notificationMessage = null;

NotificationLayer.prototype.getNotificationMessage = function()
{
    return this.notificationMessage;
}