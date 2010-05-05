<?php
use_helper('Home', 'Language', 'Sections', 'Viewer', 'General', 'Field', 'AutoComplete'); 

$culture = $sf_user->getCulture();
$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_moderator);
$has_map = $document->getRaw('has_map');
$has_map = !empty($has_map);


echo init_js_var(true, 'home_nav', $connected);

echo '<div id="wrapper_context" class="home">';

// lang-independent content starts here

echo start_section_tag('portal', 'intro');
echo field_text_data_if_set($document, 'abstract', null, array('needs_translation' => $needs_translation, 'show_images' => false));

if ($is_not_archive)
{
    include_partial('portals/inside_search_form', array('document' => $document));
}
echo end_section_tag();

if ($has_map)
{
    include_partial('documents/map_section', array('document' => $document));
}

if ($connected)
{
    include_partial('documents/wizard_button', array('sf_cache_key' => $culture));
}

include_partial('documents/welcome', array('default_open' => true));

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation, 'images' => $associated_images));
echo end_section_tag();



if ($has_images):
?>
        <div id="last_images">
            <?php
    include_partial('images/latest', array('items' => $latest_images, 'culture' => $culture, 'default_open' => true));
            ?>
        </div>
<?php
endif;

?>
        <div id="home_background_content">
            <div id="home_left_content">
                <?php
if ($has_outings)
{
    include_partial('outings/latest', array('items' => $latest_outings, 'culture' => $culture, 'default_open' => true));
}
if ($has_articles)
{
    include_partial('articles/latest', array('items' => $latest_articles, 'culture' => $culture, 'default_open' => true));
}
                ?>
            </div>
            <div id="home_right_content">
                <?php
if ($has_news)
{
    include_partial('documents/latest_mountain_news', array('items' => $latest_mountain_news, 'culture' => $culture, 'default_open' => true));
}
if ($has_topics)
{
    include_partial('documents/latest_threads', array('items' => $latest_threads, 'culture' => $culture, 'default_open' => true));
}
                ?>
            </div>
        </div>
        <div id="fake_clear"> &nbsp;</div>

<?php


include_partial('documents/license', array('license' => 'by-sa'));

echo end_content_tag();

echo '</div>';
?>
