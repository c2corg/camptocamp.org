<?php
use_helper('Link', 'Javascript');

if (!isset($default_open))
{
    $default_open = true;
}
if (!isset($custom_title_text))
{
    $custom_title_text = __('Latest videos');
}
if (!isset($custom_title_link))
{
    $custom_title_link = '';
}
if (!isset($custom_rss_link))
{
    $custom_rss_link = '';
}
?>
<div id="last_videos" class="latest">
<?php
include_partial('documents/home_section_title',
                array('module' => 'images',
                      'has_title_link' => false,
                      'custom_title_text' => $custom_title_text,
                      'custom_section_id' => 'last_videos',
                      'home_section' => false)); ?>
<div class="home_container_text" id="last_videos_section_container">
<?php if (count($items) == 0): ?>
    <p><?php echo __('No recent videos available') ?></p>
<?php else: ?>
    <div id="video_list">
    <?php foreach ($items as $item): ?>
        <div class="video">
        <?php 
            $url = htmlspecialchars_decode($item['url']);
            $thumbnail = htmlspecialchars_decode($item['thumbnail']);
            $title = htmlspecialchars_decode($item['title']);
            
            $image_tag = image_tag($thumbnail,
                                   array('title' => $title, 'alt' => $title));
            echo link_to($image_tag, $url);
        ?>    
        </div>
    <?php endforeach ?>
    </div>
<?php endif;?>
</div>
</div>
