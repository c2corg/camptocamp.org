<br />
<?php
echo __('access_status') . ' ' . input_tag('pnam') . ' ';
echo __('elevation') . ' ' . elevation_selector('palt') . ' ';
echo __('public_transportation_rating short') . ' ' . tp_selector();