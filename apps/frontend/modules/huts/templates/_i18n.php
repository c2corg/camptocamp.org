<?php
use_helper('Field'); 
echo field_text_data_if_set($document, 'staffed_period');
echo field_text_data($document, 'description');
echo field_text_data_if_set($document, 'pedestrian_access');
