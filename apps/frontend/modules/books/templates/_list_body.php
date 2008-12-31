<td><?php echo link_to($item['BookI18n'][0]['name'], '@document_by_id_lang_slug?module=books&id=' . $item['BookI18n'][0]['id']
                                                     . '&lang=' . $item['BookI18n'][0]['culture']
                                                     . '&slug=' . formate_slug($item['BookI18n'][0]['search_name'])) ?></td>
<td><?php echo $item['author'] ?></td>
<td><?php echo $item['editor'] ?></td>
<td><?php echo get_paginated_activities($item['activities']) ?></td>
<td><?php echo get_paginated_value_from_list($item['book_types'], 'mod_books_book_types_list') ?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?  $item['nb_comments'] : '' ;?></td>
