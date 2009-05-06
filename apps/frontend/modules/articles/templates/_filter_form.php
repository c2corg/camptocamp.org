<?php
use_helper('FilterForm', 'General');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'anam\';');

echo picto_tag('picto_articles') . __('Name:') . ' ' . input_tag('anam');
echo __('categories') . ' ' . field_value_selector('cat', 'mod_articles_categories_list', true);
echo __('article_type') . ' ' . field_value_selector('atyp', 'mod_articles_article_types_list', true);
?>
<br /><br />
<?php
echo __('activities') . ' ' . activities_selector();
include_partial('documents/filter_sort');
