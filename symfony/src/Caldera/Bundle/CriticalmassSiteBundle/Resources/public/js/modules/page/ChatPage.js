define(['socketio', 'dateformat'], function(io) {
    ChatPage = function() {

    };

    ChatPage.prototype._userToken = null;
    ChatPage.prototype._nameId = null;
    ChatPage.prototype._socket = null;

    ChatPage.prototype.setUserToken = function(userToken) {
        this._userToken = userToken;
    };

    ChatPage.prototype.setAnonymousNameId = function(nameId) {
        this._nameId = nameId;
    };

    ChatPage.prototype.startChat = function() {
        this._initSocket();

        $('form').submit(this._submitMessage.bind(this));

        this._socket.on('message', this._printMessage.bind(this));
    };

    ChatPage.prototype._initSocket = function() {
        this._socket = io('http://criticalmass.cm:3000');
    };

    ChatPage.prototype._submitMessage = function(e) {
        e.preventDefault();

        var message = this._buildMessage();

        this._socket.emit('message', message);

        $('#m').val('');

        return false;
    };

    ChatPage.prototype._printMessage = function(message) {
        var date = new Date(message.timestamp);

        var html = '';
        html += '<li>';
        html += '<div class="media">';
        html += '<div class="media-left"><a href="#"><img class="media-object" src="http://www.gravatar.com/avatar/205e460b479e2e5b48aec07710c08d50?s=64" alt="..."></a></div>';
        html += '<div class="media-body">';
        html += '<h4 class="media-heading" style="color: ' + message.userColor + ';">' + message.username + '</h4>';
        html += message.message;
        html += '<small style="display: block;">' + date.format('dd.mm.yyyy HH:MM:ss')  + '&nbsp;Uhr</small>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</li>';

        $('#messages').prepend(html);
    };





    ChatPage.prototype._buildMessage = function() {
        return {
            userToken: this._userToken || '',
            nameId: this._nameId,
            message: $('#m').val()
        };
    };

    return ChatPage;
});