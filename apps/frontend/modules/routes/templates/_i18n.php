<?php 
use_helper('Field'); 

echo field_text_data($document, 'description');
echo field_text_data_if_set($document, 'remarks');
echo field_text_data_if_set($document, 'gear');
echo field_text_data_if_set($document, 'external_resources');
