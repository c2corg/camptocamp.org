<br />
<?php echo '<span class="lineform">' . __('Display') ?>

<?php
$npp_options = options_for_select(array('10' => 10, '20' => 20, '30' => 30, '40' => 40, '50' => 50, '100' => 100), c2cTools::mobileVersion() ? 20 : 30);
echo select_tag('npp', $npp_options);
?>

<?php echo __('items per page') ?>

<?php
$conf = 'mod_' . $sf_context->getModuleName() . '_sort_criteria';
$sort_fields = array_map('translate_sort_param', sfConfig::get($conf));
if (!isset($orderby_default))
{
    $orderby_default = '';
}
$orderby_options = options_for_select($sort_fields, $orderby_default, array('include_blank' => true));
echo select_tag('orderby', $orderby_options);
?>

<?php
if (!isset($order_default))
{
    $order_default = '';
}
$order_options = options_for_select(array('asc' => __('ascending'), 'desc' => __('descending')),
                                    $order_default, array('include_blank' => true));
echo select_tag('order', $order_options) . '</span>';
?>
<br />
