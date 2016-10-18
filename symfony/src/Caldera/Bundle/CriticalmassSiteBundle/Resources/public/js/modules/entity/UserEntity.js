define(['BaseEntity'], function () {
    UserEntity = function () {
    };

    UserEntity.prototype = new BaseEntity();
    UserEntity.prototype.constructor = UserEntity;

    UserEntity.prototype._id = null;
    UserEntity.prototype._username = null;
    UserEntity.prototype._colorRed = null;
    UserEntity.prototype._colorGreen = null;
    UserEntity.prototype._colorBlue = null;
    UserEntity.prototype._gravatarHash = null;

    UserEntity.prototype.getUsername = function () {
        return this._username;
    };

    UserEntity.prototype.getGravatarHash = function () {
        return this._gravatarHash;
    };

    UserEntity.prototype.getGravatarUrl = function () {
        return 'https://www.gravatar.com/avatar/' + this._gravatarHash;
    };

    return UserEntity;
});