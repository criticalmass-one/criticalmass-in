RegisterPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;
};

RegisterPage.prototype = new AppPage();

RegisterPage.prototype.constructor = RegisterPage;

RegisterPage.prototype.initPage = function()
{
    var this2 = this;
    $('form#form-register').on('submit', function(element)
    {
        this2.processRegistration(element);
    });
};

RegisterPage.prototype.processRegistration = function (element) {
    element.preventDefault();

    var registerData = {};
    registerData['sonata_user_registration_form[username]'] = $('#form-register input[name="_username"]').val();
    registerData['sonata_user_registration_form[plainPassword][first]'] = $('#form-register input[name="_password"]').val();
    registerData['sonata_user_registration_form[plainPassword][second]'] = $('#form-register input[name="_password"]').val();
    registerData['sonata_user_registration_form[email]'] = $('#form-register input[name="_email"]').val();

    $.ajax({
        type: 'POST',
        url: 'http://www.criticalmass.cm/app_dev.php/register/',
        context: this,
        data: registerData,
        dataType: 'json',
        success: function (res) {

            alert('OK: ' + JSON.stringify(res)); // JUST FOR TEST
        },
        error: function (res)
        {
            alert(JSON.stringify(res));
            this.presentErrorMessage('Nee');
        }});
};

RegisterPage.prototype.presentErrorMessage = function(errorMessage)
{
    $.mobile.changePage('#registerPageErrorDialog', 'pop', true, true);
}