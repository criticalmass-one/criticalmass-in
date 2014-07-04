ColorPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;
};

ColorPage.prototype = new AppPage();

ColorPage.prototype.constructor = ColorPage;

ColorPage.prototype.initPage = function()
{
    var this2 = this;

    $('#' + this.pageIdentifier + ' input').on('change', function()
    {
        this2.updateUserColor();
    });

    $('#randomColorButton').on('click', function()
    {
        this2.setRandomColor();
    });

    $('#' + this.pageIdentifier + ' input').on('slidestop', function()
    {
        this2.saveUserColor();
    });

    $.ajax({
        type: 'GET',
        url: Url.getApiPrefix() + 'user/getcolors',
        cache: false,
        context: this,
        success: function(data)
        {
            this2.initUsercolor(data);
        }
    });
};

ColorPage.prototype.initUsercolor = function(ajaxRequestData)
{
    $('#slider-usercolor-red').val(ajaxRequestData.red);
    $('#slider-usercolor-green').val(ajaxRequestData.green);
    $('#slider-usercolor-blue').val(ajaxRequestData.blue);

    $('#' + this.pageIdentifier + ' input').slider('refresh');
};

ColorPage.prototype.setRandomColor = function()
{
    var red = Math.floor(Math.random() * 255);
    var green = Math.floor(Math.random() * 255);
    var blue = Math.floor(Math.random() * 255);

    $('#slider-usercolor-red').val(red);
    $('#slider-usercolor-green').val(green);
    $('#slider-usercolor-blue').val(blue);

    $('#' + this.pageIdentifier + ' input').slider('refresh');

    $('#colorExample').css('background-color', 'rgb(' + red + ', ' + green + ', ' + blue + ')');

    _paq.push(['trackEvent', 'color', 'random']);

    this.saveUserColor();
};

ColorPage.prototype.updateUserColor = function()
{
    var red = $('#slider-usercolor-red').val();
    var green = $('#slider-usercolor-green').val();
    var blue = $('#slider-usercolor-blue').val();

    $('#colorExample').css('background-color', 'rgb(' + red + ', ' + green + ', ' + blue + ')');
};

ColorPage.prototype.saveUserColor = function()
{
    var red = $('#slider-usercolor-red').val();
    var green = $('#slider-usercolor-green').val();
    var blue = $('#slider-usercolor-blue').val();

    var colorData = { red: red, green: green, blue: blue };

    $.ajax({
        type: 'GET',
        url: Url.getApiPrefix() + 'user/setcolors',
        cache: false,
        context: this,
        data: colorData,
        success: function(data)
        {
            _paq.push(['trackEvent', 'color', 'save']);
        }
    });
};