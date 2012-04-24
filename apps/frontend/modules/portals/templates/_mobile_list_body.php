<?php
use_helper('Field', 'Link');

$item_i18n = $item['PortalI18n'][0];
$item_id = $item_i18n['id'];
?>
<div class="right"><?php echo get_paginated_activities($item['activities']) ?></div>
<div><?php
echo list_link($item_i18n, 'portals');
?></div>
<div><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></div>
<div><?php echo picto_tag('picto_images', __('nb_linked_images')), ' ', (isset($item['nb_images'])) ?  $item['nb_images'] : '0', ' ',
                picto_tag('action_comment', __('nb_comments')), ' ', (isset($item['nb_comments'])) ?
                    link_to($item['nb_comments'], '@document_comment?module=portals&id='
                    . $item_i18n['id'] . '&lang=' . $item_i18n['culture']) : '0'; ?></div>
