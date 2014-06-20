LoginPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;
}

LoginPage.prototype = new AppPage();

LoginPage.prototype.constructor = LoginPage;

LoginPage.prototype.initPage = function()
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

    $.ajax({
        type: 'GET',
        url: 'https://criticalmass.cm/app_dev.php/login',
        context: this,
        success: function (data) {
            var this2 = this;

            function processValidation(formResultData)
            {
                if (JSON.stringify(formResultData).match('Benutzername oder Passwort ungültig'))
                {
                    this2.showErrorMessage('Dein Benutzername oder dein Kennwort sind leider nicht korrekt');
                }
                else
                {
                    PageDispatcher.switchPage('loginSuccessPage');
                }
            }

            var loginData = {};
            loginData['_username'] = $('input[name="_username"]').val();
            loginData['_password'] = $('input[name="_password"]').val();
            loginData['_remember_me'] = 'on';
            loginData['_submit'] = '';
            loginData['_csrf_token'] = $(data).find('input[name="_csrf_token"]').val();

            $.ajax({
                url: UrlFactory.getUrlPrefix() + 'login_check',
                type: 'POST',
                context: this2,
                dataType: 'json',
                data: loginData,
                success: function(data)
                {
                    processValidation(data);
                },
                error: function(data)
                {
                    processValidation(data);
                }
            });
        }
    });
};

LoginPage.prototype.showErrorMessage = function(errorMessage)
{
    $('#loginErrorMessage').html(errorMessage);
    $('#loginUsernameInput').addClass('validationError');
    $('#loginPasswordInput').addClass('validationError');
};