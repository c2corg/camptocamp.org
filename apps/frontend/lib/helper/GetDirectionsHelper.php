<?php

function gmaps_direction_link($from_lat, $from_lon, $to_lat, $to_lon, $to_name, $lang)
{
    $baseurl = 'http://maps.google.com/maps';
    $from_code = (empty($from_lat) || empty($from_lon)) ? '' : "$from_lat,$from_lon";
    $to_code = "$to_lat,$to_lon" . (empty($to_name) ? '' : '+%28'.strtr($to_name, array('%28'=>'%5B', '%29'=>'%5D')).'%29');
    $lang_code = "&hl=$lang&ie=UTF8";
    $zoom_code = empty($from_code) ? "&z=12" : '';

    return "$baseurl?f=d&source=s_d&saddr=$from_code&daddr=$to_code$lang_code$zoom_code";
}

function osrm_direction_link($from_lat, $from_lon, $to_lat, $to_lon, $lang)
{
    $baseurl = "http://map.project-osrm.org/";
    $lang_code = "?hl=$lang";
    $from_code = (empty($from_lat) || empty($from_lon)) ? '' : "&loc=$from_lat,$from_lon";
    $to_code = "&loc=$to_lat,$to_lon";

    return "$baseurl$lang_code$from_code$to_code";
}   

function yahoo_maps_direction_link($from_lat, $from_lon, $to_lat, $to_lon, $lang)
{
    switch ($lang)
    {
        case 'fr': $baseurl = 'http://fr.maps.yahoo.com/broadband'; break;
        case 'ca':
        case 'es': $baseurl = 'http://espanol.maps.yahoo.com/broadband'; break;
        case 'de': $baseurl = 'http://de.routenplaner.yahoo.com/broadband'; break;
        case 'eu':
        case 'it':
        case 'en':
        default  : $baseurl = 'http://maps.yahoo.com/broadband'; break;
    }
    $from_code = (empty($from_lat) || empty($from_lon)) ? '' : "$from_lat,$from_lon";
    $to_code = "$to_lat,$to_lon";

    return "$baseurl?q1=$from_code&q2=$to_code";
}

/** see http://www.viawindowslive.com/Resources/VirtualEarth/BuildyourownURL.aspx */
function live_search_maps_direction_link($from_lat, $from_lon, $to_lat, $to_lon, $to_name)
{
    $baseurl = 'http://maps.live.com/default.aspx';
    $from_code = (empty($from_lat) || empty($from_lon)) ? '' : "pos.${from_lat}_${from_lon}";
    $to_code = "pos.${to_lat}_${to_lon}" . (empty($to_name) ? '' : "_${to_name}");
    $zoom_code = empty($from_code) ? "&lvl=12&cp=${to_lat}_${to_lon}" : '';

    return "$baseurl?rtp=$from_code~$to_code$zoom_code";
}

function openmapquest_direction_link($from_lat, $from_lon, $to_lat, $to_lon, $lang)
{
    // see http://www.mapquestapi.com/link-to-mapquest/#parameters
    switch ($lang)
    {
        case 'fr': $baseurl = 'http://open.mapquest.fr'; break;
        case 'ca':
        case 'es': $baseurl = 'http://open.mapquest.es'; break;
        case 'de': $baseurl = 'http://open.mapquest.de'; break;
        case 'it': $baseurl = 'http://open.mapquest.it'; break;
        case 'eu':
        case 'en':
        default  : $baseurl = 'http://open.mapquest.co.uk/'; break;
    }
    $from_code = (empty($from_lat) || empty($from_lon)) ? '' : "saddr=$from_lat,$from_lon&";
    $to_code = "daddr=$to_lat,$to_lon";

    return "$baseurl?$from_code$to_code&maptype=map&vs=directions";
}
