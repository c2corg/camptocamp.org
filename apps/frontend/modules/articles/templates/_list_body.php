<td><?php echo link_to($item['ArticleI18n'][0]['name'], '@document_by_id_lang?module=articles&id=' . $item['ArticleI18n'][0]['id']
                                                        . '&lang=' . $item['ArticleI18n'][0]['culture']) ?></td>
<td><?php echo get_paginated_value_from_list($item['categories'], 'mod_articles_categories_list') ?></td>
<td><?php echo get_paginated_activities($item['activities']) ?></td>
<td><?php echo get_paginated_value($item['article_type'], 'mod_articles_article_types_list') ?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?  $item['nb_comments'] : '' ;?></td>
