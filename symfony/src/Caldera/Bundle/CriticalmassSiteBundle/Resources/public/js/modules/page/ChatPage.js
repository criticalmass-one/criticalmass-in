define(['socketio', 'dateformat'], function(io) {
    ChatPage = function() {

    };

    ChatPage.prototype._userToken = null;
    ChatPage.prototype._anonymousNameId = null;
    ChatPage.prototype._anonymousName = null;
    ChatPage.prototype._socket = null;

    ChatPage.prototype.setUserToken = function(userToken) {
        this._userToken = userToken;
    };

    ChatPage.prototype.setAnonymousNameId = function(anonymousNameId) {
        this._anonymousNameId = anonymousNameId;
    };

    ChatPage.prototype.startChat = function() {
        this._initSocket();

        this._initEvents();

        this._socket.on('message', this._printMessage.bind(this));
        this._socket.on('joined', this._memberJoined.bind(this));
    };

    ChatPage.prototype._initEvents = function() {
        $('form').submit(this._submitMessage.bind(this));

        var that = this;

        $('#chat-gender-buttons button').on('click', function(button) {
            var gender = $(this).data('gender');

            that._chooseGender(gender);
        });
    };
    
    ChatPage.prototype._chooseGender = function(gender) {
        var that = this;

        $.get('/app_dev.php/chat/anonymoususername?gender=' + gender, function(response) {
            that._anonymousNameId = response.anonymousNameId;
            that._anonymousName = response.anonymousName;

            that._join();
        });
    };
    

    ChatPage.prototype._initSocket = function() {
        this._socket = io('http://criticalmass.cm:3000');

        if (this._userToken) {
            this._join();
        }
    };

    ChatPage.prototype._join = function() {
        var joinMessage = {
            userToken: this._userToken || null,
            anonymousNameId: this._anonymousNameId || null
        };

        this._socket.emit('join', joinMessage);
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
        html += '<li class="margin-bottom-medium">';
        html += '<div class="media">';
        html += '<div class="media-left"><a href="#"><img class="media-object img-circle" src="http://www.gravatar.com/avatar/' + message.gravatarHash + '?s=64" alt="..."></a></div>';
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

    ChatPage.prototype._memberJoined = function(message) {
        $('#members').prepend('<li>' + message.username + '</li>');
    };

    ChatPage.prototype._memberLeft = function(message) {

    };
    
    ChatPage.prototype._buildMessage = function() {
        return {

            message: $('#m').val()
        };
    };

    return ChatPage;
});