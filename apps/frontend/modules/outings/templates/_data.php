<?php
use_helper('Field');
$activities = $document->getRaw('activities');

if (isset($preview) && $preview)
{
    $participants = field_text_data_if_set($document, 'participants', null, array('show_images' => false));//
    if (!empty($participants))
    {
        echo '<div class="all_associations col_left col_66">'
           . $participants
           . '</div>';
    }
}
 
// put here meta tags for microdata that cannot be inside ul tags
echo microdata_meta('name', $document->getName());
if (isset($nb_comments) && $nb_comments)
{
    echo microdata_meta('interactionCount', $nb_comments . ' UserComments');
    echo microdata_meta('discussionUrl', url_for('@document_comment?module=outings&id='.$sf_params->get('id').'&lang='.$sf_params->get('lang')));
}
?>
<ul class="data col_left col_33">
    <?php
    li(field_activities_data($document));
    li(field_bool_data($document, 'partial_trip', array('show_only_yes' => true)));
    li(field_data_range_if_set($document, 'min_elevation', 'max_elevation', array('separator' => 'elevation separator', 'suffix' => 'meters')));
    li(field_data_range_if_set($document, 'height_diff_up', 'height_diff_down', array('separator' => 'height diff separator', 'prefix_min' => '+',
        'prefix_max' => '-', 'suffix' => 'meters', 'range_only' => true)));
    li(field_data_if_set($document, 'outing_length', array('suffix' => 'kilometers')));
    if ($document->get('geom_wkt'))
    {
        li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang'), $sf_params->get('version')));
    } 

    li(field_bool_data($document, 'outing_with_public_transportation', array('show_only_yes' => true)));
    $access_elevation = field_data_if_set($document, 'access_elevation', array('suffix' => 'meters'));
    if (empty($access_elevation))
    {
        li(field_data_from_list_if_set($document, 'access_status', 'mod_outings_access_statuses_list'));
    }
    else
    {
        $access_status = field_data_from_list_if_set($document, 'access_status', 'mod_outings_access_statuses_list', array('raw' => true, 'prefix' => ' - '));
        li($access_elevation . $access_status);
    }
    if (array_intersect(array(1,2,5), $activities)) // ski, snow or ice_climbing
    {
        li(field_data_range_if_set($document, 'up_snow_elevation', 'down_snow_elevation', array('separator' => 'elevation separator',
            'suffix' => 'meters')));
    }
    ?>
</ul>
<ul class="data col col_33">
    <li style="display:none"></li>
    <?php
    li(field_data_from_list_if_set($document, 'conditions_status', 'mod_outings_conditions_statuses_list'));
    li(field_data_from_list_if_set($document, 'glacier_status', 'mod_outings_glacier_statuses_list'));
    if (array_intersect(array(1,2,5), $activities)) // ski, snow or ice_climbing
    {
        li(field_data_from_list_if_set($document, 'track_status', 'mod_outings_track_statuses_list'));
    }
    li(field_data_from_list_if_set($document, 'frequentation_status', 'mod_outings_frequentation_statuses_list'));
    li(field_data_from_list_if_set($document, 'hut_status', 'mod_outings_hut_statuses_list'));
    li(field_data_from_list_if_set($document, 'lift_status', 'mod_outings_lift_statuses_list'));
    ?>
</ul>
