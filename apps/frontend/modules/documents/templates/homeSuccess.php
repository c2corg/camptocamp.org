<?php
$static_base_url = sfConfig::get('app_static_url');
$response = sfContext::getInstance()->getResponse();
$response->addJavascript(sfConfig::get('app_static_url') . '/static/js/fold_home.js?' . sfSVN::getHeadRevision('fold_home.js'), 'head_last');

$culture = $sf_user->getCulture();
$connected = $sf_user->isConnected();
include_partial('documents/welcome', array('open' => $nav_about_open));

if ($connected)
{
    include_partial('documents/wizard_button', array('sf_cache_key' => $culture));
}
include_partial('documents/prepare', array('sf_cache_key' => $culture, 'default_open' => $nav_prepare_open));
include_partial('documents/toolbox', array('sf_cache_key' => $culture, 'default_open' => $nav_toolbox_open));
// TODO: removed until we have decided how the content is generated
// include_partial('documents/news', array('sf_cache_key' => $culture, 'open' => true));
include_partial('documents/figures', array('sf_cache_key' => $culture, 'figures' => $figures, 'default_open' => $nav_figures_open));
include_partial('documents/buttons', array('sf_cache_key' => $culture));
?>

<div id="wrapper_context">
<div id="ombre_haut">
    <div id="ombre_haut_corner_right"></div>
    <div id="ombre_haut_corner_left"></div>
</div>

<div id="content_article">
    <div id="article" class="home_article">
        <div id="last_images">
            <?php
            include_partial('images/latest', array('items' => $latest_images, 'culture' => $culture, 'default_open' => $last_images_open));
            ?>
        </div>
        <div id="home_left_content">
            <?php
            include_partial('common/edit_in_place', array('message' => $sf_data->getRaw('message')));
            include_partial('outings/latest', array('items' => $latest_outings, 'culture' => $culture, 'default_open' => $last_outings_open));
            include_partial('documents/latest_meta', array('items' => $meta_items, 'culture' => $culture, 'default_open' => $on_the_web_open));
            include_partial('articles/latest', array('items' => $latest_articles, 'culture' => $culture, 'default_open' => $last_articles_open));
            ?>
        </div>
        <div id="home_right_content">
            <?php
            include_partial('documents/latest_mountain_news', array('items' => $latest_mountain_news, 'culture' => $culture, 'default_open' => $mountain_news_open));
            include_partial('documents/latest_threads', array('items' => $latest_threads, 'culture' => $culture, 'default_open' => $last_msgs_open));
            include_partial('documents/latest_docs', array('culture' => $culture, 'default_open' => $last_docs_open));
            ?>
        </div>
        <div id="fake_clear"> &nbsp;</div>
    </div>
</div>

<?php include_partial('common/content_bottom') ?>
