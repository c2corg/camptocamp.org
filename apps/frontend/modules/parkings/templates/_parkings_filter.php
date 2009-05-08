<?php
use_helper('General');
?>
<br />
<?php
echo '<div class="fieldname">' . picto_tag('picto_parkings') . __('Access point:') . ' </div>' . input_tag('pnam');
echo __('elevation') . ' ' . elevation_selector('palt');
?>
<br />
<?php
echo __('public_transportation_rating short') . ' ' . field_value_selector('tp', 'app_parkings_public_transportation_ratings', false, false, true);
echo __('public_transportation_types short') . ' ' . field_value_selector('tpty', 'app_parkings_public_transportation_types', false, false, true);
