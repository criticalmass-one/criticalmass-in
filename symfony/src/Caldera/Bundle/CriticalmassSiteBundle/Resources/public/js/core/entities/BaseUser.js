BaseUser = function()
{

};

BaseUser.prototype.id = null;
BaseUser.prototype.username = null;
BaseUser.prototype.gravatarHash = null;
BaseUser.prototype.colorRed = null;
BaseUser.prototype.colorGreen = null;
BaseUser.prototype.colorBlue = null;

BaseUser.prototype.getId = function()
{
    return this.id;
};

BaseUser.prototype.setId = function(id)
{
    this.id = id;
};

BaseUser.prototype.getUsername = function()
{
    return this.username;
};

BaseUser.prototype.setUsername = function(username)
{
    this.username = username;
};

BaseUser.prototype.getGravatarHash = function()
{
    return this.gravatarHash;
};

BaseUser.prototype.setGravatarHash = function(gravatarHash)
{
    this.gravatarHash = gravatarHash;
};

BaseUser.prototype.getColorRed = function()
{
    return this.colorRed;
};

BaseUser.prototype.setColorRed = function(colorRed)
{
    this.colorRed = colorRed;
};

BaseUser.prototype.getColorGreen = function()
{
    return this.colorGreen;
};

BaseUser.prototype.setColorGreen = function(colorGreen)
{
    this.colorGreen = colorGreen;
};

BaseUser.prototype.getColorBlue = function()
{
    return this.colorBlue;
};

BaseUser.prototype.setColorBlue = function(colorBlue)
{
    this.colorBlue = colorBlue;
};