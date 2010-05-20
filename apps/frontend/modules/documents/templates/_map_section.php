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
    if (!isset($home_section))
    {
        $home_section = false;
    }
    if (!isset($section_title))
    {
        $section_title = 'Interactive map';
    }
    if (!isset($show_tip))
    {
        $show_tip = true;
    }
    
    if ($home_section)
    {
        include_partial('documents/home_section_title',
                        array('module'            => 'maps',
                              'home_section'      => false,
                              'custom_title_text' => __($section_title),
                              'has_title_link'    => false,
                              'custom_section_id' => 'map_container'));
    }
    else
    {
        echo start_section_tag($section_title, 'map_container', 'opened', true, false, false, $show_tip);
    }
    
    if (!empty($help_text))
    {
        echo __($help_text);
    }
    
    use_helper('Map');
    if (!isset($layers_list))
    {
        $layers_list = null;
    }
    if (!isset($height))
    {
        $height = null;
    }
    if (!isset($center))
    {
        $center = null;
    }
    echo show_map('map_container', $document, $sf_user->getCulture(), $layers_list, $height, $center, $has_geom);
    echo end_section_tag(true);
}
