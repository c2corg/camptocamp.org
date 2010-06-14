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

    // TODO icons, polylines and shapes (?), section folding, extra objects (see routes)
    $map_url = 'http://maps.google.com/maps/api/staticmap?size=300x300&amp;maptype=terrain&amp;';
    $map_options = array();

    
    $map_options[] = 'center='.$document['lon'].','.$document['lat']; // TODO this won't work for everything (just temporary)
    $map_options[] = 'zoom=14';
    $map_options[] = 'sensor=false';

    $map_url .= implode('&amp;', $map_options);
    echo '<img src="'.$map_url.'" />';

    echo end_section_tag(true);
}
