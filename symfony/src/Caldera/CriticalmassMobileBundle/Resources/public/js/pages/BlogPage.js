BlogPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;
};

BlogPage.prototype = new AppPage();
BlogPage.prototype.constructor = BlogPage;

BlogPage.prototype.initPage = function()
{
    function callback()
    {
        var articleList = ArticleFactory.getArticles();

        for (var index in articleList)
        {
            var article = articleList[index];

            var html = '<li id="blogArticle' + article.getId() + '" class="blogArticle ui-corner-all custom-corners">';
            html += '<div class="ui-bar ui-bar-a"><h3>' + article.getTitle() + '</h3></div>';
            html += '<div class="ui-body ui-body-a">';
            html += '<img class="gravatarIcon" src="https://www.gravatar.com/avatar/' + article.getGravatar() + '?s=42" />';
            html += '<span class="author">von <strong class="username">' + article.getUsername() + '</strong><br /><time>' + article.getFormattedDateTime() + '</time></span>';
            html += '<p>' + article.getFormattedText() + '</p></div>';
            html += '</li>';

            $('ul#blogArticleList').prepend(html);
        }
    }

    CallbackHell.registerEventListener('articleListRefreshed', callback);
    ArticleFactory.refreshAllStoredArticles();
};