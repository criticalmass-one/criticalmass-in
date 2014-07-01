Article = function()
{
};

Article.prototype.id = null;
Article.prototype.username = null;
Article.prototype.gravatar = null;
Article.prototype.title = null;
Article.prototype.abstract = null;
Article.prototype.text = null;
Article.prototype.dateTime = null;

Article.prototype.getId = function()
{
    return this.id;
};

Article.prototype.setId = function(id)
{
    this.id = id;
};

Article.prototype.getUsername = function()
{
    return this.username;
};

Article.prototype.setUsername = function(username)
{
    this.username = username;
};

Article.prototype.getGravatar = function()
{
    return this.gravatar;
};

Article.prototype.setGravatar = function(gravatar)
{
    this.gravatar = gravatar;
};

Article.prototype.getTitle = function()
{
    return this.title;
};

Article.prototype.setTitle = function(title)
{
    this.title = title;
};

Article.prototype.getAbstract = function()
{
    return this.abstract;
};

Article.prototype.setAbstract = function(abstract)
{
    this.abstract = abstract;
};

Article.prototype.getText = function()
{
    return this.text;
};

Article.prototype.getFormattedText = function()
{
    return (this.text + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br />' + '$2');
}

Article.prototype.setText = function(text)
{
    this.text = text;
};

Article.prototype.getDateTime = function()
{
    return this.dateTime;
};

Article.prototype.getFormattedDateTime = function()
{
    var dateTime = new Date(this.dateTime);

    return (dateTime.getHours() < 10 ? '0' + dateTime.getHours() : dateTime.getHours()) + '.' +
           (dateTime.getMinutes() < 10 ? '0' + dateTime.getMinutes() : dateTime.getMinutes()) + ' Uhr, ' +
           (dateTime.getDate() < 10 ? '0' + dateTime.getDate() : dateTime.getDate())  + '.' +
           (dateTime.getMonth() + 1 < 10 ? '0' + (dateTime.getMonth() + 1) : (dateTime.getMonth() + 1)) + '.' +
           (dateTime.getFullYear());
};

Article.prototype.setDateTime = function(dateTime)
{
    this.dateTime = dateTime;
};