<?php
use_helper('Field'); 

echo field_text_data($document, 'description', 'road access', array('needs_translation' => $needs_translation, 'images' => $images));
if ($document->get('geom_wkt'))
{
    echo field_getdirections($sf_params->get('id'));
}
echo field_text_data_if_set($document, 'public_transportation_description', null, array('needs_translation' => $needs_translation, 'images' => $images));
echo field_text_data_if_set($document, 'snow_clearance_comment', null, array('needs_translation' => $needs_translation, 'images' => $images));
echo field_text_data_if_set($document, 'accommodation', null, array('needs_translation' => $needs_translation, 'images' => $images));
