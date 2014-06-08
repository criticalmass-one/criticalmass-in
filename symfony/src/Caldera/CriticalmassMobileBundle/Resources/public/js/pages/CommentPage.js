CommentPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;
};

CommentPage.prototype = new AppPage();

CommentPage.prototype.constructor = CommentPage;

CommentPage.prototype.pageIdentifier = null;

CommentPage.prototype.commentsArray = [];

CommentPage.prototype.initPage = function()
{
    this.drawMessages();

    var this2 = this;
    this.timer = window.setInterval(function()
    {
        this2.drawMessages();
    }, 5000);

    $('#submitCommentButton').on('click', function(element)
    {
        element.preventDefault();

        this2.submitComment();
    });
};

CommentPage.prototype.createComment = function(commentData)
{
    var commentList = $('#commentList');

    var commentHTML = '<li class="commentItem">';
    commentHTML += '<a href="#mapPage@' + commentData.latitude + ',' + commentData.longitude + ',13">';
    commentHTML += '<img src="http://www.gravatar.com/avatar/' + commentData.gravatar + '" />';
    commentHTML += '<strong>' + commentData.username + '</strong> schrieb:';

    var dateTime = new Date(commentData.dateTime);

    commentHTML += '<time>(' + (dateTime.getHours() < 10 ? '0' + dateTime.getHours() : dateTime.getHours()) + '.' + (dateTime.getMinutes() < 10 ? '0' + dateTime.getMinutes() : dateTime.getMinutes()) + ' Uhr, ' + (dateTime.getDate() < 10 ? '0' + dateTime.getDate() : dateTime.getDate())  + '.' + ((dateTime.getMonth() + 1) < 10 ? '0' + (dateTime.getMonth() + 1) : (dateTime.getMonth() + 1)) + '.' + dateTime.getFullYear() + ')</time>';
    commentHTML += '<p>' + commentData.message + '</p>';
    commentHTML += '</a>';
    commentHTML += '</li>';

    commentList.prepend(commentHTML);
    commentList.listview('refresh');

    this.commentsArray[commentData.commentId] = commentData;
};

CommentPage.prototype.drawMessages = function()
{
    function callback(ajaxResultData)
    {
        for (index in ajaxResultData)
        {
            if (!this.commentsArray[ajaxResultData[index].commentId])
            {
                this.createComment(ajaxResultData[index]);
            }
        }
    }

    $.support.cors = true;
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: UrlFactory.getNodeJSApiPrefix() + '?action=fetchComments&citySlug=' + this.getCitySlug(),
        cache: false,
        context: this,
        crossDomain: true,
        success: callback
    });
};

CommentPage.prototype.submitComment = function()
{
    var this2 = this;

    function callback(data)
    {
        alert(data);
    }

    function submit(commentData)
    {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: UrlFactory.getApiPrefix() + 'ride/writecomment',
            cache: false,
            context: this,
            data: commentData,
            success: callback
        });
    }

    if (navigator.geolocation)
    {
        function processError2(positionError)
        {
            var commentData = {
                citySlug: this2.getCitySlug(),
                message: $('#commentText').val(),
                latitude: 0,
                longitude: 0
            };

            submit(commentData);
        }

        function processPosition2(positionResult)
        {
            var commentData = {
                citySlug: this2.getCitySlug(),
                message: $('#commentText').val(),
                latitude: positionResult.coords.latitude,
                longitude: positionResult.coords.longitude
            };

            submit(commentData);
        }

        navigator.geolocation.watchPosition(processPosition2, processError2, { maximumAge: 15000, timeout: 5000, enableHighAccuracy: false });
    }
    else
    {
        var commentData = {
            citySlug: this2.getCitySlug(),
            message: $('#commentText').val(),
            latitude: 0,
            longitude: 0
        };

        submit(commentData);
    }


};