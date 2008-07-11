<?php 
use_helper('Pagination', 'MyImage', 'Lightbox', 'Javascript', 'Link', 'Viewer');
// add lightbox ressources
_addLbRessources(false);

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
<div id="article">
<?php 
$items = $pager->getResults('array', ESC_RAW);

if (count($items) == 0):
    echo __('there is no %1% to show', array('%1%' => __('images')));
else:
    $pager_navigation = pager_navigation($pager);
    echo $pager_navigation;
    $items = Language::parseListItems($items, 'Image');
?>
<?php foreach ($items as $item): ?>
    <div class="thumb_data">
    <?php
    $title = $item['ImageI18n'][0]['name'];
    $filename = $item['filename'];
    $thumb_url = image_url($filename, 'small');
    $image_route = '@document_by_id?module=images&id=' . $item['ImageI18n'][0]['id'];
    echo link_to(image_tag($thumb_url, array('class' => 'img', 'alt' => $title)),
                 absolute_link(image_url($filename, 'big')), array('title' => $title,
                                                                   'rel' => 'lightbox[document_images]',
                                                                   'class' => 'view_big'));
    echo $title . '<br />';
    echo link_to(__('Information'), $image_route);
    if (!empty($item['nb_comments']))
    {
        echo '<br />' . 
             image_tag('/static/images/picto/comment.png',
                       array('title' => __('nb_comments'), 'style' => 'margin-bottom:-4px')) .
             ' (' . $item['nb_comments'] . ')';
    }
    ?>
    </div>
<?php endforeach ?>
<div style="clear:both"><?php echo $pager_navigation; ?></div>
<?php endif ?>
</div>
</div>

<?php include_partial('common/content_bottom') ?>
