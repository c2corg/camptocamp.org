<?php
use_helper('MyForm');
echo '<div class="sbox">';
echo form_tag('documents/search', array('method' => 'get', 'class' => 'search'));
echo search_box_tag();
echo '</form></div>';
?>
