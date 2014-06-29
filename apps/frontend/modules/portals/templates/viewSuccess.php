<?php
use_helper('Home', 'Language', 'Sections', 'Viewer', 'General', 'Field', 'AutoComplete', 'Button', 'WikiTabs', 'sfBBCode', 'SmartFormat');

$culture = $sf_user->getCulture();
$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$mobile_version = c2cTools::mobileVersion();
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator && !$mobile_version);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_moderator && !$mobile_version);
$has_map = $document->getRaw('has_map');
$has_map = !empty($has_map);

$design_files = $document->get('design_file');
$design_files = explode(',', $design_files);
if (count($design_files))
{
    foreach ($design_files as $file)
    {
        $file = trim($file);
        if (!empty($file))
        {
            use_stylesheet('/static/css/' . $file . '.css', 'custom');
        }
    }
}

$lang = $document->getCulture();
$version = ($is_not_archive ? null : $document->getVersion());
$slug = get_slug($document);
if ($is_not_archive)
{
    $url = "@document_by_id_lang_slug?module=portals&id=$id&lang=$lang&slug=$slug";
}
else
{
    $url = "@document_by_id_lang_version?module=portals&id=$id&lang=$lang&version=$version";
}


//display_page_header('portals', $document, $id, $metadata, $current_version);

echo display_title($document->get('name'), 'portals', true, 'home_nav', $url);

if (!$mobile_version) // left navigation menus are only for web version
{
    echo '<div id="nav_space" class="nav_box">&nbsp;</div>';
    
    // TODO : change after creation of text field in portal doc
    // $title = $document->get('abstract_title');
    $title = __('home_welcome');
    $abstract = $document->get('abstract');
    $abstract = parse_links(parse_bbcode_abstract($abstract));
    include_partial('documents/welcome', array('sf_cache_key' => $id . '_' . $culture . '_' . $lang,
                                               'title' => $title,
                                               'description' => $abstract,
                                               'default_open' => true));
    
    include_partial('documents/wizard_button', array('sf_cache_key' => ($is_connected ? 'connected' : 'not_connected') . '_' . $culture));

    if ($has_images && $has_map)
    {
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
                              'custom_rss_link' => $custom_rss_link,
                              'home_section' => false));
    }

    include_partial('documents/prepare', array('sf_cache_key' => $culture,
                                             'default_open' => true));
    
    if ($is_moderator)
    {
        $tabs = tabs_list_tag($id, $document->getCulture(), $document->isAvailable(), 'view',
                              $is_not_archive ? NULL : $document->getVersion(),
                              get_slug($document), $nb_comments);
        echo $tabs;
    }
    
    include_partial('portals/nav', array('id'  => $id, 'document' => $document));
    
    echo '<div id="nav_share" class="nav_box">' . button_share() . '</div>';
}

echo display_content_top('home');

echo start_content_tag('portals_content', true);

if ($merged_into = $document->get('redirects_to'))
{
    include_partial('documents/merged_warning', array('merged_into' => $merged_into));
}

if (!$is_not_archive)
{
    include_partial('documents/versions_browser', array('id'      => $id,
                                                        'document' => $document,
                                                        'metadata' => $metadata,
                                                        'current_version' => $current_version));
}

if ($has_map && !$mobile_version)
{
    $map_filter = $sf_data->getRaw('map_filter');
    include_partial($mobile_version ? 'documents/mobile_map_section' : 'documents/map_section',
                     array('document' => $document,
                           'layers_list' => $map_filter['objects'],
                           'center' => $map_filter['center'],
                           'height' => $map_filter['height'],
                           'show_map' => true,
                           'has_geom' => $has_geom));
}
elseif ($has_images)
{
    echo '<div id="last_images">';
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
    echo '</div>';
}

?>
        <div id="home_background_content">
            <div id="home_left_content">
                <?php
if ($has_outings)
{
    $outing_url_params = $sf_data->getRaw('outing_url_params');
    $outing_url_params = implode('&', $outing_url_params);
    include_partial('outings/latest',
                    array('items' => $latest_outings,
                          'culture' => $culture,
                          'default_open' => true,
                          'custom_url_params' => $outing_url_params));
}

if ($is_not_archive)
{
    include_partial('portals/inside_search_form', array('document' => $document));
}

// lang-dependent content
echo '<div class="article_contenu">';
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation, 'images' => $associated_images));
echo '</div>';

                ?>
            </div>
            <div id="home_right_content">
                <?php
if ($has_videos)
{
    include_partial('portals/latest_videos', array('items' => $latest_videos, 'culture' => $culture, 'default_open' => true));
}
if ($has_news)
{
    include_partial('documents/latest_mountain_news', array('items' => $latest_mountain_news, 'culture' => $culture, 'default_open' => true));
}
if ($has_topics)
{
    include_partial('documents/latest_threads', array('items' => $latest_threads, 'culture' => $culture, 'default_open' => true));
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
                          'custom_title_link' => $custom_title_link,
                          'custom_rss_link' => $custom_rss_link));
}

if ($mobile_version) // for mobile, move prepare outing box under articles section
{
    include_partial('documents/prepare', array('sf_cache_key' => $culture,
                                             'default_open' => true));
}
                ?>
            </div>
        </div>

<?php




if (!$mobile_version) // Informations section for web version only
{
    echo start_section_tag('Information', 'data');
    if ($is_not_archive && $is_not_merged)
    {
        $document->associated_areas = $associated_areas;
    }

    if ($is_not_archive)
    {
        echo '<div class="all_associations">';
        
        include_partial('areas/association',
                        array('associated_docs' => $associated_areas,
                              'module' => 'areas',
                              'weather' => true,
                              'avalanche_bulletin' => true));
        
        if ($is_not_merged)
        {
            if ($show_link_tool)
            {
                $modules_list = array('areas');
                
                echo c2c_form_add_multi_module('portals', $id, $modules_list, 4, array('field_prefix' => 'multi_1'));
            }
        }
        
        echo '</div>';
    }

    include_partial('data', array('document' => $document));

    echo end_section_tag();

    if ($is_not_archive && $is_not_merged && $is_moderator)
    {
        include_partial('documents/images', array('images' => $associated_images,
                                                  'document_id' => $id,
                                                  'dissociation' => 'moderator',
                                                  'is_protected' => $document->get('is_protected')));
    }
}

include_partial('documents/license', array('license' => 'by-sa'));

echo end_content_tag();

include_partial('common/content_bottom');
?>
