<?php
use_helper('General');
?>
<br />
<?php
echo picto_tag('picto_huts') . __('Hut:') . ' ' . (isset($autofocus) ? input_tag('hnam', null, array('autofocus' => 'autofocus')) : input_tag('hnam'));
echo __('elevation') . ' ' . elevation_selector('halt');
?>
<br />
<?php
echo __('shelter_type') . ' ' . field_value_selector('htyp', 'mod_huts_shelter_types_list', array('keepfirst' => false, 'multiple' => true));
echo __('is_staffed') . __('&nbsp;:') . ' ' . bool_selector('hsta');
?>
<br />
<?php
echo __('staffed_capacity') . ' ' . elevation_selector('hscap', null);
?>
<br />
<?php
echo __('unstaffed_capacity') . ' ' . elevation_selector('hucap', null);
?>
<br />
<?php

echo __('has_unstaffed_matress') . ' ' . field_value_selector('hmat','app_boolean_list' );
echo __('has_unstaffed_blanket') . ' ' . field_value_selector('hbla','app_boolean_list' );
echo __('has_unstaffed_gas') . ' ' . field_value_selector('hgas','app_boolean_list' );
echo __('has_unstaffed_wood') . ' ' . field_value_selector('hwoo','app_boolean_list' );

  
