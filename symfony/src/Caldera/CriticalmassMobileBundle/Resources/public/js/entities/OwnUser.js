OwnUser = function()
{

};

OwnUser.prototype = new BaseUser();

OwnUser.prototype.constructor = OwnUser;

OwnUser.prototype.plus = null;

OwnUser.prototype.getPlus = function()
{
    return this.plus;
};

OwnUser.prototype.setPlus = function(plus)
{
    this.plus = plus;
};