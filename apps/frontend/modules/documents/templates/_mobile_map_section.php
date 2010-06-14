<?php

if (!isset($has_geom))
{
    $has_geom = (boolean)($document->get('geom_wkt'));
}
if (!$has_geom && $document->module == 'routes')
{
    foreach (array('summits', 'parkings', 'huts') as $type)
    {
        if (!isset($document->$type)) continue;
        foreach ($document->$type as $associated_doc)
        {
            if (!empty($associated_doc['pointwkt']))
            {
                $has_geom = true;
                break 2;
            }
        }
    }
}

if (!isset($show_map))
{
    $show_map = false;
}
if ($has_geom || $show_map)
{
    if (!isset($section_title))
    {
        $section_title = 'Interactive map';
    }
    if (!isset($show_tip))
    {
        $show_tip = true;
    }
    
    if (!isset($layers_list))
    {
        $layers_list = null;
    }
    if (!isset($height))
    {
        $height = null;
    }
    if (isset($center))
    {
        $center = $sf_data->getRaw('center');
    }
    else
    {
        $center = null;
    }

    echo start_section_tag($section_title, 'map_container', 'opened', true, false, false, $show_tip);

    // TODO icons, polylines and shapes (?), section folding, cache, extra objects (see routes)
    $map_url = 'http://maps.google.com/maps/api/staticmap?size=300x300&amp;maptype=terrain&amp;mobile=true&amp;sensor=false&amp;';
    $map_options = array();

    
//    $map_options[] = 'center='.$document['lon'].','.$document['lat']; // TODO this won't work for everything (just temporary)
    $module = $document->module;
    if ($module == 'summits' || $module == 'parkings' || $module == 'sites' ||
        $module == 'huts' || $module == 'products' || $module == 'users' || $module == 'images')
    {
        $marker_url = urlencode('http://www.camptocamp.org/static/images/modules/'.$module.'_mini.png'); // TODO hard coded uri...+factorize
        $map_options[] = 'markers=shadow:false|icon:'.$marker_url.'|'.$document['lon'].','.$document['lat'];
        $map_options[] = 'zoom=14';
    }
    elseif ($module == 'outings' || $module == 'maps' || $module == 'routes' || $module == 'areas')
    {
        // TODO
    }

    // routes : display linked summits, parkings and huts
    if ($module == 'routes')
    {
        foreach(array('summits', 'parkings', 'huts') as $type)
        {
            if (!isset($document->$type)) continue;
            $marker_url = urlencode('http://www.camptocamp.org/static/images/modules/'.$type.'_mini.png'); // TODO hard coded uri...
            $markers = array();
            foreach ($document->$type as $doc)
            {
                if (!empty($doc['pointwkt']))
                {
                    $coords = explode(' ', gisQuery::getEWKT($doc['id'], true));
                    $markers[] = substr($coords[0], 0, 7).','.substr($coords[1], 0, 7); // TODO not ok
                }
            }
            if (count($markers))
            {
                $map_options[] = 'markers=shadow:false|icon:'.$marker_url.'|'.implode('|', $markers);
            }
        }
    }

    $map_url .= implode('&amp;', $map_options);
    echo image_tag($map_url, array('alt' => __('map')));

    echo end_section_tag(true);
}
