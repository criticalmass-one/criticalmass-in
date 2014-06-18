UrlFactory = function()
{

}

UrlFactory.environment = 'dev';
UrlFactory.system = 'local';
UrlFactory.tls = true;

UrlFactory.nodeJSPort = 1337;

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
        return 'www.criticalmass.cm';
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

UrlFactory.getNodeJSPortNumber = function()
{
    return this.nodeJSPort;
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

UrlFactory.getUrlPrefix = function()
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

UrlFactory.getNodeJSApiPrefix = function()
{
    return 'https://criticalmass.in:1337/';
}