<?php
use_helper('General');
?>
<br />
<?php
echo '<div class="fieldname">' . picto_tag('picto_huts') . __('Hut:') . ' </div>' . input_tag('hnam');
echo __('elevation') . ' ' . elevation_selector('halt');
echo __('is_staffed') . ' ' . bool_selector('hsta');