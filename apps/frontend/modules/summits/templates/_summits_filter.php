<?php
echo __('Summit:') . ' ' . input_tag('snam') . ' ';
echo __('elevation') . ' ' . elevation_selector('salt');
echo '<br />';
echo __('summit_type') . ' ' . topo_dropdown('styp', 'mod_summits_summit_types_list', true, true, true, true) . ' ';
