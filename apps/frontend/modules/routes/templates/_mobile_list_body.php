<?php use_helper('General', 'Field', 'Link');

$item_i18n = $item['RouteI18n'][0];
$item_id = $item_i18n['id'];
$item_culture = $item_i18n['culture'];
?>
<div class="right"><?php echo get_paginated_activities($item['activities']) ?></div>
<div><?php
if(strlen($item['geom_wkt']))
{
    $has_gps_track = picto_tag('action_gps', __('has GPS track'));
}
else
{
    $has_gps_track = '';
}
// in some cases (ticket #337, we have to add best summit name with a second request. It is
// then located in $item['name']
$summit_2 = $item['associations'][0]['Summit'][0]['SummitI18n'][0];
if (isset($item['name']))
{
    $summit_name = $item['name'];
}
else
{
    $summit_name = $summit_2['name'];
}
echo list_link($item_i18n, 'routes') .
      ' ' . $has_gps_track;

if (isset($item['name']) && $summit_name != $summit_2['name'])
{
    $link = link_to($summit_2['name'],
                    '@document_by_id_lang_slug?module=summits&id=' . $summit_2['id'] .
                        '&lang=' .  $summit_2['culture'] . '&slug=' . make_slug($summit_2['name']),
                    ($summit_2['culture'] != $sf_user->getCulture() ? array('hreflang' => $summit_2['culture']) : array()));
    echo '<br /><small>', __('route linked with', array('%1%' => $link)), '</small>';
}
?></div>
<div>
<?php
$height_diff_up = is_scalar($item['height_diff_up']) ? ($item['height_diff_up'] . __('meters')) : NULL;
if (($height_diff_up != NULL) && is_scalar($item['difficulties_height']))
{
    $height_diff_up .= ' (' . $item['difficulties_height'] . __('meters') . ')';
}
echo _implode(' - ', array(displayWithSuffix($item['max_elevation'], 'meters'),
                           get_paginated_value($item['facing'], 'app_routes_facings'),
                           $height_diff_up,
                           field_route_ratings_data($item, false))); ?></div>
<div><?php
if (isset($item['linked_docs']))
{
    echo __('access'), ' ';
    include_partial('parkings/parkings4list', array('parkings' => $item['linked_docs'])); 
} ?></div>
<div><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></div>
<div><?php echo picto_tag('picto_images', __('nb_linked_images')), ' ', (isset($item['nb_images'])) ?  $item['nb_images'] : '0', ' ',
                picto_tag('action_comment', __('nb_comments')), ' ', (isset($item['nb_comments'])) ?
                    link_to($item['nb_comments'], '@document_comment?module=routes&id='
                    . $item_i18n['id'] . '&lang=' . $item_i18n['culture']) : '0', ' ',
                picto_tag('picto_outings', __('nb_outings')), ' ', (isset($item['nb_linked_docs'])) ?  $item['nb_linked_docs'] : '0';?>
</div>
