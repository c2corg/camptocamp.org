<?php
use_helper('Field'); 

echo field_text_data($document, 'description', 'road access');
echo field_text_data_if_set($document, 'public_transportation_description');
echo field_text_data_if_set($document, 'snow_clearance_comment');
echo field_text_data_if_set($document, 'accommodation');
