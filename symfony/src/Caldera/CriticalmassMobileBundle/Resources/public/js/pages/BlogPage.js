BlogPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;
};

BlogPage.prototype = new AppPage();
BlogPage.prototype.constructor = BlogPage;

BlogPage.prototype.initPage = function()
{
    alert('foobarbaz');
};