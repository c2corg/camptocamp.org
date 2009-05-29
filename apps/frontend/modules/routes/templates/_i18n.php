<?php 
use_helper('Field'); 

echo field_text_data($document, 'description', null, $needs_translation);
echo field_text_data_if_set($document, 'remarks', null, $needs_translation);
echo field_text_data_if_set($document, 'gear', null, $needs_translation);
echo field_text_data_if_set($document, 'external_resources', null, $needs_translation);
echo field_text_data_if_set($document, 'route_history', null, $needs_translation);
