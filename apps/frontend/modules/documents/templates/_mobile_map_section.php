<?php
use_helper('Mobile');

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
        $section_title = 'Static map';
    }
    
    echo start_section_tag($section_title, 'map_container', 'opened', true, false, false, false);
    echo '<div class="section" id="map_container_section_container">';

    $map_url = 'http://maps.googleapis.com/maps/api/staticmap?size=310x310&amp;maptype=terrain&amp;mobile=true&amp;sensor=false&amp;key='
               . sfConfig::get('app_google_api_key') . '&amp;';

    $map_options = array();

    $module = $document->module;
    if ($module == 'summits' || $module == 'parkings' || $module == 'sites' ||
        $module == 'huts' || $module == 'products' || $module == 'users' || $module == 'images')
    {
        $map_options[] = 'markers=shadow:false|icon:'._marker_url($module).'|'.$document['lat'].','.$document['lon'];
        $map_options[] = 'zoom=12';
    }
    elseif ($document->get('geom_wkt') && ($module == 'outings' || $module == 'routes'))
    {
        $tolerance = _compute_tolerance(gisQuery::getBox2d($document->id, $module), true);
        $geoms =  explode('),(', gisQuery::getEWKT($document->id, true, $module, null, $tolerance));
        foreach($geoms as $geom)
        {
            $map_options[] = 'path=weight:2|color:0xffff00cc|enc:'.
                _polyline_encode(str_replace(array('(', ')'), '', $geom));
        }
    }
    elseif ($document->get('geom_wkt') && ($module == 'maps' || $module == 'areas'))
    {
        // we cannot use donut geometries, so we instead create multiple lines
        $tolerance = _compute_tolerance(gisQuery::getBox2d($document->id, $module));
        $geoms = gisQuery::getEWKT($document->id, true, $module, null, $tolerance);
        $geoms = explode('),(', $geoms);
        foreach($geoms as $geom) {
            $map_options[] = 'path=weight:2|color:0xff0000cc|enc:'.
                             _polyline_encode(str_replace(array('(', ')'), '', $geom));
        }
    }

    // display linked summits, parkings and huts, if any
    $markers = array();
    $nb_printed_docs = 0;
    foreach(array($document->parkings, $document->summits, $document->huts) as $docs)
    {
        if (!isset($docs)) continue;

        foreach($docs as $doc)
        {
            if (!empty($doc['pointwkt']))
            {
                switch ($doc['module'])
                {
                    case 'parkings':
                        $marker = _marker_url('parkings');
                        break;
                    case 'summits':
                        $marker = _marker_url('summits', $doc['summit_type']);
                        break;
                    case 'huts':
                        $marker = _marker_url('huts', $doc['shelter_type']);
                        break;
                }

                if (!isset($markers[$marker])) $markers[$marker] = [];
                $nb_printed_docs++;
                array_push($markers[$marker], sprintf('%6f,%6f', floatval($doc['lat']), floatval($doc['lon'])));
            }
        }
    }

    foreach($markers as $icon => $items)
    {
        $map_options[] = 'markers=shadow:false|icon:'.$icon.'|'.implode('|', $items);
    }

    // if only one linked doc is displayed, without any trace, set zoom
    if ($nb_printed_docs <= 1 && !(boolean)($document->get('geom_wkt')))
    {
        $map_options[] = 'zoom=12';
    }

    $map_url .= implode('&amp;', $map_options);
    echo image_tag($map_url, array('alt' => __('map')));

    echo '</div>', end_section_tag(true);
    $cookie_position = array_search('map_container', sfConfig::get('app_personalization_cookie_fold_positions'));
    echo javascript_tag('C2C.setSectionStatus(\'map_container\', '.$cookie_position.', true);');
}
