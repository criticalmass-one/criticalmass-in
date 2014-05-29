UrlFactory = function()
{

}

UrlFactory.environment = 'dev';
UrlFactory.system = 'local';
UrlFactory.tls = false;

UrlFactory.getEnvironment = function()
{
    return this.environment;
}

UrlFactory.getSystem = function()
{
    return this.system;
}

UrlFactory.getHostName = function()
{
    if (this.getSystem() == 'local')
    {
        return 'www.criticalmass.local';
    }

    return 'www.criticalmass.in';
}

UrlFactory.getPortNumber = function()
{
    if (this.tls)
    {
        return 443;
    }

    return 80;
}

UrlFactory.getProtocolString = function()
{
    if (this.tls)
    {
        return 'https://';
    }

    return 'http://';
}

UrlFactory.getEnvironmentString = function()
{
    if (this.getEnvironment() == 'dev')
    {
        return 'app_dev.php/';
    }

    return '';
}

UrlFactory.getUrlPrefix = function(ajaxResponseData)
{
    return this.getProtocolString() +
        this.getHostName() +
        ':' + this.getPortNumber() +
        '/' + this.getEnvironmentString();
}

UrlFactory.getApiPrefix = function()
{
    return this.getUrlPrefix() + 'api/';
}