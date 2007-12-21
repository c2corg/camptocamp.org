<?php
use_helper('FilterForm');

echo __('Name:') . ' ' . input_tag('bnam') . ' ';
echo __('book_types') . ' ' . field_value_selector('btyp', 'mod_books_book_types_list');
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
