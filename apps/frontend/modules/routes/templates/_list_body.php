<td><?php echo link_to($item['associations'][0]['Summit'][0]['SummitI18n'][0]['name'] . ' : ' . $item['RouteI18n'][0]['name'],
                       '@document_by_id_lang_slug?module=routes&id=' . $item['RouteI18n'][0]['id'] . '&lang=' . $item['RouteI18n'][0]['culture'] .
                       '&slug=' . formate_slug($item['associations'][0]['Summit'][0]['SummitI18n'][0]['search_name'] . '-' . $item['RouteI18n'][0]['search_name'])) ?></td>
<td><?php echo get_paginated_activities($item['activities']) ?></td>
<td><?php echo get_paginated_value($item['facing'], 'app_routes_facings') ?></td>
<td><?php $height_diff_up = is_scalar($item['height_diff_up']) ? ($item['height_diff_up'] . __('meters')) : NULL;
          if (($height_diff_up != NULL) && is_scalar($item['difficulties_height']))
          {
              $height_diff_up .= ' (' . $item['difficulties_height'] . __('meters') . ')';
          }
          echo $height_diff_up ?></td>
<td><?php echo field_route_ratings_data($item, false) // helper is included in _list_header ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (strlen($item['geom_wkt'])) ? __('yes') : __('no') ;?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?  $item['nb_comments'] : '' ;?></td>
