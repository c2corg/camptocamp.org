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
echo __('is_staffed') . ' ' . bool_selector('hsta');
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
echo __('has_unstaffed_matress') . ' ' . bool_selector('hmat');
echo __('has_unstaffed_blanket') . ' ' . bool_selector('hbla');
echo __('has_unstaffed_gas') . ' ' . bool_selector('hgas');
echo __('has_unstaffed_wood') . ' ' . bool_selector('hwoo');
