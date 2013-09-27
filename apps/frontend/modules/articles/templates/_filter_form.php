<?php
use_helper('FilterForm', 'General');

if (!c2cTools::mobileVersion())
{
   // put focus on the name field on dom load
   echo javascript_tag('if (!("autofocus" in document.createElement("input"))) {
   document.observe(\'dom:loaded\', function() { $(\'cnam\').focus(); })};');
}
$ranges_raw = $sf_data->getRaw('ranges');
$selected_areas_raw = $sf_data->getRaw('selected_areas');
include_partial('areas/areas_selector', array('ranges' => $ranges_raw, 'selected_areas' => $selected_areas_raw, 'use_personalization' => false));
?>
<div class="fieldgroup">
<?php
echo '<div class="fieldname">' . picto_tag('picto_articles') . __('name') . ' </div>' . input_tag('cnam', null, array('autofocus' => 'autofocus'));
echo '<br /><br /><div class="fieldname">' . __('article_type') . ' </div>' . field_value_selector('ctyp', 'mod_articles_article_types_list', array('blank' => true));
?>
</div>
<?php
echo __('categories') . ' ' . field_value_selector('ccat', 'mod_articles_categories_list', array('keepfirst' => false, 'multiple' => true, 'size' => 9));
?>
<br />
<?php
$activities_raw = $sf_data->getRaw('activities');
echo __('activities') . ' ' . activities_selector(false, false, $activities_raw);
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('ccult');
?><br /><?php
include_partial('documents/filter_sort');
