<br />
<?php
echo __('access_status') . ' ' . input_tag('pnam') . ' ';
echo __('elevation') . ' ' . elevation_selector('palt') . ' ';
echo '<label for="tp">' . __('public_transportation_rating short') . '</label> ' . checkbox_tag('tp');
