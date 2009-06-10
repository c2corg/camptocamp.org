<?php 
use_helper('Pagination', 'MyImage', 'Lightbox', 'Javascript', 'Link', 'Viewer', 'General');

// add lightbox ressources
addLbMinimalRessources();

$id = $sf_params->get('id');
$lang = $sf_params->get('lang');

echo display_title(__('images list'), $sf_params->get('module'));

echo '<div id="nav_space">&nbsp;</div>';
include_partial('nav4list');
//include_partial('documents/nav_news');
?>

<div id="wrapper_context">
<div id="ombre_haut">
    <div id="ombre_haut_corner_right"></div>
    <div id="ombre_haut_corner_left"></div>
</div>

<div id="content_article">
<div id="article" class="images_content">
<?php 
echo '<p class="list_header">' . __('images presentation');

$items = $pager->getResults('array', ESC_RAW);

if (count($items) == 0):
    echo '<br /><br />' . __('there is no %1% to show', array('%1%' => __('images'))) . '</p>';
else:
    echo '</p>';
    $pager_navigation = pager_navigation($pager);
    echo $pager_navigation;
    $items = Language::parseListItems($items, 'Image');
    $static_base_url = sfConfig::get('app_static_url');
?>
<?php foreach ($items as $item): ?>
    <div class="thumb_data">
    <?php
    $i18n_item = $item['ImageI18n'][0];
    $title = $i18n_item['name'];
    $filename = $item['filename'];
    $thumb_url = image_url($filename, 'small');
    $image_route = '@document_by_id_lang_slug?module=images&id=' . $item['id'] . '&lang=' . $i18n_item['culture'] . '&slug=' . formate_slug($i18n_item['search_name']);
    echo link_to(image_tag($thumb_url, array('class' => 'img', 'alt' => $title)),
                 absolute_link(image_url($filename, 'big', true), true),
                 array('title' => $title,
                       'rel' => 'lightbox[document_images]',
                       'class' => 'view_big'));
    echo $title . '<br />';
    echo link_to(__('Details'), $image_route);
    if (!empty($item['nb_comments']))
    {
        echo ' - ' .
             picto_tag('action_comment', __('nb_comments'),
                       array('style' => 'margin-bottom:-4px')) .
             ' (' . link_to($item['nb_comments'], '@document_comment?module=images&id=' . $item['id'] . '&lang=' . $i18n_item['culture']) . ')';
    }
    ?>
    </div>
<?php endforeach ?>
<div style="clear:both"><?php echo $pager_navigation; ?></div>
<?php endif ?>
</div>
</div>

<?php include_partial('common/content_bottom') ?>
