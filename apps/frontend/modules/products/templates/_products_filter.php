<?php
use_helper('General');
?>
<br />
<?php
echo picto_tag('picto_products') . __('Product:') . ' ' . (isset($autofocus) ? input_tag('fnam', null, array('autofocus' => 'autofocus')) : input_tag('fnam'));
echo __('elevation') . ' ' . elevation_selector('falt');
?>
<br />
<?php
echo __('product_type') . ' ' . field_value_selector('ftyp', 'mod_products_types_list', array('keepfirst' => false, 'multiple' => true));
