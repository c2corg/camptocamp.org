<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag('Event.observe(window, \'load\', function(){$(\'bnam\').focus();});');

echo __('Name:') . ' ' . input_tag('bnam') . ' ';
echo __('book_types') . ' ' . field_value_selector('btyp', 'mod_books_book_types_list', false, false, true);
?>
<br />
<?php
echo __('author') . ' ' . input_tag('auth') . ' ';
echo __('editor') . ' ' . input_tag('edit');
?>
<br />
<?php
echo __('activities') . ' ' . activities_selector();
include_partial('documents/filter_sort');
