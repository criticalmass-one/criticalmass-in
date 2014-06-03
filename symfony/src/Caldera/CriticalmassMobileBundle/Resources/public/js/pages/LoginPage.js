LoginPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;

    this.initLoginPageEventListeners();
}

LoginPage.prototype = new AppPage();

LoginPage.prototype.constructor = LoginPage;

LoginPage.prototype.initLoginPageEventListeners = function()
{
    var this2 = this;

    $('#form-login').submit(function(element)
    {
        this2.processLogin(element);
    })
}

LoginPage.prototype.processLogin = function(element)
{
    element.preventDefault();

    var loginData = {};
    loginData['_username'] = $('input[name="_username"]').val();
    loginData['_password'] = $('input[name="_password"]').val();
    loginData['_remember_me'] = $('input[name="_remember_me"]').val();
    loginData['_submit'] = $('input[name="_submit"]').val();

    var this2 = this;

    $.ajax({
        url: UrlFactory.getUrlPrefix() + 'login_check',
        type: 'POST',
        context: this2,
        dataType: 'json',
        data: loginData,
        success: function(data)
        {
            if (data.has_error)
            {
                $.mobile.changePage('#loginPageErrorDialog', 'pop', true, true);
                _paq.push(['trackEvent', 'user_login', 'failed']);
            }
            else
            {
                _paq.push(['trackEvent', 'user_login', 'success']);
                this2.switchToLoggedInMode(data);
            }
        },
        error: function(data)
        {
            $.mobile.changePage('#loginPageErrorDialog', 'pop', true, true);
            _paq.push(['trackEvent', 'user_login', 'failed']);
        }
    });
}