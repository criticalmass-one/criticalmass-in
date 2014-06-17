TokenPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;
};

TokenPage.prototype = new AppPage();

TokenPage.prototype.constructor = TokenPage;

TokenPage.prototype.initPage = function()
{
    $.ajax({
        type: 'GET',
        url: UrlFactory.getApiPrefix() + 'user/getpingtoken',
        context: this,
        dataType: 'json',
        success: function (res) {
            $('#pingToken').val(res.pingToken);
        }
    });
};