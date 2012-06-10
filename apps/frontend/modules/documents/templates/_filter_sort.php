<br />
<?php
echo '<div class="orderform">' . __('Display');

$npp_options = options_for_select(array('10' => 10, '20' => 20, '30' => 30, '40' => 40, '50' => 50, '100' => 100), c2cTools::mobileVersion() ? 20 : 30);
echo select_tag('npp', $npp_options);
echo __('items per page');

$conf = 'mod_' . $sf_context->getModuleName() . '_sort_criteria';
$sort_fields = array_map('translate_sort_param', sfConfig::get($conf));

// First criteria of order
if (!isset($orderby_default))
{
    $orderby_default = '';
}
$orderby_options = options_for_select($sort_fields, $orderby_default, array('include_blank' => true));
echo select_tag('orderby', $orderby_options);

if (!isset($order_default))
{
    $order_default = '';
}
$order_options = options_for_select(array('asc' => __('ascending'), 'desc' => __('descending')),
                                    $order_default, array('include_blank' => true));
echo select_tag('order', $order_options);

// Second criteria of order
echo '<br />' . __('then by');

if (!empty($orderby_default))
{
    $orderby2_options = options_for_select($sort_fields, '', array('include_blank' => true));
}
else
{
    $orderby2_options = $orderby_options;
}
echo select_tag('orderby2', $orderby2_options);

if (!empty($order_default))
{
    $order2_options = options_for_select(array('asc' => __('ascending'), 'desc' => __('descending')),
                                         '', array('include_blank' => true));
}
else
{
    $order2_options = $order_options;
}
echo select_tag('order2', $order2_options);

echo '</div>';
?>
<br />
