CityFactory = function()
{

}

CityFactory.prototype.getCityBySlug = function(citySlugString)
{
    if (citySlugString == 'hamburg')
    {
        return new City('hamburg');
    }

    if (citySlugString == 'wedel')
    {
        return new City('wedel');
    }
}