<?php
use_helper('Field'); 
echo field_text_data_if_set($document, 'description', null, array('needs_translation' => $needs_translation, 'images' => $images));
echo field_text_data_if_set($document, 'hours', null, array('needs_translation' => $needs_translation, 'show_images' => false));
echo field_text_data_if_set($document, 'access', null, array('needs_translation' => $needs_translation, 'images' => $images));
