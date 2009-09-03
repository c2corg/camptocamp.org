<?php 
use_helper('Field'); 
echo field_text_data($document, 'description', null, array('needs_translation' => $needs_translation, 'images' => $images));
