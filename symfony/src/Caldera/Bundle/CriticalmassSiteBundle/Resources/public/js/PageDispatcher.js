PageDispatcher = function()
{

};

PageDispatcher.pageArray = new Array();
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
        _paq.push(['trackEvent', 'initPage', pagename]);

        switch (pagename)
        {
            case 'mapPage':
                var mapPage = new MapPage('mapPage');
                mapPage.initPage();
                this.pageArray['mapPage'] = mapPage;
                break;
            case 'startPage':
                var startPage = new StartPage('startPage');
                startPage.initPage();
                this.pageArray['startPage'] = startPage;
                break;
            case 'loginPage':
                var loginPage = new LoginPage('loginPage');
                loginPage.initPage();
                this.pageArray['loginPage'] = loginPage;
                break;
            case 'registerPage':
                var registerPage = new RegisterPage('registerPage');
                registerPage.initPage();
                this.pageArray['registerPage'] = registerPage;
                break;
            case 'cityPage':
                var cityPage = new CityPage('cityPage');
                cityPage.initPage();
                this.pageArray['cityPage'] = cityPage;
                break;
            case 'colorPage':
                var colorPage = new ColorPage('colorPage');
                colorPage.initPage();
                this.pageArray['colorPage'] = colorPage;
                break;
            case 'commentPage':
                var commentPage = new CommentPage('commentPage');
                commentPage.initPage();
                this.pageArray['commentPage'] = commentPage;
                break;
            case 'blogPage':
                var blogPage = new BlogPage('blogPage');
                blogPage.initPage();
                this.pageArray['blogPage'] = blogPage;
                break;
            case 'glympsePage':
                var glympsePage = new GlympsePage('glympsePage');
                glympsePage.initPage();
                this.pageArray['glympsePage'] = glympsePage;
                break;
        }
    }
};

PageDispatcher.getPage = function(pageId)
{
    return this.pageArray[pageId];
};

PageDispatcher.isPageInitialized = function(pageId)
{
    return this.pageArray[pageId] != null;
};

PageDispatcher.getCurrentPageId = function()
{
    return $(':mobile-pagecontainer').pagecontainer('getActivePage')[0].id;
};

PageDispatcher.switchPage = function(newPageId)
{
    $(':mobile-pagecontainer').pagecontainer('change', '#' + newPageId);

    CallbackHell.executeEventListener('pageSwitch', this.pageArray[newPageId]);
};