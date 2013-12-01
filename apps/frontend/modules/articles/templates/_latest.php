<div id="last_articles" class="latest">
<?php
use_helper('Text', 'sfBBCode', 'SmartFormat', 'SmartDate', 'Button');
if (!isset($default_open))
{
    $default_open = true;
}

if (isset($custom_title_text))
{
    $custom_title_text = $sf_data->getRaw('custom_title_text');
}
else
{
    $custom_title_text = '';
}

if (isset($custom_footer_text))
{
    $custom_footer_text = $sf_data->getRaw('custom_footer_text');
}
else
{
    $custom_footer_text = __('articles list');
}

if (isset($custom_title_link))
{
    $custom_title_link = $sf_data->getRaw('custom_title_link');
}
else
{
    $custom_title_link = '@default_index?module=articles';
}

if (isset($custom_rss_link))
{
    $custom_rss_link = $sf_data->getRaw('custom_rss_link');
}
else
{
    $custom_rss_link = '';
}

include_partial('documents/home_section_title',
                array('module' => 'articles',
                      'custom_title_text' => $custom_title_text,
                      'custom_title_link' => $custom_title_link,
                      'custom_rss_link' => $custom_rss_link)); ?>

<div class="home_container_text" id="last_articles_section_container">
<?php if (count($items) == 0): ?>
    <p><?php echo __('No recent changes available') ?></p>
<?php else: ?>
    <ul class="article_changes">
    <?php 
    $date = $list_item = 0;
    foreach ($items as $item): ?>
        <?php
            // Add class to know if li is odd or even
            if ($list_item%2 == 1): ?>
                <li class="odd">
            <?php else: ?>
                <li class="even">
            <?php endif;
            $list_item++;

            $id = $item['id'];
            $lang = $item['ArticleI18n'][0]['culture']; ?>
            <span class="home_article_title">
            <?php echo link_to($item['ArticleI18n'][0]['name'], 
                               "@document_by_id_lang_slug?module=articles&id=$id&lang=$lang&slug="
                                   . make_slug($item['ArticleI18n'][0]['name']),
                               array('hreflang' => $lang)); ?>
            </span>
            <?php echo truncate_article_abstract(parse_links(parse_bbcode_abstract($item['ArticleI18n'][0]['abstract'])),
                                                 sfConfig::get('app_recent_documents_articles_abstract_characters_limit')); ?>
            </li>
    <?php endforeach ?>
    </ul>
<?php endif; ?>
<div class="home_link_list">
<?php echo link_to($custom_footer_text, $custom_title_link)
           . ' - ' .  link_to(__('Summary'), getMetaArticleRoute('home_articles')); ?>
</div>
</div>
<?php
$cookie_position = array_search('last_articles', sfConfig::get('app_personalization_cookie_fold_positions'));
echo javascript_tag('C2C.setSectionStatus(\'last_articles\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').");");
?>
</div>
