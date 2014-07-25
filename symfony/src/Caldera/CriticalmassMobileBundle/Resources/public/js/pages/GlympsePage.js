GlympsePage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;
};

GlympsePage.prototype = new AppPage();

GlympsePage.prototype.constructor = GlympsePage;

GlympsePage.prototype.initPage = function()
{
    console.log(this.getCitySlug() + '@criticalmass.in');
    $('#glympseInviteEmailAddress').html(this.getCitySlug() + '@criticalmass.in');
};