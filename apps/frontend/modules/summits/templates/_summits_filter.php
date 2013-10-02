<?php
use_helper('General');

echo picto_tag('picto_summits') . __('Summit:') . ' ' . (isset($autofocus) ? input_tag('snam', null, array('autofocus' => 'autofocus')) : input_tag('snam'));
echo __('elevation') . ' ' . elevation_selector('salt');
echo '<br />';
echo __('summit_type') . ' ' . field_value_selector('styp', 'app_summits_summit_types', array('keepfirst' => false, 'multiple' => true, 'size' => 5));
