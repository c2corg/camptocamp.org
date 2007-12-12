<span class="article_title">&nbsp;</span>
<div id="nav_space">&nbsp;</div>

<?php
$culture = $sf_user->getCulture();
$connected = $sf_user->isConnected();
include_partial('documents/welcome');

if ($connected)
{
    include_partial('documents/wizard_button', array('sf_cache_key' => $culture));
}
include_partial('documents/nav4home', array('sf_cache_key' => $culture . '-' . (int)$connected));
?>

<div id="wrapper_context">
<div id="ombre_haut">
    <div id="ombre_haut_corner_right"></div>
    <div id="ombre_haut_corner_left"></div>
</div>

<div id="content_article">
    <div id="article">
       
        <div id="home_left_content">
            <?php
            include_partial('common/edit_in_place', array('message' => $sf_data->getRaw('message')));
            include_partial('outings/latest', array('items' => $latest_outings, 'culture' => $culture));
            include_partial('documents/latest_meta', array('items' => $meta_items, 'culture' => $culture));
            ?>
        </div>
        <div id="home_right_content">
            <div id="images" class="front_images">
                <?php
                include_partial('images/latest', array('items' => $latest_images, 'culture' => $culture));
                ?>
            </div>
            <?php
            include_partial('articles/latest', array('items' => $latest_articles, 'culture' => $culture)); 
            include_partial('documents/latest_threads', array('items' => $latest_threads, 'culture' => $culture));
            ?>
        </div>
        <div id="fake_clear"> &nbsp;</div>
    </div>
</div>

<?php include_partial('common/content_bottom') ?>
