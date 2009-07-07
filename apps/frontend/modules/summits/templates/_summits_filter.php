<?php
use_helper('General');

echo picto_tag('picto_summits') . __('Summit:') . ' ' . input_tag('snam');
echo __('elevation') . ' ' . elevation_selector('salt');
echo '<br />';
echo __('summit_type') . ' ' . field_value_selector('styp', 'app_summits_summit_types', false, false, true);
