<?php use_helper('MyForm', 'Button'); ?>

<span class="article_title">&nbsp;</span>

<div id="wrapper_context">
<div id="ombre_haut">
    <div id="ombre_haut_corner_right"></div>
    <div id="ombre_haut_corner_left"></div>
</div>

<div id="content_article">
    <div id="article">

    <h1><?php echo __('404_error') ?></h1>
    <p><?php echo __('The page you are trying to reach is no longer available.') ?></p>

    <p><?php echo __('You can:') ?></p>

    <ul class="list404">
        <li><?php echo __('Try a site search:') ?>&nbsp;<?php include_partial('common/search_form'); ?></li>
        <li><?php echo link_to(__('Go to the homepage'), '@homepage') ?></li>
        <li><?php echo link_to(__('Contact us'), getMetaArticleRoute('contact')) ?></li>
    </ul>
    </div>
</div>

<?php include_partial('common/content_bottom') ?>
