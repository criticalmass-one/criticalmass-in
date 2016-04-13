define(['socketio'], function(io) {
    ChatPage = function() {
        var socket = io('http://criticalmass.cm:3000');
        $('form').submit(function(){
            socket.emit('chat message', $('#m').val());
            $('#m').val('');
            return false;
        });

        socket.on('chat message', function(msg){
            $('#messages').append($('<li>').text(msg));
        });
    };

    return ChatPage;
});