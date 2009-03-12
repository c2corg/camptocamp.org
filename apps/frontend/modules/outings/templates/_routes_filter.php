<?php
$response = sfContext::getInstance()->getResponse();
$response->addJavascript(sfConfig::get('app_static_url') . '/static/js/routes_filter.js?' . sfSVN::getHeadRevision('routes_filter.js'), 'last');
?>
<br />
<?php echo __('Route:') . ' ' . input_tag('rnam'); ?>
<br /><br />
<?php echo __('height_diff_up') . ' ' . elevation_selector('hdif'); ?>
<br />
<?php echo __('facing') . ' ' . facings_selector('fac'); ?> 
<br /><br />
<?php echo __('activities') . ' ' . activities_selector(true); ?>
<br /><br />
<div id="ski_fields" style="display:none">
<?php
echo __('sub_activities') . ' ' . field_value_selector('sub', 'mod_outings_sub_activities_list');
?>
<br />
<?php 
echo __('toponeige_technical_rating') . ' ' . range_selector('trat', 'app_routes_toponeige_technical_ratings') . ' ';
echo __('toponeige_exposition_rating') . ' ' . range_selector('expo', 'app_routes_toponeige_exposition_ratings');
?>
<br />
<?php
echo __('labande_global_rating') . ' ' . range_selector('lrat', 'app_routes_global_ratings') . ' ';
echo __('labande_ski_rating') . ' ' . range_selector('srat', 'app_routes_labande_ski_ratings');
?>
</div>
<div id="snow_ice_fields" style="display:none">
<?php
echo __('ice_rating') . ' ' . range_selector('irat', 'app_routes_ice_ratings') . ' ';
echo __('mixed_rating') . ' ' . range_selector('mrat', 'app_routes_mixed_ratings');
?>
</div>
<div id="rock_mountain_fields" style="display:none">
<?php
echo __('rock_free_rating') . ' ' . range_selector('frat', 'app_routes_rock_free_ratings') . ' ';
echo __('rock_required_rating') . ' ' . range_selector('rrat', 'app_routes_rock_free_ratings');
?>
<br />
<?php echo __('aid_rating') . ' ' . range_selector('arat', 'app_routes_aid_ratings'); ?>
</div>
<div id="snow_mountain_rock_ice_fields" style="display:none">
<?php
echo __('global_rating') . ' ' . range_selector('grat', 'app_routes_global_ratings') . ' ';
echo __('engagement_rating') . ' ' . range_selector('erat', 'app_routes_engagement_ratings');
?>
<br />
<?php
echo __('equipment_rating') . ' ' . range_selector('prat', 'app_equipment_ratings_list', null, true);
?>
</div>
<div id="hiking_fields" style="display:none">
<?php echo __('hiking_rating') . ' ' . range_selector('hrat', 'app_routes_hiking_ratings'); ?>
</div>
<div id="ski_snow_mountain_ice_fields" style="display:none">
<?php
echo __('is_on_glacier') . ' ';
echo select_tag('glac', options_for_select(array('yes' => __('yes'), 'no' => __('no')),
                                           '', array('include_blank' => true)));
?>
</div>
