<?php
use_helper('Field'); 
echo field_text_data_if_set($document, 'abstract', null, array('needs_translation' => $needs_translation, 'show_images' => false));
echo field_text_data_if_set($document, 'description', null, array('needs_translation' => $needs_translation, 'images' => $images));
