<?php
use_helper('Field'); 
echo field_text_data_if_set($document, 'staffed_period', null, $needs_translation);
echo field_text_data($document, 'description', null, $needs_translation);
echo field_text_data_if_set($document, 'pedestrian_access', null, $needs_translation);
