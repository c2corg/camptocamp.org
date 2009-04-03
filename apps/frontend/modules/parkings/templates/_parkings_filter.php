<br />
<?php
echo __('Access point:') . ' ' . input_tag('pnam');
echo __('elevation') . ' ' . elevation_selector('palt');
?>
<br />
<?php
echo __('public_transportation_rating short') . ' ' . tp_selector();