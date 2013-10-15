<?php
use_helper('Field');
$activities = $document->getRaw('activities');

// put here meta tags for microdata that cannot be inside ul tags
echo microdata_meta('name', $document->getName());
if (isset($nb_comments) && $nb_comments)
{
    echo microdata_meta('interactionCount', $nb_comments . ' UserComments');
    echo microdata_meta('discussionUrl', url_for('@document_comment?module=routes&id='.$sf_params->get('id').'&lang='.$sf_params->get('lang')));
}
?>
<ul class="data col_left col_33">
    <?php
    li(field_activities_data($document));
    li(field_data_range_if_set($document, 'min_elevation', 'max_elevation', array('separator' => 'elevation separator', 'suffix' => 'meters')));
    li(field_data_range_if_set($document, 'height_diff_up', 'height_diff_down', array('separator' => 'height diff separator',
        'prefix_min' => '+', 'prefix_max' => '-', 'suffix' => 'meters', 'range_only' => true)));
    li(field_data_if_set($document, 'route_length', array('suffix' => 'kilometers')));
 
    if (array_intersect(array(1,2,3,4,5), $activities)) // ski, snow or mountain or rock or ice_climbing
    {
        $value = $document->get('elevation');
        li(field_data_arg_if_set('difficulties_start_elevation', $value, '', 'meters'));
        li(field_data_if_set($document, 'difficulties_height', array('suffix' => 'meters')));
    }

    if (array_intersect(array(1,2,3,4,7), $activities)) // ski, snow or mountain or rock_climbing
    {
        li(field_data_from_list_if_set($document, 'configuration', 'mod_routes_configurations_list', array('multiple' => true)));
    }
    
    li(field_data_from_list_if_set($document, 'facing', 'app_routes_facings'));
    $new_items = array();
    if (!array_intersect(array(2,3,4,5), $activities)) // neither snow nor mountain, rock nor ice_climbing
    {
        $new_items[1] = 'return_same_way_easy';
    }
    if (!$has_associated_huts)
    {
        $new_items[2] = 'loop_short';
    }
    li(field_data_from_list_if_set($document, 'route_type', 'mod_routes_route_types_list', array('new_items' => $new_items)));
    
    $duration = field_data_from_list_if_set($document, 'duration', 'mod_routes_durations_list');
    if ($duration)
    {
        if (in_array($document->getRaw('duration'), array(1,2))) // cannot use an intelligent translation string because of '1/2'
        {
            if (array_intersect(array(1,2,3,6,7), $activities)) // do not show duration for rock and ice climbing only if duration is 1 day or less
            {
                li($duration . ' ' . __('day'));
            }
        }
        else
        {
            li($duration . ' ' . __('days'));
        }
    }

    if (array_intersect(array(1,2,7), $activities)) // ski or snow or snowshoeing
    {
        li(field_data_if_set($document, 'slope'));
    }

    if (array_intersect(array(2,3,4,5), $activities)) // snow or mountain, rock or ice_climbing
    {
        li(field_data_from_list($document, 'global_rating', 'app_routes_global_ratings'), array('class' => 'separator'));
        li(field_data_from_list($document, 'engagement_rating', 'app_routes_engagement_ratings'));
    }

    if (array_intersect(array(2,3,5), $activities)) // snow, mountain or ice_climbing
    {
        li(field_data_from_list_if_set($document, 'objective_risk_rating', 'app_routes_objective_risk_ratings'));
    }

    if (array_intersect(array(2,3,4,5), $activities)) // snow or mountain, rock or ice_climbing
    {
        li(field_data_from_list($document, 'equipment_rating', 'app_equipment_ratings_list'));
    }

    if (array_intersect(array(3,4), $activities)) // rock_climbing or mountain_climbing
    {
        $equipment_rating = $document->getRaw('equipment_rating');
        $aid_rating = $document->getRaw('aid_rating');
        if ($equipment_rating == 1 && empty($aid_rating))
        {
            $suffix = array('', 'A0');
        }
        else
        {
            $suffix = '';
        }
        li(field_data_from_list_if_set($document, 'rock_exposition_rating', 'app_routes_rock_exposition_ratings'));
        li(field_data_from_list_if_set($document, 'aid_rating', 'app_routes_aid_ratings'));
        li(field_data_range_from_list_if_set($document, 'rock_free_rating', 'rock_required_rating', 'app_routes_rock_free_ratings',
            array('name_if_equal' => 'rock_free_and_required_rating', 'separator' => 'rock rating separator', 'suffix' => $suffix)));
    }

    if (array_intersect(array(2,5), $activities)) // snow or ice_climbing
    {
        li(field_data_from_list_if_set($document, 'ice_rating', 'app_routes_ice_ratings'));
        li(field_data_from_list_if_set($document, 'mixed_rating', 'app_routes_mixed_ratings'));
    }

    if (in_array(1, $activities)) // skitouring
    {
        li(field_data_from_list($document, 'toponeige_technical_rating', 'app_routes_toponeige_technical_ratings'), array('class' => 'separator'));
        li(field_data_from_list($document, 'toponeige_exposition_rating', 'app_routes_toponeige_exposition_ratings'));
        li(field_data_from_list($document, 'labande_ski_rating', 'app_routes_labande_ski_ratings'));
        li(field_data_from_list($document, 'labande_global_rating', 'app_routes_global_ratings'));
        li(field_bool_data_from_list($document, 'sub_activities', 'mod_routes_sub_activities_list', array('single_value' => 2, 'show_only_yes' => true)));
        li(field_bool_data_from_list($document, 'sub_activities', 'mod_routes_sub_activities_list', array('single_value' => 4, 'show_only_yes' => true)));
    }

    if (in_array(6, $activities)) // hiking
    {
        li(field_data_from_list($document, 'hiking_rating', 'app_routes_hiking_ratings'), array('class' => 'separator'));
    }

    if (in_array(7, $activities)) // snowshoeing
    {
        li(field_data_from_list($document, 'snowshoeing_rating', 'app_routes_snowshoeing_ratings'), array('class' => 'separator'));
    }

    li($first = field_bool_data_from_list($document, 'sub_activities', 'mod_routes_sub_activities_list', array('single_value' => 6, 'show_only_yes' => true)),
       array('class' => 'separator'));
    li(field_bool_data_from_list($document, 'sub_activities', 'mod_routes_sub_activities_list', array('single_value' => 8, 'show_only_yes' => true)), empty($first));
    
    if ($document->get('geom_wkt'))
    {
        li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang'), $sf_params->get('version')),
           array('class' => 'separator'));
    }
    ?>
</ul>
