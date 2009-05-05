<br />
<?php
echo '<div class="picto picto_huts"></div>';
echo '<div class="fieldname">' . __('Hut:') . ' </div>' . input_tag('hnam');
echo __('elevation') . ' ' . elevation_selector('halt');
echo __('is_staffed') . ' ' . bool_selector('hsta');