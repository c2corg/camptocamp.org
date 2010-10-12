<?php
use_helper('FilterForm', 'General');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'cnam\';');

?>
<div class="fieldgroup">
<?php
echo '<div class="fieldname">' . picto_tag('picto_articles') . __('name') . ' </div>' . input_tag('cnam');
echo '<br /><br /><div class="fieldname">' . __('article_type') . ' </div>' . field_value_selector('ctyp', 'mod_articles_article_types_list', true);
?>
</div>
<?php
echo __('categories') . ' ' . field_value_selector('ccat', 'mod_articles_categories_list', false, false, true, 9);
?>
<br />
<?php echo __('activities') . ' ' . activities_selector();
include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => false));
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('ccult');
?><br /><?php
include_partial('documents/filter_sort');
