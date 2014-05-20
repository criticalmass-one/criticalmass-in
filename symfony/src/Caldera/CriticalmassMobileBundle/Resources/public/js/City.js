City = function()
{
};

City.prototype.id = null;
City.prototype.citySlug = null;
City.prototype.city = null;
City.prototype.title = null;
City.prototype.description = null;

City.prototype.getId = function()
{
    return this.id;
};

City.prototype.setId = function(id)
{
    this.id = id;
};

City.prototype.getCitySlug = function()
{
    return this.citySlug;
};

City.prototype.setCitySlug = function(citySlug)
{
    this.citySlug = citySlug;
};

City.prototype.getCity = function()
{
    return this.city;
};

City.prototype.setCity = function(city)
{
    this.city = city;
};

City.prototype.getTitle = function()
{
    return this.title;
};

City.prototype.setTitle = function(title)
{
    this.title = title;
};

City.prototype.getDescripton = function()
{
    return this.description;
};

City.prototype.setDescription = function(description)
{
    this.description = description;
};

City.prototype.parseAjaxResultData = function(ajaxResultData)
{
    this.setId(ajaxResultData.id);
    this.setCitySlug(ajaxResultData.slug);
    this.setCity(ajaxResultData.city);
    this.setTitle(ajaxResultData.title);
    this.setDescription(ajaxResultData.description);
}