CityPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;

    //this.initLoginPageEventListeners();

    this.refreshContent();
}

CityPage.prototype = new AppPage();

CityPage.prototype.constructor = CityPage;

CityPage.prototype.refreshContent = function()
{
    var city = CityFactory.getCityFromStorageBySlug(this.getCitySlug());

    var char = 96;

    $('#' + this.pageIdentifier + ' #citySocialMediaLinks').html('');

    if (city.getUrl() != '')
    {
        var html = $('#' + this.pageIdentifier + ' #citySocialMediaLinks').html();
        html += '<div class="ui-block-' + String.fromCharCode(++char) + '"><a id="citySocialMediaLinksUrlButton" href="' + city.getUrl() + '" target="_blank">WWW</a></div>';
        $('#' + this.pageIdentifier + ' #citySocialMediaLinks').html(html);

        $('#' + this.pageIdentifier + ' #citySocialMediaLinksUrlButton').button();
    }

    if (city.getFacebook() != '')
    {
        var html = $('#' + this.pageIdentifier + ' #citySocialMediaLinks').html();
        html += '<div class="ui-block-' + String.fromCharCode(++char) + '"><a id="citySocialMediaLinksFacebookButton" href="' + city.getFacebook() + '" target="_blank">facebook</a></div>';
        $('#' + this.pageIdentifier + ' #citySocialMediaLinks').html(html);

        $('#' + this.pageIdentifier + ' #citySocialMediaLinksFacebookButton').button();
    }

    if (city.getTwitter() != '')
    {
        var html = $('#' + this.pageIdentifier + ' #citySocialMediaLinks').html();
        html += '<div class="ui-block-' + String.fromCharCode(++char) + '"><a id="citySocialMediaLinksTwitterButton" href="' + city.getTwitter() + '" target="_blank">twitter</a></div>';
        $('#' + this.pageIdentifier + ' #citySocialMediaLinks').html(html);

        $('#' + this.pageIdentifier + ' #citySocialMediaLinksTwitterButton').button();
    }
}