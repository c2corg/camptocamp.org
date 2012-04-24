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

$activities_raw = $sf_data->getRaw('activities');
$special_activities = array(2 => '', 3 => '', 5 => '');
$merged_activities = array('2-3-5' => 'mountaineering');
$multiple_activities = array(8);
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

<div class="column span-6">
<?php
if ($type != 'routes')
{
    echo '<a href="' . url_for('@cdasearch_by_type?lang=' . $lang . '&type=routes') . '">';
}
$img_title = __('Search routes');
if ($type == null) {
  $img = '/static/images/cda/slide6a.jpg';
  $nobg = '';
} else {
    $nobg = ' nobg';
    if ($type == 'routes') {
        $img = '/static/images/cda/slide6a_small.jpg';
    } else {
        $img = '/static/images/cda/slide6a_small_inactive.jpg';
    }
}
echo image_tag($img, array('alt' => $img_title, 'title' => $img_title));
echo '<div class="img_title' . $nobg . '">' . $img_title . '</div>';
if ($type != 'routes')
{
    echo '</a>';
}
?></div>
<div class="column last span-6">
<?php
if ($type != 'outings')
{
    echo '<a href="' . url_for('@cdasearch_by_type?lang=' . $lang . '&type=outings') . '">';
}
$img_title = __('Search outings');
if ($type == null) {
  $img = '/static/images/cda/slide7a.jpg';
  $nobg = '';
} else {
    $nobg = ' nobg';
    if ($type == 'outings') {
        $img = '/static/images/cda/slide7a_small.jpg';
    } else {
        $img = '/static/images/cda/slide7a_small_inactive.jpg';
    }
}
echo image_tag($img, array('alt' => $img_title, 'title' =>  $img_title));
echo '<div class="img_title' . $nobg . '">' . $img_title . '</div>';
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

echo '<br />' . __('cda_activities') . ' ' . activities_selector(false, true, $activities_raw, $special_activities, $merged_activities, $multiple_activities, false);

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
