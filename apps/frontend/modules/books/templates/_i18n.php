<?php
use_helper('Field');
echo field_text_data_if_set($document, 'description', null, array('needs_translation' => $needs_translation, 'images' => $images, 'show_label' => false));
