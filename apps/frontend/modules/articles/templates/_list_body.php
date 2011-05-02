<?php
use_helper('Field', 'Link');

$item_i18n = $item['ArticleI18n'][0];
?>
<td><?php echo list_link($item_i18n, 'articles') ?></td>
<td><?php echo get_paginated_value_from_list($item['categories'], 'mod_articles_categories_list') ?></td>
<td><?php echo get_paginated_activities($item['activities']) ?></td>
<td><?php echo get_paginated_value($item['article_type'], 'mod_articles_article_types_list') ?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=articles&id='
        . $item_i18n['id'] . '&lang=' . $item_i18n['culture'])
    : '' ;?></td>
