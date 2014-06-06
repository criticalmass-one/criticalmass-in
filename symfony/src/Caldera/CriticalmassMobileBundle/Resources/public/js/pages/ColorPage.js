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

    //$('#slider-usercolor-red').slider('value', red);
    $('#colorExample').css('background-color', 'rgb(' + red + ', ' + green + ', ' + blue + ')');
};

ColorPage.prototype.updateUserColor = function()
{
    var red = $('#slider-usercolor-red').val();
    var green = $('#slider-usercolor-green').val();
    var blue = $('#slider-usercolor-blue').val();

    $('#colorExample').css('background-color', 'rgb(' + red + ', ' + green + ', ' + blue + ')');
};