<?php
use_helper('FilterForm', 'General');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'cnam\';');

echo picto_tag('picto_articles') . __('name:') . ' ' . input_tag('cnam');
echo __('categories') . ' ' . field_value_selector('ccat', 'mod_articles_categories_list', false, false, true, 9);
?>
<br />
<?php
echo __('article_type') . ' ' . field_value_selector('ctyp', 'mod_articles_article_types_list', true);
?>
<br /><br />
<?php
echo __('activities') . ' ' . activities_selector();
include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => false));
include_partial('documents/filter_sort');
