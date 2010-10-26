<?php
use_helper('FilterForm', 'General');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'bnam\';');

?>
<div class="fieldgroup">
<?php
echo '<div class="fieldname">' . picto_tag('picto_books') . __('name') . ' </div>' . input_tag('bnam');
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
echo __('activities') . ' ' . activities_selector(false, true);
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('bcult');
?>
<br /><br />
<?php
include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => false));
include_partial('documents/filter_sort');
