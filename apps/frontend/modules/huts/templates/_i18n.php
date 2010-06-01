<?php
use_helper('Field');

if ($document->get('shelter_type') == 5)
{
    $access_label = 'access';
}
else
{
    $access_label = null;
}

echo field_text_data_if_set($document, 'staffed_period', null, array('needs_translation' => $needs_translation, 'images' => $images));
echo field_text_data_if_set($document, 'description', null, array('needs_translation' => $needs_translation, 'images' => $images));
echo field_text_data_if_set($document, 'pedestrian_access', $access_label, array('needs_translation' => $needs_translation, 'images' => $images));
