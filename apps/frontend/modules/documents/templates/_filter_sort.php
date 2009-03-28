<br />
<?php echo __('Display') ?>

<?php
$npp_options = options_for_select(array('10' => 10, '20' => 20, '30' => 30, '40' => 40, '50' => 50, '100' => 100), 30);
echo select_tag('npp', $npp_options);
?>

<?php echo __('items per page') ?>

<?php
$conf = 'mod_' . $sf_context->getModuleName() . '_sort_criteria';
$sort_fields = array_map('translate_sort_param', sfConfig::get($conf));
$orderby_options = options_for_select($sort_fields, '', array('include_blank' => true));
echo select_tag('orderby', $orderby_options);
?>

<?php
$order_options = options_for_select(array('asc' => __('ascending'), 'desc' => __('descending')),
                                    '', array('include_blank' => true));
echo select_tag('order', $order_options);
?>
<br />
