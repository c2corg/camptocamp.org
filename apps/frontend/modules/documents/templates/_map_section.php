<?php
$has_geom = (boolean)($document->get('geom_wkt'));
if (!$has_geom && $document->module == 'routes')
{
    foreach (array('summits', 'parkings', 'huts') as $type)
    {
        foreach ($document->$type as $associated_doc)
        {
            if ($associated_doc['pointwkt'] != null)
            {
                $has_geom = true;
                break 2;
            }
        }    
    }
}

if ($has_geom)
{
    echo start_section_tag('Interactive map', 'map_container', 'opened', true);
    use_helper('Map'); 
    echo show_map('map_container', $document, $sf_user->getCulture());
    echo end_section_tag(true);
}