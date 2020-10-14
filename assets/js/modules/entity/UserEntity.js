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
    UserEntity.prototype._imageName = null;

    UserEntity.prototype.getUsername = function () {
        return this._username;
    };

    UserEntity.prototype.getProfilePhotoUrl = function () {
        return '/media/cache/resolve/user_profile_photo_timelapse/users/' + this._imageName;
    };

    return UserEntity;
});
