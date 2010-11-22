<div><?php echo link_to($item['MapI18n'][0]['name'], '@document_by_id_lang_slug?module=maps&id=' . $item['MapI18n'][0]['id']
                                                    . '&lang=' . $item['MapI18n'][0]['culture']
                                                    . '&slug=' . make_slug($item['MapI18n'][0]['name'])) ?></div>
<div><?php echo get_paginated_value($item['editor'], 'mod_maps_editors_list'), ' ',
                $item['code'], ' - ',
                get_paginated_value($item['scale'], 'mod_maps_scales_list') ?></div>
<div><?php echo picto_tag('picto_images', __('nb_linked_images')), ' ', (isset($item['nb_images'])) ?  $item['nb_images'] : '0', ' ',
                picto_tag('action_comment', __('nb_comments')), ' ', (isset($item['nb_comments'])) ?
                    link_to($item['nb_comments'], '@document_comment?module=summits&id='
                    . $item_i18n['id'] . '&lang=' . $item_i18n['culture']) : '0'; ?></div>
