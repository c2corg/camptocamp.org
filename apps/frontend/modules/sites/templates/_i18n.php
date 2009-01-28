<?php
use_helper('Field');

echo field_text_data($document, 'description');
echo field_text_data_if_set($document, 'remarks'); 
echo field_text_data_if_set($document, 'pedestrian_access'); 
echo field_text_data_if_set($document, 'way_back'); 
echo field_text_data_if_set($document, 'site_history'); 
