<?php
use_helper('MyForm');
echo form_tag('documents/search', array('method' => 'get', 'class' => 'search'));
echo '<div class="sbox">';
echo search_box_tag();
echo '</div></form>';
?>
