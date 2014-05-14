LoginPage = function()
{
    this.initEventListeners();
    this.initLoginPageEventListeners();
    this.initMenuUserStatus();
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
        url : '/app_dev.php/login_check',
        type : 'POST',
        context : this2,
        dataType : 'json',
        data : loginData,
        success : function(data)
        {
            if (data.has_error)
            {
                alert(data.error);
            }
            else
            {
                this2.switchToLoggedInMode(data);
            }
        },
        failure : function(data)
        {

        }
    });
}