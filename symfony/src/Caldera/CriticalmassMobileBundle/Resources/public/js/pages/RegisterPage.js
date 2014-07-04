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
        url: Url.getUrlPrefix() + 'register/',
        context: this,
        success: function (data) {
            var this2 = this;
            function processValidation(formResultData)
            {
                var validationErrors = 0;

                if (JSON.stringify(formResultData).match('Dieser Benutzername wird bereits verwendet'))
                {
                    this2.showErrorMessage('#registrationUsernameError', 'Dieser Benutzername ist leider bereits vergeben');
                    ++validationErrors;
                    _paq.push(['trackEvent', 'registerValidation', 'failureUsernameDuplicate']);
                }
                else
                if (JSON.stringify(formResultData).match('Bitte geben Sie einen Benutzernamen an'))
                {
                    this2.showErrorMessage('#registrationUsernameError', 'Du hast leider vergessen, einen Benutzernamen einzugeben');
                    ++validationErrors;
                    _paq.push(['trackEvent', 'registerValidation', 'failureUsernameMissing']);
                }
                else
                {
                    this2.clearErrorMessage('#registrationUsernameError');
                }


                if (JSON.stringify(formResultData).match('Diese E-Mail-Adresse wird bereits verwendet'))
                {
                    this2.showErrorMessage('#registrationAddressError', 'Mit dieser E-Mail-Adresse ist bereits ein Benutzerkonto registriert worden');
                    ++validationErrors;
                    _paq.push(['trackEvent', 'registerValidation', 'failureAddressDuplicate']);
                }
                else
                if (JSON.stringify(formResultData).match('Diese E-Mail-Adresse ist ungültig'))
                {
                    this2.showErrorMessage('#registrationAddressError', 'Du hast leider eine ungültige E-Mail-Adresse eingegeben');
                    ++validationErrors;
                    _paq.push(['trackEvent', 'registerValidation', 'failureAddressInvalid']);
                }
                else
                if (JSON.stringify(formResultData).match('Bitte geben Sie eine E-Mail-Adresse an'))
                {
                    this2.showErrorMessage('#registrationAddressError', 'Bitte gib eine E-Mail-Adresse ein');
                    ++validationErrors;
                    _paq.push(['trackEvent', 'registerValidation', 'failureAddressMissing']);
                }
                else
                {
                    this2.clearErrorMessage('#registrationAddressError');
                }


                if (JSON.stringify(formResultData).match('Bitte geben Sie ein Passwort an'))
                {
                    this2.showErrorMessage('#registrationPasswordError', 'Bitte wähle ein Kennwort für dein Benutzerkonto');
                    ++validationErrors;
                    _paq.push(['trackEvent', 'registerValidation', 'failurePasswordMissing']);
                }
                else
                {
                    this2.clearErrorMessage('#registrationPasswordError');
                }

                if (validationErrors == 0)
                {
                    PageDispatcher.switchPage('registerSuccessPage');

                    _paq.push(['trackEvent', 'registerValidation', 'success']);
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
                url: Url.getUrlPrefix() + 'register/',
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