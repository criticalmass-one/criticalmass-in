PageDispatcher = function()
{

};

PageDispatcher.pageArray = Array();

PageDispatcher.registerPageSwitchCallback = function()
{
    var this2 = this;

    $(':mobile-pagecontainer').on('pagecontainershow', function(event, ui)
    {
        this2.handlePageSwitch();
    });
}

PageDispatcher.handlePageSwitch = function()
{
    this.initPage(this.getCurrentPageId());
}

PageDispatcher.handleCurrentPage = function()
{
    this.initPage(this.getCurrentPageId());
}

PageDispatcher.initPage = function(pagename)
{
    switch (pagename)
    {
        case 'mapPage': var mapPage = new MapPage('mapPage');
                        mapPage.initPage();
                        this.pageArray['mapPage'] = mapPage;
                        break;
        case 'startPage': var startPage = new StartPage('startPage');
                        startPage.initPage();
                        this.pageArray['startPage'] = startPage;
                        break;
    }
}

PageDispatcher.getCurrentPageId = function()
{
    return $(':mobile-pagecontainer').pagecontainer('getActivePage')[0].id;
}

