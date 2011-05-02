<?php
use_helper('Field', 'Link');

$item_i18n = $item['ArticleI18n'][0];
?>
<div class="right"><?php echo get_paginated_activities($item['activities']) ?></div>
<div><?php echo list_link($item_i18n, 'articles') ?></div>
<div><?php echo _implode(' - ',
                         array(get_paginated_value($item['article_type'], 'mod_articles_article_types_list'),
                               get_paginated_value_from_list($item['categories'], 'mod_articles_categories_list'))) ?></div>
<div><?php echo picto_tag('picto_images', __('nb_linked_images')), ' ', (isset($item['nb_images'])) ?  $item['nb_images'] : '0', ' ',
                picto_tag('action_comment', __('nb_comments')), ' ', (isset($item['nb_comments'])) ?
                    link_to($item['nb_comments'], '@document_comment?module=articles&id='
                    . $item_i18n['id'] . '&lang=' . $item_i18n['culture']) : '0'; ?></div>
