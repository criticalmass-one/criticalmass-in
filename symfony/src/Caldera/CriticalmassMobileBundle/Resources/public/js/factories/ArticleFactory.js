ArticleFactory = function()
{

};

ArticleFactory.storage = sessionStorage.articleListData;

ArticleFactory.convertObjectToArticle = function(objectData)
{
    var article = new Article();

    article.setId(objectData.id);
    article.setUsername(objectData.username);
    article.setGravatar(objectData.gravatar);
    article.setTitle(objectData.title);
    article.setAbstract(objectData.abstract);
    article.setText(objectData.text);
    article.setDateTime(objectData.dateTime);

    return article;
};

ArticleFactory.getArticles = function()
{
    if (!this.storage)
    {
        return null;
    }

    var articleList = JSON.parse(this.storage);
    var resultArray = new Array();

    for (var index in articleList)
    {
        var dateTime = new Date(articleList[index].dateTime);
        resultArray[dateTime.getTime()] = this.convertObjectToArticle(articleList[index]);
    }

    return resultArray;
};

ArticleFactory.storeAllArticles = function()
{
    function callback(resultData)
    {
        this.storage = JSON.stringify(resultData);
        CallbackHell.executeEventListener('articleListRefreshed');
    }

    if (!this.storage || this.storage == null)
    {
        $.support.cors = true;
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: Url.getNodeJSApiPrefix() + '?action=fetchArticles',
            cache: false,
            context: this,
            crossDomain: true,
            success: callback
        });
    }
};

ArticleFactory.refreshAllStoredArticles = function()
{
    this.storage = null;
    this.storeAllArticles();
};