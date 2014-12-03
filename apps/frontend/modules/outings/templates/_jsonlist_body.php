<?php
use_helper('Field', 'SmartFormat', 'sfBBCode');

$item_i18n = $item->getRaw('OutingI18n');
$item_i18n = $item_i18n[0];

$a = sfConfig::get('app_activities_list');
$c = sfConfig::get('mod_outings_conditions_statuses_list');
$f = sfConfig::get('mod_outings_frequentation_statuses_list');

$properties = array(
    'module' => 'outings'
,   'name' => $item_i18n['name']
,   'url' => jsonlist_url($item_i18n, 'outings')
,   'date' => $item['date']
,   'activities' => BaseDocument::convertStringToArrayTranslate($item['activities'], $a)
,   'creator' => $item['creator']
,   'maxElevation' => doctrine_value($item['max_elevation'])
,   'heightDiffUp' => doctrine_value($item['height_diff_up'])
,   'routes_rating' => isset($item['linked_routes']) ?
                       field_route_ratings_data($item, false, false, false, 'json') : null
,   'conditionsStatus' => @$c[doctrine_value($item['conditions_status'])]
,   'frequentation' => @$f[doctrine_value($item['frequentation_status'])]
,   'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0
,   'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0
,   'hasTrack' => (strlen($item['geom_wkt']) > 0) ? 1 : 0
,   'linkedAreas' => json_decode(get_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations'])))
);

if ($add_conditions)
{
    $outing_route_desc = $item_i18n['outing_route_desc'];
    if (check_not_empty($outing_route_desc) && !($outing_route_desc instanceof sfOutputEscaperObjectDecorator))
    {
        if ($text_html)
        {
            $outing_route_desc = parse_links(parse_bbcode($outing_route_desc, null, false, false));
        }
    }
    else
    {
        $outing_route_desc = null;
    }
    
    $conditions_levels = unserialize($item_i18n['conditions_levels']);

    $conditions = $item_i18n['conditions'];
    if (check_not_empty($conditions) && !($conditions instanceof sfOutputEscaperObjectDecorator))
    {
        if ($text_html)
        {
            $conditions = parse_links(parse_bbcode($conditions, null, false, false));
        }
    }
    else
    {
        $conditions = null;
    }
    
    $avalanche_date_list = sfConfig::get('mod_outings_avalanche_date_list');
    $avalanche_date = BaseDocument::convertStringToArrayTranslate($item['avalanche_date'], $avalanche_date_list);
    $has_avalanche_date = check_not_empty($avalanche_date) && !($avalanche_date instanceof sfOutputEscaperObjectDecorator) && count($avalanche_date) && !array_intersect(array(0, 1), $avalanche_date);
    
    $avalanche_desc = $item_i18n['avalanche_desc'];
    if ($has_avalanche_date && check_not_empty($avalanche_desc) && !($avalanche_desc instanceof sfOutputEscaperObjectDecorator))
    {
        if ($text_html)
        {
            $avalanche_desc = parse_links(parse_bbcode($avalanche_desc, null, false, false));
        }
    }
    else
    {
        $avalanche_desc = null;
    }
    
    $weather = $item_i18n['weather'];
    if (check_not_empty($weather) && !($weather instanceof sfOutputEscaperObjectDecorator))
    {
        if ($text_html)
        {
            $weather = parse_links(parse_bbcode($weather, null, false, false));
        }
    }
    else
    {
        $weather = null;
    }
    
    $timing = $item_i18n['timing'];
    if (check_not_empty($timing) && !($timing instanceof sfOutputEscaperObjectDecorator))
    {
        if ($text_html)
        {
            $timing = parse_links(parse_bbcode($timing, null, false, false));
        }
    }
    else
    {
        $timing = null;
    }

    $glacier_statuses = sfConfig::get('mod_outings_glacier_statuses_list');
    $frequentation_statuses = sfConfig::get('mod_outings_frequentation_statuses_list');
    
    $properties = array_merge ($properties, array(
        'accessElevation' => doctrine_value($item['access_elevation'])
    ,   'upSnowElevation' => doctrine_value($item['up_snow_elevation'])
    ,   'downSnowElevation' => doctrine_value($item['down_snow_elevation'])
    ,   'outingRouteDesc' => $outing_route_desc
    ,   'glacierStatus' => @$glacier_statuses[doctrine_value($item['glacier_status'])]
    ,   'frequentationStatus' => @$frequentation_statuses[doctrine_value($item['frequentation_status'])]
    ,   'conditionsLevels' => $conditions_levels
    ,   'conditions' => $conditions
    ,   'avalancheObsType' => $avalanche_date
    ,   'avalancheDesc' => $avalanche_desc
    ,   'weather' => $weather
    ,   'timing' => $timing
    ));
}

if ($add_all_fields)
{
    $access_comments = $item_i18n['access_comments'];
    if (check_not_empty($access_comments) && !($access_comments instanceof sfOutputEscaperObjectDecorator))
    {
        if ($text_html)
        {
            $access_comments = parse_links(parse_bbcode($description, null, false, false));
        }
    }
    else
    {
        $access_comments = null;
    }

    $hut_comments = $item_i18n['hut_comments'];
    if (check_not_empty($hut_comments) && !($hut_comments instanceof sfOutputEscaperObjectDecorator))
    {
        if ($text_html)
        {
            $hut_comments = parse_links(parse_bbcode($hut_comments, null, false, false));
        }
    }
    else
    {
        $hut_comments = null;
    }

    $description = $item_i18n['description'];
    if (check_not_empty($description) && !($description instanceof sfOutputEscaperObjectDecorator))
    {
        if ($text_html)
        {
            $description = parse_links(parse_bbcode($description, null, false, false));
        }
    }
    else
    {
        $description = null;
    }

    $track_statuses = sfConfig::get('mod_outings_track_statuses_list');
    $hut_statuses = sfConfig::get('mod_outings_hut_statuses_list');
    $lift_statuses = sfConfig::get('mod_outings_lift_statuses_list');
    
    $properties = array_merge ($properties, array(
        'minElevation' => doctrine_value($item['min_elevation'])
    ,   'heightDiffDown' => doctrine_value($item['height_diff_down'])
    ,   'outingLength' => doctrine_value($item['outing_length'])
    ,   'partialTrip' => doctrine_value($item['partial_trip'])
    ,   'usePublicTransportation' => doctrine_value($item['outing_with_public_transportation'])
    ,   'trackStatus' => @$track_statuses[doctrine_value($item['track_status'])]
    ,   'hutStatus' => @$hut_statuses[doctrine_value($item['hut_status'])]
    ,   'liftStatus' => @$lift_statuses[doctrine_value($item['lift_status'])]
    ,   'accessComments' => $access_comments
    ,   'hutComments' => $hut_comments
    ,   'outingComments' => $description
    ));
}

echo json_encode(array(
    'type' => 'Feature'
,   'geometry' => ($add_gpx_track) ? geojson_geometry($item) : null
,   'id' => $item['id']
,   'properties' => $properties
));
