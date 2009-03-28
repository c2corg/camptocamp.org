<br />
<?php
echo __('Name:') . ' ' . input_tag('hnam');
echo __('elevation') . ' ' . elevation_selector('halt');
?>
<br />
<?php
echo __('shelter_type') . ' ' . field_value_selector('styp', 'mod_huts_shelter_types_list', false, false, true);