<?php

/**
 * We use an url shortener service
 * TODO hard coded url...
 */
function _marker_url($module)
{
    switch ($module)
    {
        case 'summits':  $url = 'http://bit.ly/bFwxuy'; break; // http://www.camptocamp.org/static/images/modules/summits_mini.png
        case 'sites':    $url = 'http://bit.ly/cgrmJU'; break; // http://www.camptocamp.org/static/images/modules/sites_mini.png
        case 'parkings': $url = 'http://bit.ly/bY0O5n'; break; // http://www.camptocamp.org/static/images/modules/parkings_mini.png
        case 'huts':     $url = 'http://bit.ly/aVdVPn'; break; // http://www.camptocamp.org/static/images/modules/huts_mini.png
        case 'products': $url = 'http://bit.ly/bd3q9a'; break; // http://www.camptocamp.org/static/images/modules/products_mini.png
        case 'images':   $url = 'http://bit.ly/c4cept'; break; // http://www.camptocamp.org/static/images/modules/images_mini.png
        case 'users':    $url = 'http://bit.ly/asXBXR'; break; // http://www.camptocamp.org/static/images/modules/users_mini.png
        default:         $url = ''; break;
    }
    return urlencode($url);
}

/**
 * Encode a geometry obtained vi gisQuery::getEWKT into an encodedpolyline
 * http://code.google.com/apis/maps/documentation/polylinealgorithm.html
 */
function _polyline_encode($geom)
{
    $points = explode(',', $geom);
    foreach ($points as $key => $point)
    {
        $coords = explode(' ', $point);
        $points[$key] = array(floatval($coords[1]), floatval($coords[0]));
    }

    $encoder = new PolylineEncoder();
    return $encoder->encode($points)->rawPoints;
}

/**
 * empirical function to determine which tolerance to apply
 * to simplify for a geometry, given the box2d
 */
function _compute_tolerance($box2d)
{
    $bounds = explode(' ', str_replace(',', ' ', $box2d));
    $max = max(abs(intval($bounds[2])-intval($bounds[0])), abs(intval($bounds[3])-intval($bounds[1])));
    return intval($max * 1.5 / 100);
}
