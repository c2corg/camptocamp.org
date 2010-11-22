<?php use_helper('General');

$item_i18n = $item['RouteI18n'][0];
$item_id = $item_i18n['id'];
$item_culture = $item_i18n['culture'];
?>
<td><input type="checkbox" value="<?php echo $item_id ;?>" name="id[]"/></td>
<td><?php
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
echo link_to($summit_name . __('&nbsp;:') . ' ' . $item_i18n['name'],
             "@document_by_id_lang_slug?module=routes&id=$item_id&lang=$item_culture&slug=" . make_slug($summit_name . '-' . $item_i18n['name'])) .
      ' ' . $has_gps_track;

if (isset($item['name']) && $summit_name != $summit_2['name'])
{
    $link = link_to($summit_2['name'],
                    '@document_by_id_lang_slug?module=summits&id=' . $summit_2['id'] .
                    '&lang=' .  $summit_2['culture'] .
                    '&slug=' . make_slug($summit_2['name']));
    echo '<br /><small>', __('route linked with', array('%1%' => $link)), '</small>';
}
?></td>
<td><?php echo get_paginated_activities($item['activities']) ?></td>
<td><?php echo displayWithSuffix($item['max_elevation'], 'meters') ?></td>
<td><?php echo get_paginated_value($item['facing'], 'app_routes_facings') ?></td>
<td><?php $height_diff_up = is_scalar($item['height_diff_up']) ? ($item['height_diff_up'] . __('meters')) : NULL;
          if (($height_diff_up != NULL) && is_scalar($item['difficulties_height']))
          {
              $height_diff_up .= ' (' . $item['difficulties_height'] . __('meters') . ')';
          }
          echo $height_diff_up ?></td>
<td><?php echo field_route_ratings_data($item, false) // helper is included in _list_header ?></td>
<td><?php if (isset($item['linked_docs']))
              include_partial('parkings/parkings4list', array('parkings' => $item['linked_docs'])) ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], "@document_comment?module=routes&id=$item_id&lang=$item_culture")
    : '' ;?></td>
<td><?php
if (isset($item['nb_linked_docs']))
{
    $nb_linked_docs = $item['nb_linked_docs'];
    if ($nb_linked_docs > 0)
    {
        echo link_to($nb_linked_docs, "outings/conditions?routes=$item_id&orderby=date&order=desc");
    }
    else
    {
        echo $nb_linked_docs;
    }
}
?></td>

