City = function(citySlug)
{
    if (citySlug)
    {
        this.citySlug = citySlug;
    }
};

City.prototype.citySlug = null;

City.prototype.getCitySlug = function()
{
    return this.citySlug;
};

City.prototype.setCitySlug = function(citySlug)
{
    this.citySlug = citySlug;
};