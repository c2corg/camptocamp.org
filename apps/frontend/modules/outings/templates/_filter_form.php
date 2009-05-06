<?php
use_helper('FilterForm', 'General');

// put focus on the name filed on window load
echo javascript_tag('focus_field = \'onam\';');

include_partial('areas/areas_selector', array('ranges' => $ranges));
?>
<br />
<?php
echo '<div class="fieldname">' . picto_tag('picto_outings') . __('name') . ' </div>' . input_tag('onam');
echo ' ' . georef_selector();
include_partial('summits/summits_short_filter');
include_partial('huts/huts_short_filter');
include_partial('parkings/parkings_filter');
echo __('outing_with_public_transportation') . ' ' . bool_selector('owtp');
include_partial('routes_filter');
?>
<br />
<?php
echo __('Date:') . ' ' . date_selector();
?>
<br />
<?php
include_partial('documents/filter_sort');
