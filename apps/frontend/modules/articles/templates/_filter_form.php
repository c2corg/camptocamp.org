<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag('Event.observe(window, \'load\', function(){$(\'anam\').focus();});');

echo __('Name:') . ' ' . input_tag('anam') . ' ';
echo __('categories') . ' ' . field_value_selector('cat', 'mod_articles_categories_list', true) . ' ';
echo __('article_type') . ' ' . field_value_selector('atyp', 'mod_articles_article_types_list', true);
?>
<br />
<?php
echo __('activities') . ' ' . activities_selector();
include_partial('documents/filter_sort');
