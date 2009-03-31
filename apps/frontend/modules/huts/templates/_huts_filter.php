<br />
<?php
echo __('Name:') . ' ' . input_tag('hnam');
echo __('elevation') . ' ' . elevation_selector('halt');
?>
<br />
<?php
echo __('shelter_type') . ' ' . field_value_selector('styp', 'mod_huts_shelter_types_list', false, false, true);
echo __('is_staffed') . ' ' . bool_selector('ista');
?>
<br />
<?php
echo __('staffed_capacity') . ' ' . elevation_selector('scap', null);
?>
<br />
<?php
echo __('unstaffed_capacity') . ' ' . elevation_selector('ucap', null);
?>
<br />
<?php
echo __('has_unstaffed_matress') . ' ' . bool_selector('hmat');
echo __('has_unstaffed_blanket') . ' ' . bool_selector('hbla');
?>
<br />
<?php
echo __('has_unstaffed_gas') . ' ' . bool_selector('hgas');
echo __('has_unstaffed_wood') . ' ' . bool_selector('hwoo');
