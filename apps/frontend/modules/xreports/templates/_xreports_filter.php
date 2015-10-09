<?php
use_helper('General');

echo '<br />';
echo picto_tag('picto_xreports') . __('name') . ' ' . (isset($autofocus) ? input_tag('xnam', null, array('autofocus' => 'autofocus')) : input_tag('xnam'));
echo __('elevation') . ' ' . elevation_selector('falt');
echo '<br />';
$activities_raw = $sf_data->getRaw('activities');
echo    '<div class="section_subtitle">' . __('activities') . '</div>'
      . activities_selector(true, true, $activities_raw);
echo    '<div class="col_left">'
      . '<div class="section_subtitle">' . __('event_type') . '</div>'
      . field_value_selector('xtyp', 'mod_xreports_event_type_list', array('keepfirst' => false, 'multiple' => true))
      . '</div>';
echo    '<div class="col">'
      . '<div class="section_subtitle">' . __('severity') . '</div>'
      . field_value_selector('xsev', 'mod_xreports_severity_list', array('keepfirst' => false, 'multiple' => true))
      . '<br />'
      . __('rescue') . ' ' . bool_selector('xres')
      . '</div>';
echo '<p></p>';
echo __('nb_participants') . ' ' . elevation_selector('xpar', '');
echo '<br />';
echo __('nb_impacted') . ' ' . elevation_selector('ximp', '');
echo '<br /><br />';
echo __('Date') . __('&nbsp;:') . ' ' . date_selector(array('month' => true, 'year' => true, 'day' => true));
