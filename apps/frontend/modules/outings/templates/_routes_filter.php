<?php
use_helper('General');
?>
<br /><br />
<?php
$activities_raw = $sf_data->getRaw('activities');
echo __('activities') . ' ' . field_value_selector('acttyp', 'mod_outings_activities_type_list', array('keepfirst' => false, 'filled_options' => false));
echo activities_selector(true, true, $activities_raw);
echo __('max_elevation') . ' ' . elevation_selector('oalt');
echo __('height_diff_up') . ' ' . elevation_selector('odif');
?>
<br />
<?php echo __('facing') . ' ' . facings_selector('fac'); ?> 
<div id="ski_snow_mountain_rock_ice_fields" style="display:none">
</div>
<div id="ski_snow_mountain_rock_fields" style="display:none">
</div>
<div id="ski_snow_mountain_fields" style="display:none">
<?php
echo __('is_on_glacier') . ' ' . bool_selector('glac');
?>
</div>
<div id="snow_mountain_rock_ice_fields" style="display:none">
<br />
<?php
echo __('global_rating') . ' ' . range_selector('grat', 'app_routes_global_ratings');
echo __('engagement_rating') . ' ' . range_selector('erat', 'app_routes_engagement_ratings');
?>
<br />
<?php
echo __('equipment_rating') . ' ' . range_selector('prat', 'app_equipment_ratings_list', null, true);
?>
</div>
<div id="rock_mountain_fields" style="display:none">
<?php
echo __('rock_free_rating') . ' ' . range_selector('frat', 'app_routes_rock_free_ratings');
echo __('rock_required_rating') . ' ' . range_selector('rrat', 'app_routes_rock_free_ratings');
?>
<br />
<?php echo __('aid_rating') . ' ' . range_selector('arat', 'app_routes_aid_ratings'); ?>
</div>
<div id="snow_ice_fields" style="display:none">
<?php
echo __('ice_rating') . ' ' . range_selector('irat', 'app_routes_ice_ratings');
echo __('mixed_rating') . ' ' . range_selector('mrat', 'app_routes_mixed_ratings');
?>
</div>
<div id="ski_fields" style="display:none">
<br />
<?php 
echo __('toponeige_technical_rating') . ' ' . range_selector('trat', 'app_routes_toponeige_technical_ratings');
echo __('toponeige_exposition_rating') . ' ' . range_selector('expo', 'app_routes_toponeige_exposition_ratings');
?>
<br />
<?php
echo __('labande_global_rating') . ' ' . range_selector('lrat', 'app_routes_global_ratings');
echo __('labande_ski_rating') . ' ' . range_selector('srat', 'app_routes_labande_ski_ratings');
?>
<br />
<?php
echo bool_selector_from_list('sub', 'mod_outings_sub_activities_list', 2);
echo bool_selector_from_list('sub', 'mod_outings_sub_activities_list', 4);
?>
</div>
<div id="snowshoeing_fields" style="display:none">
<br />
<?php
echo __('snowshoeing_rating') . ' ' . range_selector('wrat', 'app_routes_snowshoeing_ratings');
?>
</div>
<div id="hiking_fields" style="display:none">
<br />
<?php
echo __('hiking_rating') . ' ' . range_selector('hrat', 'app_routes_hiking_ratings');
echo __('route_length') . ' ' . elevation_selector('olen', 'kilometers');
?>
</div>
<br />
<?php 
echo bool_selector_from_list('sub', 'mod_outings_sub_activities_list', 6);
echo bool_selector_from_list('sub', 'mod_outings_sub_activities_list', 8);
