<?php
use_helper('Field');

$activities = $document->getRaw('activities');
?>
    <ul class="data col_left col_33">
    <?php
    li(field_activities_data($document));
    li(field_data_range_if_set($document, 'min_elevation', 'max_elevation', 'elevation separator', '', '', 'meters'));
    li(field_data_range_if_set($document, 'height_diff_up', 'height_diff_down', 'height diff separator', '+', '-', 'meters', true));
    li(field_data_if_set($document, 'route_length', '', 'kilometers'));

    if (array_intersect(array(1,2,3,4,5), $activities)) // ski, snow or mountain or rock or ice_climbing
    {
        $value = $document->get('elevation');
        li(field_data_arg_if_set('difficulties_start_elevation', $value, '', 'meters'));
        li(field_data_if_set($document, 'difficulties_height', '', 'meters'));
    }

    if (array_intersect(array(1,2,3,4), $activities)) // ski, snow or mountain or rock_climbing
    {
        li(field_data_from_list_if_set($document, 'configuration', 'mod_routes_configurations_list', true));
    }
    
    li(field_data_from_list_if_set($document, 'facing', 'app_routes_facings'));
    li(field_data_from_list_if_set($document, 'route_type', 'mod_routes_route_types_list'));
    
    $duration = field_data_from_list_if_set($document, 'duration', 'mod_routes_durations_list');
    if ($duration)
    {
        li($duration . ' ' . __('days'));
    }

    if (array_intersect(array(1,2,3), $activities)) // ski, snow, mountain
    {
        li(field_bool_data($document, 'is_on_glacier'));
    }
    
    if (array_intersect(array(1,2), $activities)) // ski or snow
    {
        li(field_data_if_set($document, 'slope'));
    }

    if (array_intersect(array(2,3,4,5), $activities)) // snow or mountain, rock or ice_climbing
    {
        li(field_data_from_list($document, 'global_rating', 'app_routes_global_ratings'), true);
        li(field_data_from_list($document, 'engagement_rating', 'app_routes_engagement_ratings'));
    }

    if (array_intersect(array(3,4), $activities)) // rock_climbing or mountain_climbing
    {
        li(field_data_from_list_if_set($document, 'rock_free_rating', 'app_routes_rock_free_ratings'));
        li(field_data_from_list_if_set($document, 'rock_required_rating', 'app_routes_rock_free_ratings'));
        li(field_data_from_list_if_set($document, 'aid_rating', 'app_routes_aid_ratings'));
    }

    if (array_intersect(array(2,5), $activities)) // snow or ice_climbing
    {
        li(field_data_from_list_if_set($document, 'ice_rating', 'app_routes_ice_ratings'));
        li(field_data_from_list_if_set($document, 'mixed_rating', 'app_routes_mixed_ratings'));
    }

    if (array_intersect(array(2,3,4,5), $activities)) // snow or mountain, rock or ice_climbing
    {
        li(field_data_from_list($document, 'equipment_rating', 'app_equipment_ratings_list'));
    }

    if (in_array(1, $activities)) // skitouring
    {
        li(field_data_from_list($document, 'toponeige_technical_rating', 'app_routes_toponeige_technical_ratings'), true);
        li(field_data_from_list($document, 'toponeige_exposition_rating', 'app_routes_toponeige_exposition_ratings'));
        li(field_data_from_list($document, 'labande_ski_rating', 'app_routes_labande_ski_ratings'));
        li(field_data_from_list($document, 'labande_global_rating', 'app_routes_global_ratings'));
        li(field_data_from_list_if_set($document, 'sub_activities', 'mod_routes_sub_activities_list', true));
    }

    if (in_array(6, $activities)) // hiking
    {
        li(field_data_from_list($document, 'hiking_rating', 'app_routes_hiking_ratings'), true);
    }
    
    if ($document->get('geom_wkt'))
    {
        li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang')), true);
    }
    ?>
</ul>
