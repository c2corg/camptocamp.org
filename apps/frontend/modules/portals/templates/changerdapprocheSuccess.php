<?php
use_helper('Home', 'Language', 'Sections', 'Viewer', 'General', 'Field', 'AutoComplete', 'sfBBCode', 'SmartFormat'); 

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
echo '<link href="' . sfConfig::get('app_static_url') . '/static/css/changerdapproche.css" media="all" type="text/css" rel="stylesheet">';

echo init_js_var(true, 'home_nav', $connected);

echo '<div id="wrapper_context" class="home">';

// lang-independent content starts here

if ($is_not_archive)
{
    include_partial('portals/inside_search_form', array('document' => $document));
}

if ($has_map)
{
    include_partial('documents/map_section', array('document' => $document,
                                                   'layers_list' => $map_filter['objects'],
                                                   'extent' => $map_filter['extent'],
                                                   'height' => $map_filter['height'],
                                                   'home_section' => true,
                                                   'section_title' => 'cda map title',
                                                   'show_map' => true));
}

?>
        <div id="home_background_left_content">
<?php

if ($connected)
{
    include_partial('portals/wizard_button', array('sf_cache_key' => $culture));
}

$abstract = $document->get('abstract');
$abstract = parse_links(parse_bbcode_simple($abstract));
$title = __('changerdapproche');
include_partial('portals/welcome', array('title' => $title,
                                         'description' => $abstract,
                                         'default_open' => true));


if ($has_images):
?>
        <div id="last_images">
            <?php
    $image_url_params = $sf_data->getRaw('image_url_params');
    $image_url_params = implode('&', $image_url_params);
    $custom_title_link = 'images/list?' . $image_url_params;
    $custom_rss_link = 'images/rss?' . $image_url_params;
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

?>
        </div>
        <div id="home_background_content">
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
    $custom_title_link = 'articles/list?' . $article_url_params;
    $custom_rss_link = 'articles/rss?' . $article_url_params;
    include_partial('articles/latest',
                    array('items' => $latest_articles,
                          'culture' => $culture,
                          'default_open' => true,
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
        </div>
        <div id="fake_clear"> &nbsp;</div>

<?php
// lang-dependent content
if ($has_description)
{
    echo start_section_tag('Description', 'description', 'opened', false, false, false, false);
    include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                    'needs_translation' => $needs_translation, 'images' => $associated_images));
    echo end_section_tag();
    
    include_partial('documents/license', array('license' => 'by-sa'));
}
?>
