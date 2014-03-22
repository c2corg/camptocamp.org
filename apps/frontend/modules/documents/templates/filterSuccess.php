<?php
use_helper('Form', 'Viewer', 'Javascript', 'Escaping', 'MyForm');
$module = $sf_context->getModuleName();
$response = sfContext::getInstance()->getResponse();
$response->addJavascript('/static/js/filter.js', 'last');

if (!isset($activities))
{
    $activities = array();
}
else
{
    $activities_raw = $sf_data->getRaw('activities');
    $activities = $activities_raw;
}

if (!isset($selected_areas))
{
    $selected_areas = array();
}
else
{
    $selected_areas_raw = $sf_data->getRaw('selected_areas');
    $selected_areas = $selected_areas_raw;
}

if (!isset($coords))
{
    $coords = array();
}
else
{
    $coords_raw = $sf_data->getRaw('coords');
    $coords = $coords_raw;
}

echo display_title(__('Search a ' . $module), $module);

if (!c2cTools::mobileVersion()):
?>
<div id="nav_space">&nbsp;</div>
<div id="nav_tools">
    <div id="nav_tools_top"></div>
    <div id="nav_tools_content">
        <ul>
            <li><?php echo link_to(__('Back to list'),
                        "@default_index?module=$module",
                        array('class' => 'action_back nav_edit')) ?>
            </li>
        </ul>
    </div>
    <div id="nav_tools_down"></div>
</div>

<?php
endif;

echo display_content_top('filter_content');
echo start_content_tag($module . '_content');

echo form_tag("/$module/filterredirect", array('id' => 'filterform'));

$perso = c2cPersonalization::getInstance();
$personalization_applied = false;
$perso_on = $perso->isMainFilterSwitchOn();
$has_perso_activities = count($perso->getActivitiesFilter());
$has_perso_areas = count($perso->getPlacesFilter());
switch($module)
{
    // We use activities and areas personalization for the following modules
    case 'books':
    case 'huts':
    case 'routes':
    case 'outings':
    case 'users':
        if ($perso_on)
        {
            if (!count($activities) && $has_perso_activities && !count($selected_areas) && $has_perso_areas)
            {
                $msg = __('activity and area filters applied');
                $personalization_applied = true;
            }
            elseif (!count($selected_areas) && $has_perso_areas)
            {
                $msg = __('area filters applied');
                $personalization_applied = true;
            }
            elseif (!count($activities) && $has_perso_activities)
            {
                $msg = __('activity filters applied');
                $personalization_applied = true;
            }
        }
        break;
    // We use areas personalization only for the following modules
    case 'maps':
    case 'parkings':
    case 'sites':
    case 'summits':
        $msg = __('area filters applied');
        if ($perso_on && !count($selected_areas) && $has_perso_areas)
            $personalization_applied = true;
        break;
    // We do not use personalization for the following modules
    case 'areas':
    case 'images':
    case 'articles':
    default:
        break;
}

if ($personalization_applied)
{
    echo '<p class="list_header warning-tips">', $msg, '</p>';
}

if (!isset($ranges)) $ranges = array();
include_partial("$module/filter_form", array('ranges' => $ranges, 'selected_areas' => $selected_areas, 'activities' => $activities, 'coords' => $coords));

echo c2c_reset_tag(__('Cancel'), array('picto' => 'action_cancel'));
echo c2c_submit_tag(__('Search'), array('picto' => 'action_filter', 'class' => 'main_button'));
?>
</form>

<?php include_partial("documents/google_search", array('module' => $module));

echo end_content_tag();

include_partial('common/content_bottom') ?>
