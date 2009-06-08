<?php
use_helper('Form', 'Viewer', 'Javascript', 'Escaping');
$module = $sf_context->getModuleName();
$response = sfContext::getInstance()->getResponse();
$response->addJavascript('http://www.google.com/jsapi', 'last');
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

<div id="wrapper_context">
<div id="ombre_haut">
    <div id="ombre_haut_corner_right"></div>
    <div id="ombre_haut_corner_left"></div>
</div>

<div id="content_article">
<div id="article" class="article <?php echo $module . '_content'; ?>">

<?php
echo form_tag("/$module/filterredirect", array('id' => 'filterform'));

echo '<p class="list_header">' . __('Filter presentation').'</p>';
if (!isset($ranges)) $ranges = array();
include_partial("$module/filter_form", array('ranges' => $ranges));
?>
<br />
<br />
<?php echo submit_tag(__('Search')); ?>

<?php echo reset_tag(__('Cancel')); ?>
</form>

<?php include_partial("documents/google_search", array('module' => $module)); ?>

</div></div>
<?php include_partial('common/content_bottom') ?>
