<?php
use_helper('FilterForm', 'General', 'MyForm');

// put focus on the name filed on window load
echo javascript_tag('focus_field = \'onam\';');

include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => true));
?>
<br />
<?php
echo '<div class="fieldname">' . picto_tag('picto_outings') . __('name') . ' </div>' . input_tag('onam');
echo georef_selector('With GPS track:');
include_partial('summits/summits_short_filter');
include_partial('huts/huts_short_filter');
include_partial('parkings/parkings_filter');
echo __('outing_with_public_transportation') . ' ' . bool_selector('owtp');
include_partial('routes_filter', array('activities' => $activities));
?>
<br />
<?php
echo __('Date') . __('&nbsp;:') . ' ' . date_selector(array('month' => false, 'year' => true, 'day' => true));
?>
<br />
<?php echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('ocult') ?>
<br />
<?php
include_partial('documents/filter_sort');
?>
<br />
<?php
echo label_for('cond', __('Show conditions'), array('title' => __('show conditions of the outings'))) . ' ' . checkbox_tag('cond', 1, false);
