<?php
use_helper('General');
?>
<br />
<?php
echo picto_tag('picto_xreports') . __('Xreport:') . ' ' . (isset($autofocus) ? input_tag('xnam', null, array('autofocus' => 'autofocus')) : input_tag('xnam'));
echo __('elevation') . ' ' . elevation_selector('falt');
echo georef_selector();
?>
<br />
<?php
$activities_raw = $sf_data->getRaw('activities');
echo activities_selector(true, true, $activities_raw);
echo __('event_type') . ' ' . field_value_selector('xtyp', 'mod_xreports_event_type_list', array('keepfirst' => false, 'multiple' => true));
echo __('severity') . ' ' . field_value_selector('xsev', 'mod_xreports_severity_list', array('keepfirst' => false, 'multiple' => true));
echo __('rescue') . ' ' . bool_selector('xres');
?>
<br />
<?php
echo __('nb_participants') . ' ' . elevation_selector('xpar', '');
echo __('nb_impacted') . ' ' . elevation_selector('ximp', '');
?>
<br /><br />
<?php
echo __('Date') . __('&nbsp;:') . ' ' . date_selector(array('month' => true, 'year' => true, 'day' => true));
