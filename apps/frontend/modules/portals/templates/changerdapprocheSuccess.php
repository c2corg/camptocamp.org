<?php
use_helper('Home', 'Language', 'Sections', 'Viewer', 'General', 'Field', 'AutoComplete', 'sfBBCode', 'SmartFormat', 'Button'); 

$culture = $sf_user->getCulture();
$connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_moderator);

$design_files = $document->get('design_file');
$design_files = explode(',', $design_files);
if (count($design_files))
{
    $app_static_url = sfConfig::get('app_static_url');
    foreach ($design_files as $file)
    {
        $file = trim($file);
        if (!empty($file))
        {
            use_stylesheet($app_static_url . '/static/css/' . $file . '.css', 'custom');
        }
    }
}

echo init_js_var(true, 'home_nav', $connected);

echo '<div id="wrapper_context" class="home article">';

// lang-independent content starts here

?>
        <div id="home_background_left_content">
<?php

$abstract = $document->get('abstract');
$abstract = parse_links(parse_bbcode_abstract($abstract));
$title = __('changerdapproche');
$know_more_link = getMetaArticleRoute('cda_know_more', false);
include_partial('portals/welcome', array('title' => $title,
                                         'description' => $abstract,
                                         'know_more_link' => $know_more_link,
                                         'default_open' => true));

if ($connected)
{
    include_partial('portals/wizard_button', array('sf_cache_key' => $culture));
}

if ($has_videos)
{
    include_partial('portals/latest_videos', array('items' => $latest_videos, 'culture' => $culture, 'default_open' => true));
}
if ($has_images):
?>
        <div id="last_images">
            <?php
    $image_url_params = $sf_data->getRaw('image_url_params');
    $image_url_params = implode('&', $image_url_params);
    $custom_title_link = 'images/list';
    $custom_rss_link = 'images/rss';
    if (!empty($image_url_params))
    {
        $custom_title_link .= '?' . $image_url_params;
        $custom_rss_link .= '?' . $image_url_params;
    }
    include_partial('images/latest',
                    array('items' => $latest_images,
                          'culture' => $culture,
                          'default_open' => true,
                          'custom_title_link' => $custom_title_link,
                          'custom_rss_link' => $custom_rss_link));
            ?>
        </div>
<?php
endif;

include_partial('portals/prepare', array('content_id' => 'cda_prepare_outing_box',
                                         'default_open' => true));

?>
        </div>
        <div id="home_background_content">
<?php
if ($has_map)
{
    include_partial('documents/map_section', array('document' => $document,
                                                   'layers_list' => $map_filter['objects'],
                                                   'center' => $map_filter['center'],
                                                   'height' => $map_filter['height'],
                                                   'home_section' => true,
                                                   'section_title' => 'cda map title',
                                                   'show_map' => true,
                                                   'has_geom' => $has_geom));
}
?>
            <div id="home_left_content">
                <?php
if ($has_outings)
{
    $outing_url_params = $sf_data->getRaw('outing_url_params');
    $outing_url_params = implode('&', $outing_url_params) . '&orderby=date&order=desc';
    $custom_title_link = 'outings/list?' . $outing_url_params;
    $custom_rss_link = 'outings/rss?' . $outing_url_params;
    include_partial('outings/latest',
                    array('items' => $latest_outings,
                          'culture' => $culture,
                          'default_open' => true,
                          'custom_title_text' => __('Soft mobility outings'),
                          'custom_title_link' => $custom_title_link,
                          'custom_rss_link' => $custom_rss_link));
}
if ($has_articles)
{
    $article_url_params = $sf_data->getRaw('article_url_params');
    $article_url_params = implode('&', $article_url_params);
    $custom_title_link = 'articles/list';
    $custom_rss_link = 'articles/rss';
    if (!empty($article_url_params))
    {
        $custom_title_link .= '?' . $article_url_params;
        $custom_rss_link .= '?' . $article_url_params;
    }
    include_partial('articles/latest',
                    array('items' => $latest_articles,
                          'culture' => $culture,
                          'default_open' => true,
                          'custom_title_text' => __('Soft mobility articles'),
                          'custom_title_link' => $custom_title_link,
                          'custom_rss_link' => $custom_rss_link));
}
                ?>
            </div>
            <div id="home_right_content">
                <?php
if ($has_news)
{
    include_partial('documents/latest_mountain_news',
                    array('items' => $latest_mountain_news,
                          'culture' => $culture,
                          'default_open' => true));
}
if ($has_topics)
{
    include_partial('documents/latest_threads',
                    array('items' => $latest_threads,
                          'culture' => $culture,
                          'default_open' => true));
}

                ?>
            </div>
<?php
if ($is_not_archive)
{
    echo '<div id="fake_clear"> &nbsp;</div>';
    include_partial('portals/inside_search_form', array('document' => $document));
}

// lang-dependent content
if ($has_description)
{
    echo '<div class="article_contenu">';
    include_partial('portals/i18n', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                          'needs_translation' => $needs_translation, 'images' => $associated_images));
    echo '</div>';
}
?>
        </div>
        <div id="fake_clear"> &nbsp;</div>
