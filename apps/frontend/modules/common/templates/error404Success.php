<?php use_helper('Viewer', 'MyForm', 'Button');

echo display_title();
echo display_content_top('no_nav');
echo start_content_tag();
?>
    <h1><?php echo __('404_error') ?></h1>
    <p><?php echo __('The page you are trying to reach is no longer available.') ?></p>

    <p><?php echo __('You can:') ?></p>

    <ul class="list404">
        <li><?php echo __('Try a site search:') ?>&nbsp;<?php include_partial('common/search_form'); ?></li>
        <li><?php echo link_to(__('Go to the homepage'), '@homepage') ?></li>
        <li><?php echo link_to(__('Contact us'), getMetaArticleRoute('contact')) ?></li>
    </ul>

<?php
echo end_content_tag();

include_partial('common/content_bottom') ?>
