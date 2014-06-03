CityPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;
}

CityPage.prototype = new AppPage();

CityPage.prototype.constructor = CityPage;

CityPage.prototype.initPage = function()
{
    var city = CityFactory.getCityFromStorageBySlug(this.getCitySlug());

    this.refreshSocialMediaLinks();

    this.refreshCriticalMassContentLinks();

    $('#' + this.pageIdentifier + ' #cityFullDescription').html(city.getDescripton());
}

CityPage.prototype.refreshCriticalMassContentLinks = function()
{
    $('#' + this.pageIdentifier + ' #cityRideButton').html('37 Touren');
    $('#' + this.pageIdentifier + ' #cityHeatmapButton').html('43 Heatmaps');
}

CityPage.prototype.refreshSocialMediaLinks = function()
{
    var city = CityFactory.getCityFromStorageBySlug(this.getCitySlug());

    var char = 96;

    var socialMediaLinksBox = $('#' + this.pageIdentifier + ' #citySocialMediaLinks');
    socialMediaLinksBox.removeClass('ui-grid-solo ui-grid-a ui-grid-b');
    socialMediaLinksBox.html(' ');

    switch (city.countSocialMediaLinks())
    {
        case 1:
            socialMediaLinksBox.addClass('ui-grid-solo');
            break;
        case 2:
            socialMediaLinksBox.addClass('ui-grid-a');
            break;
        case 3:
            socialMediaLinksBox.addClass('ui-grid-b');
            break;
    }

    if (city.getUrl() != '')
    {
        var html = socialMediaLinksBox.html();
        html += '<div class="ui-block-' + String.fromCharCode(++char) + '"><a id="citySocialMediaLinksUrlButton" href="' + city.getUrl() + '" target="_blank">WWW</a></div>';
        socialMediaLinksBox.html(html);

        $('#' + this.pageIdentifier + ' #citySocialMediaLinksUrlButton').button();
    }

    if (city.getFacebook() != '')
    {
        var html = socialMediaLinksBox.html();
        html += '<div class="ui-block-' + String.fromCharCode(++char) + '"><a id="citySocialMediaLinksFacebookButton" href="' + city.getFacebook() + '" target="_blank">facebook</a></div>';
        socialMediaLinksBox.html(html);

        $('#' + this.pageIdentifier + ' #citySocialMediaLinksFacebookButton').button();
    }

    if (city.getTwitter() != '')
    {
        var html = socialMediaLinksBox.html();
        html += '<div class="ui-block-' + String.fromCharCode(++char) + '"><a id="citySocialMediaLinksTwitterButton" href="' + city.getTwitter() + '" target="_blank">twitter</a></div>';
        socialMediaLinksBox.html(html);

        $('#' + this.pageIdentifier + ' #citySocialMediaLinksTwitterButton').button();
    }

}