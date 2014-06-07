Messages = function(map)
{
    this.map = map;
};

Messages.prototype.map = null;

Messages.prototype.drawMessages = function()
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

    var marker = L.marker([53.568376607724424, 9.968891143798828], { icon: criticalmassIcon }).addTo(this.map.map);

    var popupHTML = '<div class="messagePopup">';
    popupHTML += '<img src="http://www.gravatar.com/avatar/0cf33267893fa9eb18e8b227dcb05a65?s=32" />';
    popupHTML += '<strong>maltehuebner</strong> schrieb:';
    popupHTML += '<time>(20.08 Uhr, 30. Mai 2014)</time>'
    popupHTML += '<p>Es hat schon wieder jemand meine Kartoffelchips schnabuliert.</p>';
    popupHTML += '</div>';

    marker.bindPopup(popupHTML);
}