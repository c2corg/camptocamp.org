<?php
use_helper('Form', 'Viewer', 'Javascript', 'Escaping');
$module = $sf_context->getModuleName();
$response = sfContext::getInstance()->getResponse();
$response->addJavascript(sfConfig::get('app_static_url') . '/static/js/filter.js?' . sfSVN::getHeadRevision('filter.js'), 'last');

echo display_title(__('Search a ' . $module), $module);
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
echo display_content_top();
echo start_content_tag($module . '_content');

echo form_tag("/$module/filterredirect", array('id' => 'filterform'));

echo '<p class="list_header">' . __('Filter presentation').'</p>';
if (!isset($ranges)) $ranges = array();
include_partial("$module/filter_form", array('ranges' => $ranges));
?>
<br />
<br />
<?php echo submit_tag(__('Search'), array('class' => 'picto action_filter')); ?>

<?php echo reset_tag(__('Cancel'), array('class' => 'picto action_cancel')); ?>
</form>

<?php include_partial("documents/google_search", array('module' => $module));

echo end_content_tag();

include_partial('common/content_bottom') ?>
