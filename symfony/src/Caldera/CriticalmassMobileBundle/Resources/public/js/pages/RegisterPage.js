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

/**
 * Das hier ist richtig übel.
 * @param element
 */
RegisterPage.prototype.processRegistration = function(element)
{
    element.preventDefault();

    $('#submitRegistrationButton').attr('disabled', true);

    $.ajax({
        type: 'GET',
        url: 'https://criticalmass.cm/app_dev.php/register/',
        context: this,
        success: function (data) {
            var this2 = this;
            function processValidation(formResultData)
            {
                if (JSON.stringify(formResultData).match('Dieser Benutzername wird bereits verwendet'))
                {
                    this2.showErrorMessage('#registrationUsernameError', 'Dieser Benutzername ist leider bereits vergeben');
                }
                else
                if (JSON.stringify(formResultData).match('Bitte geben Sie einen Benutzernamen an'))
                {
                    this2.showErrorMessage('#registrationUsernameError', 'Du hast leider vergessen, einen Benutzernamen einzugeben');
                }
                else
                {
                    this2.clearErrorMessage('#registrationUsernameError');
                }


                if (JSON.stringify(formResultData).match('Diese E-Mail-Adresse wird bereits verwendet'))
                {
                    this2.showErrorMessage('#registrationAddressError', 'Mit dieser E-Mail-Adresse ist bereits ein Benutzerkonto registriert worden');
                }
                else
                if (JSON.stringify(formResultData).match('Diese E-Mail-Adresse ist ungültig'))
                {
                    this2.showErrorMessage('#registrationAddressError', 'Du hast leider eine ungültige E-Mail-Adresse eingegeben');
                }
                else
                if (JSON.stringify(formResultData).match('Bitte geben Sie eine E-Mail-Adresse an'))
                {
                    this2.showErrorMessage('#registrationAddressError', 'Bitte gib eine E-Mail-Adresse ein');
                }
                else
                {
                    this2.clearErrorMessage('#registrationAddressError');
                }

                if (JSON.stringify(formResultData).match('Bitte geben Sie ein Passwort an'))
                {
                    this2.showErrorMessage('#registrationPasswordError', 'Bitte wähle ein Kennwort für dein Benutzerkonto');
                }
                else
                {
                    this2.clearErrorMessage('#registrationPasswordError');
                }

                if (JSON.stringify(formResultData).match('Eine E-Mail wurde an (.*)@(.*).(.*) gesendet. Sie enthält einen Link, den Du anklicken musst, um Dein Benutzerkonto zu bestätigen.'))
                {
                    PageDispatcher.switchPage('registerSuccessPage');
                }

                $('#submitRegistrationButton').attr('disabled', false);
            }

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

                    processValidation(res);
                },
                error: function (res)
                {
                    processValidation(res);
                }});
        }
    });
};

RegisterPage.prototype.presentErrorMessage = function(errorMessage)
{
    $.mobile.changePage('#registerPageErrorDialog', 'pop', true, true);
};

RegisterPage.prototype.showErrorMessage = function(identifier, errorMessage)
{
    $(identifier).html(errorMessage);
    $(identifier + ' + div input').addClass('validationError');
};

RegisterPage.prototype.clearErrorMessage = function(identifier)
{
    $(identifier).html('');
    $(identifier + ' + div input').addClass('validationSuccess');
};