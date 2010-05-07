<?php
use_helper('Link', 'MyImage', 'Javascript');

if (!isset($default_open))
{
    $default_open = true;
}
if (!isset($custom_title_text))
{
    $custom_title_text = '';
}
if (!isset($custom_title_link))
{
    $custom_title_link = '';
}
if (!isset($custom_rss_link))
{
    $custom_rss_link = '';
}
include_partial('documents/home_section_title',
                array('module' => 'images',
                      'custom_title_text' => $custom_title_text,
                      'custom_title_link' => $custom_title_link,
                      'custom_rss_link' => $custom_rss_link)); ?>
<div class="home_container_text" id="last_images_section_container">
<?php if (count($items) == 0): ?>
    <p><?php echo __('No recent images available') ?></p>
<?php else: ?>
    <div id="image_list">
    <?php foreach ($items as $item): ?>
        <div class="image">
        <?php 
            $id = $item['id'];
            $filename = $item['filename'];
            
            // FIXME: use prefered language
            $i18n = $item['ImageI18n'][0];
            $lang = $i18n['culture'];
            $title = $i18n['name'];
            
            $image_tag = image_tag(image_url($filename, 'small'),
                                   array('title' => $title, 'alt' => $title));
            echo link_to($image_tag, "@document_by_id_lang_slug?module=images&id=$id&lang=$lang&slug=" . make_slug($i18n['name']));
        ?>    
        </div>
    <?php endforeach ?>
    </div>
<?php endif;?>
</div>
<?php
$cookie_position = array_search('last_images', sfConfig::get('app_personalization_cookie_fold_positions'));
echo javascript_tag('setHomeFolderStatus(\'last_images\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').", '".__('section open')."');");
?>
