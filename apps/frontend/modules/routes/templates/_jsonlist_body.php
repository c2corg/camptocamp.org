<?php
use_helper('Field', 'SmartFormat', 'sfBBCode');
$item_i18n = $item->getRaw('RouteI18n');
$item_i18n = $item_i18n[0];
$summit = $item['associations'][0]['Summit'][0]['SummitI18n'][0]['name'];

$activities_list = sfConfig::get('app_activities_list');
$facing_list = sfConfig::get('app_routes_facings');

$facing = $item['facing'];

$properties = array(
    'module' => 'routes',
    'name' => $summit . __(' :') . ' ' . $item_i18n['name'],
    'url' => jsonlist_url($item_i18n, 'routes', $summit),
    'activities' => $use_keys ? BaseDocument::convertStringToArray($item['activities']) : BaseDocument::convertStringToArrayTranslate($item['activities'], $activities_list),
    'ratings' => field_route_ratings_data($item, false, false, false, ($use_keys ? 'jsonkeys' : 'json')),
    'maxElevation' => doctrine_value($item['max_elevation']),
    'heightDiffUp' => doctrine_value($item['height_diff_up']),
    'difficultiesHeight' => doctrine_value($item['difficulties_height']),
    'mainFacing' => $use_keys ? $facing : @$facing_list[doctrine_value($facing)],
    'nbLinkedImages' =>  isset($item['nb_images']) ?  $item['nb_images'] : 0,
    'nbLinkedOutings' => isset($item['nb_linked_docs']) ? $item['nb_linked_docs'] : 0,
    'nbComments' =>  isset($item['nb_comments']) ? $item['nb_comments'] : 0,
    'hasTrack' => (strlen($item['geom_wkt']) > 0) ? true : false,
    'linkedAreas' => json_decode(get_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations'], 'use_keys' => $use_keys))),
    'linkedParkings' => json_decode(get_partial('parkings/parkings4jsonlist', array('parkings' => (isset($item['linked_docs']) ? $item['linked_docs'] : array()), 'use_keys' => $use_keys)))
) ;

if ($add_all_fields)
{
    $route_types_list = sfConfig::get('mod_routes_route_types_list');
    $durations_list = sfConfig::get('mod_routes_durations_list');
    $configurations_list = sfConfig::get('mod_routes_configurations_list');
    $sub_activities_list = sfConfig::get('mod_routes_sub_activities_list');
    
    $route_type = doctrine_value($item['route_type']);
    $duration = doctrine_value($item['duration']);
    $configuration = $use_keys ? BaseDocument::convertStringToArray($item['configuration']) : BaseDocument::convertStringToArrayTranslate($item['configuration'], $configurations_list);
    
    $sub_activities = BaseDocument::convertStringToArray($item['sub_activities']);
    $snowboarding = in_array(2, $sub_activities);
    $beginner_proof = in_array(4, $sub_activities);
    $mountain_bike_approach = in_array(6, $sub_activities);
    $lift_approach = in_array(8, $sub_activities);
    
    $is_on_glacier = doctrine_value($item['is_on_glacier']);
    $is_on_glacier = empty($is_on_glacier) ? false : true;
    
    $slope = $item->getRaw('slope');
    if (!check_not_empty($slope) || ($slope instanceof sfOutputEscaperObjectDecorator))
    {
        $slope = null;
    }

    $properties = array_merge ($properties, array(
        'minElevation' => doctrine_value($item['min_elevation'])
    ,   'heightDiffDown' => doctrine_value($item['height_diff_down'])
    ,   'routeType' => $use_keys ? $route_type : @$route_types_list[$route_type]
    ,   'duration' => $use_keys ? $duration : @$durations_list[$duration]
    ,   'configuration' => $configuration
    ,   'snowboardingProof' => $snowboarding
    ,   'beginnerProof' => $beginner_proof
    ,   'mountainBikeApproach' => $mountain_bike_approach
    ,   'liftApproach' => $lift_approach
    ,   'requiresGlacierGear' => $is_on_glacier
    ,   'slope' => $slope
    ));
    
    if ($add_text)
    {
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

        $remarks = $item_i18n['remarks'];
        if (check_not_empty($remarks) && !($remarks instanceof sfOutputEscaperObjectDecorator))
        {
            if ($text_html)
            {
                $remarks = parse_links(parse_bbcode($remarks, null, false, false));
            }
        }
        else
        {
            $remarks = null;
        }

        $gear = $item_i18n['gear'];
        if (check_not_empty($gear) && !($gear instanceof sfOutputEscaperObjectDecorator))
        {
            if ($text_html)
            {
                $gear = parse_links(parse_bbcode($gear, null, false, false));
            }
        }
        else
        {
            $gear = null;
        }

        $external_resources = $item_i18n['external_resources'];
        if (check_not_empty($external_resources) && !($external_resources instanceof sfOutputEscaperObjectDecorator))
        {
            if ($text_html)
            {
                $external_resources = parse_links(parse_bbcode($external_resources, null, false, false));
            }
        }
        else
        {
            $external_resources = null;
        }

        $route_history = $item_i18n['route_history'];
        if (check_not_empty($route_history) && !($route_history instanceof sfOutputEscaperObjectDecorator))
        {
            if ($text_html)
            {
                $route_history = parse_links(parse_bbcode($route_history, null, false, false));
            }
        }
        else
        {
            $route_history = null;
        }

        $properties = array_merge ($properties, array(
            'description' => $description
        ,   'remarks' => $remarks
        ,   'gear' => $gear
        ,   'externalResources' => $external_resources
        ,   'routeHistory' => $route_history
        ));
    }
}

echo json_encode(array(
    'type' => 'Feature',
    'id' => $item['id'],
    'properties' => $properties,
    'geometry' => ($add_gpx_track) ? geojson_geometry($item) : null
));
