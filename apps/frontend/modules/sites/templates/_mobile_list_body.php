<?php
use_helper('Field', 'Link');

$item_i18n = $item['SiteI18n'][0];
?>
<div><?php echo list_link($item_i18n, 'sites') ?></div>
<div><?php
$routes_quantity = $item['routes_quantity'];
if (is_scalar($routes_quantity) && $routes_quantity > 0)
{
    $routes_quantity = __('%1% routes_quantity', array('%1%' => $routes_quantity));
}
else
{
    $routes_quantity = '';
}

echo _implode(' - ', array(displayWithSuffix($item['elevation'], 'meters'),
                           get_paginated_value_from_list($item['site_types'], 'app_sites_site_types'),
                           $routes_quantity,
                           get_paginated_value_from_list($item['rock_types'], 'mod_sites_rock_types_list')));
?></div>


<div><?php
if (isset($item['linked_docs']))
{
    echo __('access'), ' ';
    include_partial('parkings/parkings4list', array('parkings' => $item['linked_docs']));
}
?></div>
<div><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></div>
<div><?php echo picto_tag('picto_images', __('nb_linked_images')), ' ', (isset($item['nb_images'])) ?  $item['nb_images'] : '0', ' ',
                picto_tag('action_comment', __('nb_comments')), ' ', (isset($item['nb_comments'])) ?
                    link_to($item['nb_comments'], '@document_comment?module=sites&id='
                    . $item_i18n['id'] . '&lang=' . $item_i18n['culture']) : '0', ' ',
                picto_tag('picto_outings', __('nb_outings')), ' ', (isset($item['nb_linked_docs'])) ?  $item['nb_linked_docs'] : '0';?>
</div>
