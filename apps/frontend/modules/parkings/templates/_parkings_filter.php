<br />
<?php
echo __('Access point:') . ' ' . input_tag('pnam');
echo __('elevation') . ' ' . elevation_selector('palt');
?>
<br />
<?php
echo __('public_transportation_rating short') . ' ' . tp_selector();
echo __('public_transportation_types') . ' ' . field_value_selector('tpty', 'app_parkings_public_transportation_types', false, false, true);
