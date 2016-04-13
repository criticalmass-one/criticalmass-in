define(['socketio'], function(io) {
    ChatPage = function() {

    };

    ChatPage.prototype._userToken = null;

    ChatPage.prototype.setUserToken = function(userToken) {
        this._userToken = userToken;
    };

    ChatPage.prototype.startChat = function() {
        var socket = io('http://criticalmass.cm:3000');

        var that = this;

        $('form').submit(function() {
            var message = that._buildMessage();

            socket.emit('message', message);

            $('#m').val('');

            return false;
        });

        socket.on('message', function(msg){
            $('#messages').prepend($('<li>').text(msg.message));
        });
    };

    ChatPage.prototype._buildMessage = function() {
        return {
            userToken: this._userToken || '',
            message: $('#m').val()
        };
    };

    return ChatPage;
});