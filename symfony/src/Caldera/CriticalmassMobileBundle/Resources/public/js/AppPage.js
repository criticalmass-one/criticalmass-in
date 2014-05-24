AppPage = function(pageIdentifier)
{
    if (pageIdentifier && $('#' + pageIdentifier).length)
    {
        this.pageIdentifier = pageIdentifier;
    }
    else if (pageIdentifier)
    {
        alert('PageIdentifier #' + pageIdentifier + ' nicht gefunden!');
    }
};

AppPage.prototype.initEventListeners = function()
{
    var this2 = this;

    $('a#logoutButton').click(function()
    {
        this2.logout();
    });
}

AppPage.prototype.positionSender = null;

AppPage.prototype.pageIdentifier = null;

AppPage.prototype.citySlug = 'Hamburg';

AppPage.prototype.setCitySlug = function(newCitySlug)
{
    this.citySlug = newCitySlug;
}

AppPage.prototype.getCitySlug = function()
{
    return this.citySlug;
}

AppPage.prototype.getUserLoginStatus = function()
{
    return localStorage.userLoginStatus == 'true';
}

AppPage.prototype.initMenuUserStatus = function()
{
    if (this.getUserLoginStatus() == true)
    {
        $('.show-loggedin-only').show();
        $('.show-loggedout-only').hide();
    }
    else
    {
        $('.show-loggedout-only').show();
        $('.show-loggedin-only').hide();
    }
}

AppPage.prototype.logout = function()
{
    $.ajax({
        type : 'GET',
        context : this,
        url : UrlFactory.getUrlPrefix() + 'logout',
        success : function(data)
        {
            this.switchToLoggedOutMode();
        }
    });
}

AppPage.prototype.switchToLoggedInMode = function(data)
{
    localStorage.userLoginStatus = true;
    localStorage.userName = data.username;

    var notificationLayer = new NotificationLayer("Hej " + data.username + ", willkommen zurück!");
    this.showNotificationLayer(notificationLayer);

    this.toggleMenuItems();
}

AppPage.prototype.switchToLoggedOutMode = function()
{
    localStorage.userLoginStatus = false;
    localStorage.userName = null;

    var notificationLayer = new NotificationLayer("Du hast dich gerade abgemeldet. Bis zum nächsten Mal!");
    this.showNotificationLayer(notificationLayer);

    this.toggleMenuItems();
}

AppPage.prototype.toggleMenuItems = function()
{
    $('#profileButton').toggle();
    $('#loginButton').toggle();
    $('#logoutButton').toggle();
    $('#registerButton').toggle();
}

AppPage.prototype.switchCityBySlug = function(newCitySlug)
{
    var newCity = CityFactory.getCityFromStorageBySlug(newCitySlug);

    this.setAppTitle(newCity.getTitle());
    this.refreshCityTitles(newCity.getTitle());
}

AppPage.prototype.setAppTitle = function(newTitle)
{
    $('title').html(newTitle);
}

AppPage.prototype.refreshCityTitles = function(newTitle)
{
    $('.city-full-title').html(newTitle);
}

AppPage.prototype.showNotificationLayer = function(notificationLayer)
{
    if ($('#notificationLayer').length == 0)
    {
        $('#' + this.pageIdentifier + ' section[data-role="content"]').prepend('<div id="notificationLayer" class="notification" data-icon="navigation">' + notificationLayer.getNotificationMessage() + '</div>');

        var this2 = this;

        $('#notificationLayer').click(function()
        {
            this2.flushNotification();
        });
    }
}

AppPage.prototype.flushNotification = function()
{
    $('div#notificationLayer').slideUp(250, function() {
        this.remove();
    });
}

AppPage.prototype.refreshGpsQualityGauge = function()
{
    var theme = this.positionSender.getJQueryQualityTheme();

    var gpsGauge = $('a.gpsgauge');

    gpsGauge.attr('data-theme', theme);
    gpsGauge.trigger('create');
}