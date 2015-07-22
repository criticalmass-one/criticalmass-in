Url = function()
{

};

Url.environment = 'dev';
Url.system = 'local';
Url.tls = false;

Url.nodeJSPort = 1337;

Url.getEnvironment = function()
{
    return this.environment;
};

Url.getSystem = function()
{
    return this.system;
};

Url.getHostName = function()
{
    return window.location.host;
};

Url.getPortNumber = function()
{
    if (this.tls)
    {
        return 443;
    }

    return 80;
};

Url.getNodeJSPortNumber = function()
{
    return this.nodeJSPort;
};

Url.getProtocolString = function()
{
    if (this.tls)
    {
        return 'https://';
    }

    return 'http://';
};

Url.getEnvironmentString = function()
{
    if (window.location.host == 'criticalmass.in') or (window.location.host == 'beta.criticalmass.in')
    {
        return 'app_dev.php/';
    }

    return '';
};

Url.getUrlPrefix = function()
{
    return this.getProtocolString() +
        this.getHostName() +
        ':' + this.getPortNumber() +
        '/' + this.getEnvironmentString();
};

Url.getApiPrefix = function()
{
    return this.getUrlPrefix() + 'api/';
};

Url.getNodeJSApiPrefix = function()
{
    return this.getProtocolString() + this.getHostName() + ':' + this.getNodeJSPortNumber() + '/';
};

Url.fileExists = function(fileUrl)
{
    var http = new XMLHttpRequest();
    http.open('HEAD', fileUrl, false);
    http.send();
    return http.status!=404;
};