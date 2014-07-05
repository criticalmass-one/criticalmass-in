Messages = function(map)
{
    this.map = map;
};

Messages.prototype.map = null;

Messages.prototype.commentsArray = [];

Messages.prototype.startLoop = function()
{
    this.drawMessages();

    var this2 = this;
    this.timer = window.setInterval(function()
    {
        this2.drawMessages();
    }, 5000);
};

Messages.prototype.createComment = function(commentData)
{
    var criticalmassIcon = L.icon({
        iconUrl: '/bundles/calderacriticalmasscore/images/marker/criticalmassblue.png',
        iconSize: [25, 41],
        iconAnchor: [13, 41],
        popupAnchor: [0, -36],
        shadowUrl: '/bundles/calderacriticalmasscore/images/marker/defaultshadow.png',
        shadowSize: [41, 41],
        shadowAnchor: [13, 41]
    });

    var marker = L.marker([commentData.latitude, commentData.longitude], { icon: criticalmassIcon });

    var popupHTML = '<div class="messagePopup" id="messagePopup' + commentData.commentId + '">';
    popupHTML += '<img src="https://www.gravatar.com/avatar/' + commentData.gravatar + '?s=32" />';
    popupHTML += '<strong>' + commentData.username + '</strong> schrieb:';

    var dateTime = new Date(commentData.dateTime);

    popupHTML += '<time>(' + (dateTime.getHours() < 10 ? '0' + dateTime.getHours() : dateTime.getHours()) + '.' + (dateTime.getMinutes() < 10 ? '0' + dateTime.getMinutes() : dateTime.getMinutes()) + ' Uhr, ' + (dateTime.getDate() < 10 ? '0' + dateTime.getDate() : dateTime.getDate())  + '.' + ((dateTime.getMonth() + 1) < 10 ? '0' + (dateTime.getMonth() + 1) : (dateTime.getMonth() + 1)) + '.' + dateTime.getFullYear() + ')</time>';
    popupHTML += '<p>' + commentData.message + '</p>';
    popupHTML += '</div>';

    marker.bindPopup(popupHTML);
    marker.addTo(this.map.map);

    this.commentsArray[commentData.commentId] = marker;
};

Messages.prototype.drawMessages = function()
{
    var this2 = this;

    function callback(ajaxResultData)
    {
        for (index in ajaxResultData)
        {
            if (!this.commentsArray[ajaxResultData[index].commentId])
            {
                this.createComment(ajaxResultData[index]);
            }
        }

        CallbackHell.executeEventListener('messagesDrawnAtMap', this2);
    }

    $.support.cors = true;
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: Url.getNodeJSApiPrefix() + '?action=fetchComments&citySlug=' + this.map.parentPage.getCitySlug(),
        cache: false,
        context: this,
        crossDomain: true,
        success: callback
    });
};

Messages.prototype.openPopup = function(commentId)
{
    this.commentsArray[commentId].openPopup();

    _paq.push(['trackEvent', 'commentPopup', 'open']);
};