PageDispatcher = function()
{

};

PageDispatcher.pageArray = Array();
PageDispatcher.waitingCallback = null;

PageDispatcher.registerPageSwitchCallback = function()
{
    var this2 = this;

    $(':mobile-pagecontainer').on('pagecontainershow', function(event, ui)
    {
        _paq.push(['trackEvent', 'pageSwitch', this2.getCurrentPageId()]);
        this2.handlePageSwitch();
    });
};

PageDispatcher.handlePageSwitch = function()
{
    this.initPage(this.getCurrentPageId());
};

PageDispatcher.handleCurrentPage = function()
{
    this.initPage(this.getCurrentPageId());
};

PageDispatcher.initPage = function(pagename)
{
    if (!this.pageArray[pagename])
    {
        var callback = this.waitingCallback;

        switch (pagename)
        {
            case 'mapPage':
                var mapPage = new MapPage('mapPage');
                mapPage.initPage(callback);
                this.pageArray['mapPage'] = mapPage;
                break;
            case 'startPage':
                var startPage = new StartPage('startPage');
                startPage.initPage(callback);
                this.pageArray['startPage'] = startPage;
                break;
            case 'loginPage':
                var loginPage = new LoginPage('loginPage');
                loginPage.initPage(callback);
                this.pageArray['loginPage'] = loginPage;
                break;
            case 'registerPage':
                var registerPage = new RegisterPage('registerPage');
                registerPage.initPage(callback);
                this.pageArray['registerPage'] = registerPage;
                break;
            case 'cityPage':
                var cityPage = new CityPage('cityPage');
                cityPage.initPage(callback);
                this.pageArray['cityPage'] = cityPage;
                break;
            case 'colorPage':
                var colorPage = new ColorPage('colorPage');
                colorPage.initPage(callback);
                this.pageArray['colorPage'] = colorPage;
                break;
            case 'commentPage':
                var commentPage = new CommentPage('commentPage');
                commentPage.initPage(callback);
                this.pageArray['commentPage'] = commentPage;
                break;
            case 'tokenPage':
                var tokenPage = new TokenPage('tokenPage');
                tokenPage.initPage(callback);
                this.pageArray['tokenPage'] = tokenPage;
                break;
        }
    }
    else
    {
        this.waitingCallback(this.pageArray[pagename]);
    }

    this.waitingCallback = null;
};

PageDispatcher.getPage = function(pageId)
{
    return this.pageArray[pageId];
};

PageDispatcher.getCurrentPageId = function()
{
    return $(':mobile-pagecontainer').pagecontainer('getActivePage')[0].id;
};

PageDispatcher.switchPage = function(newPageId, callback)
{
    if (callback)
    {
        this.waitingCallback = callback;
    }

    $(':mobile-pagecontainer').pagecontainer('change', '#' + newPageId);
};