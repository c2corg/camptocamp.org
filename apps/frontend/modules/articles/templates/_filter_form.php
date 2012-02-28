<?php
use_helper('FilterForm', 'General');

if (!c2cTools::mobileVersion())
{
   // put focus on the name field on dom load
   echo javascript_tag('if (!("autofocus" in document.createElement("input"))) {
   document.observe(\'dom:loaded\', function() { $(\'cnam\').focus(); })};');
}
?>
<div class="fieldgroup">
<?php
include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => false));
echo '<div class="fieldname">' . picto_tag('picto_articles') . __('name') . ' </div>' . input_tag('cnam', null, array('autofocus' => 'autofocus'));
echo '<br /><br /><div class="fieldname">' . __('article_type') . ' </div>' . field_value_selector('ctyp', 'mod_articles_article_types_list', true);
?>
</div>
<?php
echo __('categories') . ' ' . field_value_selector('ccat', 'mod_articles_categories_list', false, false, true, 9);
?>
<br />
<?php
$activities_raw = $sf_data->getRaw('activities');
echo __('activities') . ' ' . activities_selector(false, false, $activities_raw);
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('ccult');
?><br /><?php
include_partial('documents/filter_sort');
