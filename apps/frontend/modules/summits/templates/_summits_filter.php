<?php
echo __('Summit:') . ' ' . input_tag('snam') . ' ';
echo __('elevation') . ' ' . elevation_selector('salt');
echo '<br />';
echo __('summit_type') . ' ' . field_value_selector('styp', 'mod_summits_summit_types_list', false, false, true) . ' ';
