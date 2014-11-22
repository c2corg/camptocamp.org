<?php
use_helper('General');
?>
<br /><br />
<?php
echo '<div class="fieldname">' . picto_tag('picto_routes') . __('Route:') . ' </div>' .
     (isset($autofocus) ? input_tag('rnam', null, array('autofocus' => 'autofocus')) : input_tag('rnam'));
echo georef_selector('With GPS track:');
?>
<br /><br />
<?php
$activities_raw = $sf_data->getRaw('activities');
$paragliding_tag = sfConfig::get('app_tags_paragliding');
$paragliding_tag = implode('/', $paragliding_tag);
echo __('activities') . ' ' . field_value_selector('acttyp', 'mod_routes_activities_type_list', array('keepfirst' => false, 'filled_options' => false));
echo activities_selector(true, true, $activities_raw, array(8 => $paragliding_tag));
?>
<fieldset>
<?php
echo __('max_elevation') . ' ' . elevation_selector('malt');
echo __('height_diff_up') . ' ' . elevation_selector('hdif');
?>
<div data-act-filter="1 2 3 4 5" style="display:none">
<?php
echo __('difficulties_start_elevation') . ' ' . elevation_selector('ralt');
echo __('difficulties_height') . ' ' . elevation_selector('dhei');
echo __('approach_height') . ' ' . elevation_selector('rappr');
?>
</div>
<div data-act-filter="1 2 3 4" style="display:none">
<?php
echo __('configuration') . ' ' . field_value_selector('conf', 'mod_routes_configurations_list', array('keepfirst' => false, 'multiple' => true));
?>
</div>
<div>
<?php echo __('facing') . ' ' . facings_selector('fac'); ?> 
</div>
<?php 
echo __('route_type') . ' ' . field_value_selector('rtyp', 'mod_routes_route_types_list', array('keepfirst' => false, 'multiple' => true));
echo __('duration') . ' ' . range_selector('time', 'mod_routes_durations_list', 'days'); 
?>
<div data-act-filter="1 2 3" style="display:none">
<?php
echo __('is_on_glacier') . ' ' . bool_selector('glac');
?>
</div>
</fieldset>
<div data-act-filter="1 2 3 4 5 6 7" style="display:none">
<br />
<fieldset>
<div data-act-filter="2 3 4 5" style="display:none">
<?php
echo __('global_rating') . ' ' . range_selector('grat', 'app_routes_global_ratings');
echo __('engagement_rating') . ' ' . range_selector('erat', 'app_routes_engagement_ratings');
?>
<br />
<?php
echo __('equipment_rating') . ' ' . range_selector('prat', 'app_equipment_ratings_list', null, true);
?>
<span data-act-filter="2 3 5" style="display:none">
<?php
echo __('objective_risk_rating') . ' ' . range_selector('orrat', 'app_routes_objective_risk_ratings');
?>
</span>
</div>
<div data-act-filter="3 4" style="display:none">
<?php
echo __('rock_free_rating') . ' ' . range_selector('frat', 'app_routes_rock_free_ratings');
echo __('rock_required_rating') . ' ' . range_selector('rrat', 'app_routes_rock_free_ratings');
?>
<br />
<?php
echo __('aid_rating') . ' ' . range_selector('arat', 'app_routes_aid_ratings');
echo __('rock_exposition_rating') . ' ' . range_selector('rexpo', 'app_routes_rock_exposition_ratings');
?>
</div>
<div data-act-filter="2 5" style="display:none">
<?php
echo __('ice_rating') . ' ' . range_selector('irat', 'app_routes_ice_ratings');
echo __('mixed_rating') . ' ' . range_selector('mrat', 'app_routes_mixed_ratings');
?>
</div>
<div data-act-filter="1" style="display:none">
<br />
<?php
echo __('toponeige_technical_rating') . ' ' . range_selector('trat', 'app_routes_toponeige_technical_ratings');
echo __('toponeige_exposition_rating') . ' ' . range_selector('sexpo', 'app_routes_toponeige_exposition_ratings');
?>
<br />
<?php
echo __('labande_global_rating') . ' ' . range_selector('lrat', 'app_routes_global_ratings');
echo __('labande_ski_rating') . ' ' . range_selector('srat', 'app_routes_labande_ski_ratings');
?>
<br />
<?php 
echo bool_selector_from_list('sub', 'mod_routes_sub_activities_list', 2);
echo bool_selector_from_list('sub', 'mod_routes_sub_activities_list', 4);
?>
</div>
<div data-act-filter="7" style="display:none">
<br />
<?php
echo __('snowshoeing_rating') . ' ' . range_selector('wrat', 'app_routes_snowshoeing_ratings');
?>
</div>
<div data-act-filter="6" style="display:none">
<br />
<?php
echo __('hiking_rating') . ' ' . range_selector('hrat', 'app_routes_hiking_ratings');
echo __('route_length') . ' ' . elevation_selector('rlen', 'kilometers');
?>
</div>
</fieldset>
</div>
<?php 
