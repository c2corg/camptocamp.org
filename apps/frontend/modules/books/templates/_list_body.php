<?php
use_helper('Field');

$item_i18n = $item['BookI18n'][0];
?>
<td><?php echo link_to($item_i18n['name'],
                       '@document_by_id_lang_slug?module=books&id=' . $item_i18n['id'] . '&lang=' . $item_i18n['culture'] . '&slug=' . make_slug($item_i18n['name']),
                       ($item_i18n['culture'] != $sf_user->getCulture() ? array('hreflang' => $item_i18n['culture']) : array())) ?></td>
<td><?php echo $item['author'] ?></td>
<td><?php echo $item['editor'] ?></td>
<td><?php echo $item['publication_date'] ?></td>
<td><?php echo get_paginated_activities($item['activities']) ?></td>
<td><?php echo get_paginated_value_from_list($item['book_types'], 'mod_books_book_types_list') ?></td>
<td><?php echo get_paginated_value_from_list($item['langs'], 'app_languages_book') ?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=books&id='
        . $item_i18n['id'] . '&lang=' . $item_i18n['culture'])
    : '' ;?></td>
