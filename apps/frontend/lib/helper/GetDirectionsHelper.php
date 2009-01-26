<?php

function gmaps_direction_link($from_lat, $from_lon, $to_lat, $to_lon, $to_name, $lang)
{
    $baseurl = "http://maps.google.com/maps";
    $from_code = (empty($from_lat) || empty($from_lon)) ? '' : "$from_lat,$from_lon";
    $to_code = "$to_lat,$to_lon" . (empty($to_name) ? '' : "+($to_name)");
    $lang_code = "&hl=$lang&ie=UTF8";
    $zoom_code = empty($from_code) ? "&z=12" : '';

   return "$baseurl?f=d&source=s_d&saddr=$from_code&daddr=$to_code$lang_code$zoom_code";
}
