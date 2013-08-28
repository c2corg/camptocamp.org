<?php
use_helper('MyForm', 'Javascript');
echo picto_tag('action_cancel', __('close'), array('class' => 'tmp-image-close'));
echo $image_name;
echo global_form_errors_tag();
