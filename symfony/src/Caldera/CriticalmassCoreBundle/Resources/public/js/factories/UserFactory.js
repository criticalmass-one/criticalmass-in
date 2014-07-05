UserFactory = function()
{

};

UserFactory.convertObjectToUser = function(objectData)
{
    var user = new User();

    user.setId(objectData.id);
    user.setUsername(objectData.username);
    user.setGravatarHash(objectData.gravatarHash);
    user.setColorRed(objectData.colorRed);
    user.setColorGreen(objectData.colorGreen);
    user.setColorBlue(objectData.colorBlue);

    return user;
};

UserFactory.convertObjectToOwnUser = function(objectData)
{
    var ownUser = new OwnUser();

    ownUser.setId(objectData.id);
    ownUser.setUsername(objectData.username);
    ownUser.setGravatarHash(objectData.gravatarHash);
    ownUser.setColorRed(objectData.colorRed);
    ownUser.setColorGreen(objectData.colorGreen);
    ownUser.setColorBlue(objectData.colorBlue);
    ownUser.setPlus(objectData.plus);

    return ownUser;
};

UserFactory.saveOwnUserDataInStorage = function()
{
    $.ajax({
        type: 'GET',
        async: false,
        url: Url.getApiPrefix() + 'user/getownuserdata',
        cache: false,
        context: this,
        success: function(data)
        {
            localStorage.ownUser = this.convertObjectToOwnUser(data);
        }
    });
};