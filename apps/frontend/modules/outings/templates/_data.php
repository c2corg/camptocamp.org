<?php
use_helper('Field');
$activities = $document->getRaw('activities');
?>

<div class="article_contenu">
    <ul id="article_gauche_5050" class="data">
        <?php
        disp_doc_type('outing');
        li(field_activities_data($document));
        li(field_data_range_if_set($document, 'min_elevation', 'max_elevation', 'elevation separator', '', '', 'meters'));
        li(field_data_range_if_set($document, 'height_diff_up', 'height_diff_down', 'height diff separator', '+', '-', 'meters', true));
        li(field_data_if_set($document, 'outing_length', '', 'kilometers'));
        li(field_bool_data($document, 'partial_trip'));
        li(field_data_from_list_if_set($document, 'conditions_status', 'mod_outings_conditions_statuses_list'), true);
        li(field_data_from_list_if_set($document, 'frequentation_status', 'mod_outings_frequentation_statuses_list'));
        li(field_data_from_list_if_set($document, 'access_status', 'mod_outings_access_statuses_list'));
        li(field_data_if_set($document, 'access_elevation', '', 'meters'));
        
        if (array_intersect(array(1,2,5), $activities)) // ski, snow or ice_climbing
        {
            li(field_data_range_if_set($document, 'up_snow_elevation', 'down_snow_elevation', 'elevation separator', '', '', 'meters'));
            li(field_data_from_list_if_set($document, 'track_status', 'mod_outings_track_statuses_list'));
        }
        
        li(field_data_from_list_if_set($document, 'glacier_status', 'mod_outings_glacier_statuses_list'));
        li(field_data_from_list_if_set($document, 'hut_status', 'mod_outings_hut_statuses_list'));
        li(field_data_from_list_if_set($document, 'lift_status', 'mod_outings_lift_statuses_list'));
        
        if ($document->get('geom_wkt'))
        {
            li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang')), true);
        } 
        ?>
    </ul>
</div>
