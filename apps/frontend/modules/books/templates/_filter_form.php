<?php
use_helper('FilterForm', 'General');

if (!c2cTools::mobileVersion())
{
   // put focus on the name field on dom load
   echo javascript_tag('if (!("autofocus" in document.createElement("input"))) {
   document.observe(\'dom:loaded\', function() { $(\'bnam\').focus(); })};');
}
?>
<div class="fieldgroup">
<?php
echo '<div class="fieldname">' . picto_tag('picto_books') . __('name') . ' </div>' . input_tag('bnam', null, array('autofocus' => 'autofocus'));
echo '<br /><br /><div class="fieldname">' . __('author') . ' </div>' . input_tag('auth');
echo '<br /><br /><div class="fieldname">' . __('editor') . ' </div>' . input_tag('edit');
?>
</div>
<?php
echo __('book_types') . ' ' . field_value_selector('btyp', 'mod_books_book_types_list', false, false, true);
echo __('langs') . ' ' . field_value_selector('blang', 'app_languages_book', false, false, true);
?>
<br />
<?php
include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => false));
$activities_raw = $sf_data->getRaw('activities');
echo __('activities') . ' ' . activities_selector(false, true, $activities_raw);
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('bcult');
?>
<br /><br />
<?php
include_partial('documents/filter_sort');
