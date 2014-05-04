App = function()
{

};

App.prototype.citySlug = 'Hamburg';

App.prototype.setCitySlug = function(newCitySlug)
{
    this.citySlug = newCitySlug;
}

App.prototype.getCitySlug = function()
{
    return this.citySlug;
}


App.prototype.environment = 'dev';

App.prototype.setEnvironment = function(newEnvironment)
{
    this.environment = newEnvironment;
}

App.prototype.getEnvironment = function()
{
    return this.environment;
}


App.prototype.system = 'local';

App.prototype.setSystem = function(newSystem)
{
    this.system = newSystem;
}

App.prototype.getSystem = function()
{
    return this.system;
}


App.prototype.getUrlPrefix = function()
{
    var urlPrefix;

    if (this.getSystem() == 'local')
    {
        urlPrefix = 'http://www.criticalmass.local/';
    }

    if (this.getEnvironment() == 'dev')
    {
        urlPrefix += 'app_dev.php/';
    }

    return urlPrefix;
}