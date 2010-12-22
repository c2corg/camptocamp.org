<?php
use_helper('Field');

$item_i18n = $item['BookI18n'][0];
?>
<div class="right"><?php echo get_paginated_activities($item['activities']) ?></div>
<div><?php echo link_to($item_i18n['name'], '@document_by_id_lang_slug?module=books&id=' . $item_i18n['id']
                                                     . '&lang=' . $item_i18n['culture']
                                                     . '&slug=' . make_slug($item_i18n['name'])) ?></div>
<div>
<?php echo _implode(' - ',
                    array($item['author'],
                          $item['editor'],
                          $item['publication_date'])); ?></div>
<div>
<?php echo _implode(' - ',
                    array(get_paginated_value_from_list($item['book_types'], 'mod_books_book_types_list'),
                          get_paginated_value_from_list($item['langs'], 'app_languages_book'))); ?></div>
<div><?php echo picto_tag('picto_images', __('nb_linked_images')), ' ', (isset($item['nb_images'])) ?  $item['nb_images'] : '0', ' ',
                picto_tag('action_comment', __('nb_comments')), ' ', (isset($item['nb_comments'])) ?
                    link_to($item['nb_comments'], '@document_comment?module=books&id='
                    . $item_i18n['id'] . '&lang=' . $item_i18n['culture']) : '0'; ?></div>
