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

AppPage.prototype.isLoaded = false;

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
    return localStorage.userLoginStatus;
}

AppPage.prototype.initMenuUserStatus = function()
{
    if (this.getUserLoginStatus())
    {
        $('#profileButton').show();
        $('#logoutButton').show();
    }
    else
    {
        $('#loginButton').show();
        $('#registerButton').show();
    }
}

AppPage.prototype.logout = function()
{
    $.ajax({
        type : 'GET',
        context : this,
        url : '/app_dev.php/logout',
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

AppPage.prototype.environment = 'dev';

AppPage.prototype.setEnvironment = function(newEnvironment)
{
    this.environment = newEnvironment;
}

AppPage.prototype.getEnvironment = function()
{
    return this.environment;
}


AppPage.prototype.system = 'local';

AppPage.prototype.setSystem = function(newSystem)
{
    this.system = newSystem;
}

AppPage.prototype.getSystem = function()
{
    return this.system;
}


AppPage.prototype.getHostName = function()
{
    if (this.getSystem() == 'local')
    {
        return 'www.criticalmass.local';
    }

    return 'www.criticalmass.in';
}

AppPage.prototype.getPortNumber = function()
{
    return 80;
}

AppPage.prototype.getProtocolString = function()
{
    return 'http://';
}

AppPage.prototype.getEnvironmentString = function()
{
    if (this.getEnvironment() == 'dev')
    {
        return 'app_dev.php';
    }

    return '';
}

AppPage.prototype.getUrlPrefix = function(ajaxResponseData)
{
    return this.getProtocolString() +
           this.getHostName() +
           ':' + this.getPortNumber() +
           '/' + this.getEnvironmentString() +
           '/';
}

AppPage.prototype.getApiPrefix = function()
{
    return this.getUrlPrefix() + 'api/';
}


AppPage.prototype.switchCity = function(newCitySlug)
{
    alert('Stadt gewechselt, jetzt: ' + newCitySlug);
    var newCity = new City(newCitySlug);
}


AppPage.prototype.showNotificationLayer = function(notificationLayer)
{
    $('#' + this.pageIdentifier + ' section[data-role="content"]').prepend('<div id="notificationLayer" class="notification" data-icon="navigation">' + notificationLayer.getNotificationMessage() + '</div>');

    var this2 = this;


    $('#notificationLayer').click(function()
    {
        this2.flushNotification();
    })
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

    $('a.gpsgauge').attr('data-theme', theme);
    $('a.gpsgauge').trigger('create');
}