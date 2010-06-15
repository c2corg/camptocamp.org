<?php
use_helper('MyForm');
echo form_tag('documents/search', array('method' => 'get', 'class' => 'search'));
echo '<div class="sbox">';
$prefix = isset($prefix) ? $prefix : '';
$autocomplete = c2cTools::mobileVersion() ? false : (isset($autocomplete) ? $autocomplete : true);
echo search_box_tag($prefix, $autocomplete);
echo '</div></form>';
?>
