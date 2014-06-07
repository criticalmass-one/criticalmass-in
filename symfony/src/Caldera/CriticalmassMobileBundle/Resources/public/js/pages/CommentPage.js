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
};

CommentPage.prototype.createComment = function(commentData)
{
    var commentList = $('#commentList');

    var commentHTML = '<li class="commentItem">';
    commentHTML += '<img src="http://www.gravatar.com/avatar/' + commentData.gravatar + '" />';
    commentHTML += '<strong>' + commentData.username + '</strong> schrieb:';

    var dateTime = new Date(commentData.dateTime);

    commentHTML += '<time>(' + (dateTime.getHours() < 10 ? '0' + dateTime.getHours() : dateTime.getHours()) + '.' + (dateTime.getMinutes() < 10 ? '0' + dateTime.getMinutes() : dateTime.getMinutes()) + ' Uhr, ' + (dateTime.getDate() < 10 ? '0' + dateTime.getDate() : dateTime.getDate())  + '.' + ((dateTime.getMonth() + 1) < 10 ? '0' + (dateTime.getMonth() + 1) : (dateTime.getMonth() + 1)) + '.' + dateTime.getFullYear() + ')</time>';
    commentHTML += '<p>' + commentData.message + '</p>';
    commentHTML += '</li>';

    commentList.prepend(commentHTML);
    commentList.listview('refresh');
};

CommentPage.prototype.drawMessages = function()
{
    function callback(ajaxResultData)
    {
        for (index in ajaxResultData)
        {
            if (!this.commentsArray[ajaxResultData[index].id])
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