RegisterPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;
};

RegisterPage.prototype = new AppPage();

RegisterPage.prototype.constructor = RegisterPage;

RegisterPage.prototype.initPage = function()
{
    var this2 = this;
    $('form#form-register').submit(function(element)
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

    alert(JSON.stringify(registerData));
    $.ajax({
        type: 'POST',
        url: 'http://www.criticalmass.cm/app_dev.php/register/',
        asyn: false,
        data: registerData,
        dataType: "json",
        success: function (res) {

            alert('OK: ' + JSON.stringify(res)); // JUST FOR TEST
        },
        error: function (res) {
            alert('Fehler: ' + JSON.stringify(res));
        }});
};