<?php
use_helper('Field'); 

echo field_text_data($document, 'description', 'road access');
if ($document->get('geom_wkt'))
{
    field_getdirections($sf_params->get('id'));
}
echo field_text_data_if_set($document, 'public_transportation_description');
echo field_text_data_if_set($document, 'snow_clearance_comment');
echo field_text_data_if_set($document, 'accommodation');
