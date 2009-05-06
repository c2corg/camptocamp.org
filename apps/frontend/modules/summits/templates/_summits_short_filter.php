<?php
use_helper('General');
?>
<br />
<?php
echo '<div class="fieldname">' . picto_tag('picto_summits') . __('Summit:') . ' </div>' . input_tag('snam');
echo __('elevation') . ' ' . elevation_selector('salt');
