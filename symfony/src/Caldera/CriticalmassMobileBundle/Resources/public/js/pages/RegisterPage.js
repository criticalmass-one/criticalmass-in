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

    $.ajax({
        type: 'GET',
        url: 'https://criticalmass.cm/app_dev.php/register/',
        context: this,
        success: function (data) {
            var registerData = {};
            registerData['sonata_user_registration_form[username]'] = $('#form-register input[name="_username"]').val();
            registerData['sonata_user_registration_form[plainPassword][first]'] = $('#form-register input[name="_password"]').val();
            registerData['sonata_user_registration_form[plainPassword][second]'] = $('#form-register input[name="_password"]').val();
            registerData['sonata_user_registration_form[email]'] = $('#form-register input[name="_email"]').val();
            registerData['sonata_user_registration_form[_token]'] = $(data).find('#sonata_user_registration_form__token').val();

            $.ajax({
                type: 'POST',
                url: 'https://criticalmass.cm/app_dev.php/register/',
                context: this,
                data: registerData,
                dataType: 'json',
                success: function (res) {

                    alert('OK: ' + JSON.stringify(res)); // JUST FOR TEST
                },
                error: function (res)
                {
                    alert('ERROR: ' + JSON.stringify(res));
                    this.presentErrorMessage('Nee');
                }});
        }
    });
};

RegisterPage.prototype.presentErrorMessage = function(errorMessage)
{
    $.mobile.changePage('#registerPageErrorDialog', 'pop', true, true);
}