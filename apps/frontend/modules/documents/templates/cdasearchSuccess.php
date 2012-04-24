<?php
use_helper('Form', 'Javascript', 'FilterForm', 'General', 'MyForm', 'Button');

$response = sfContext::getInstance()->getResponse();
$response->addJavascript('/static/js/filter.js', 'last');

$lang = $sf_user->getCulture();
$type = $sf_params->get('type');

$class_left = $class_right = '';
if ($type != null)
{
    $class_left = $class_right = ' folded';
}
$special_activities = array(2 => '', 3 => '', 5 => '');
$merged_activities = array('2-3-5' => 'mountaineering');
if ($type == 'routes')
{
    $class_left .= ' active';
    $class_right .= ' inactive';
    $filter_tips = __('cda_search_routes_tips');
    $paragliding_tag = sfConfig::get('app_tags_paragliding');
    $paragliding_tag = implode('/', $paragliding_tag);
    $special_activities[8] = $paragliding_tag;
}
if ($type == 'outings')
{
    $class_left .= ' inactive';
    $class_right .= ' active';
    $filter_tips = __('cda_search_outings_tips');
}

?>

<div class="column span-6<?php echo $class_left; ?>">
<?php
if ($type != 'routes')
{
    echo '<a href="' . url_for('@cdasearch_by_type?lang=' . $lang . '&type=routes') . '">';
}
$img_title = __('Search routes');
$img = ($type != null) ? '/static/images/cda/slide6a_small.jpg' : '/static/images/cda/slide6a.jpg';
echo image_tag($img, array('alt' => $img_title, 'title' => $img_title));
echo '<div class="img_title">' . $img_title . '</div>';
if ($type != 'routes')
{
    echo '</a>';
}
?></div>
<div class="column last span-6<?php echo $class_right; ?>">
<?php
if ($type != 'outings')
{
    echo '<a href="' . url_for('@cdasearch_by_type?lang=' . $lang . '&type=outings') . '">';
}
$img_title = __('Search outings');
$img = ($type != null) ? '/static/images/cda/slide7a_small.jpg' : '/static/images/cda/slide7a.jpg';
echo image_tag($img, array('alt' => $img_title, 'title' =>  $img_title));
echo '<div class="img_title">' . $img_title . '</div>';
if ($type != 'outings')
{
    echo '</a>';
}
?></div>
<?php if ($type != null): ?>
<h2><?php echo $filter_tips; ?></h2>
<div class="fake_clear"> &nbsp;</div>
<?php
echo form_tag("/documents/cdaredirect", array('id' => 'filterform')); // FIXME redirect
echo input_hidden_tag('module', $type, array('id' => 'module'));
include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => false, 'height' => '300px'));
echo around_selector('arnd', true) . '<br />';

$activities_raw = $sf_data->getRaw('activities');
echo '<br />' . __('cda_activities') . ' ' . activities_selector(false, true, $activities_raw, $special_activities, $merged_activities);
echo __('cda_difficulty') . ' ' . select_tag('difficulty', options_for_select(array_map('__', sfConfig::get('app_cda_difficulty'))));
echo __('cda_elevation') . ' ' . select_tag('elevation', options_for_select(array_map('__', sfConfig::get('app_cda_elevation'))));
?>
<br />
<br />
<?php
echo c2c_reset_tag(__('Cancel'), array('picto' => 'action_cancel'));
echo c2c_submit_tag(__('Search on c2c'), array('picto' => 'action_filter', 'class' => 'main_button'));
?>
<br />
<br />
<?php echo '<a href="/' . $type . '/filter">' . __('Advanced search') . '</a>' ?>
</form>
<?php endif?>
<div class="fake_clear"> &nbsp;</div>
