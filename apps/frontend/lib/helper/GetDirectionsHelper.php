<?php

function gmaps_direction_link($from_lat, $from_lng, $to_lat, $to_lng, $to_name, $lang)
{
    return "http://maps.google.com/maps?f=d&source=s_d&saddr=$from_lat,$from_lng&daddr=$to_lat,$to_lng"
           . (empty($to_name) ? '' : "+($to_name)")
           . "&hl=$lang&ie=UTF8";
}
