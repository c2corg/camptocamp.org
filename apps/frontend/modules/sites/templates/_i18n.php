<?php
use_helper('Field');

echo field_text_data($document, 'description', null, $needs_translation);
echo field_text_data_if_set($document, 'remarks', null, $needs_translation);
echo field_text_data_if_set($document, 'pedestrian_access', null, $needs_translation);
echo field_text_data_if_set($document, 'way_back', null, $needs_translation);
echo field_text_data_if_set($document, 'site_history', null, $needs_translation);
