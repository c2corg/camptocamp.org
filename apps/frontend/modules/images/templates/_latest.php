<?php
use_helper('Link', 'MyImage', 'Javascript', 'Home');

if (!isset($default_open))
{
    $default_open = true;
}

if (isset($custom_title_text))
{
    $custom_title_text = $sf_data->getRaw('custom_title_text');
}
else
{
    $custom_title_text = '';
}

if (isset($custom_title_link))
{
    $custom_title_link = $sf_data->getRaw('custom_title_link');
}
else
{
    $custom_title_link = '';
}

if (isset($custom_rss_link))
{
    $custom_rss_link = $sf_data->getRaw('custom_rss_link');
}
else
{
    $custom_rss_link = '';
}
if (!isset($home_section))
{
    $home_section = true;
}

if ($home_section)
{
    include_partial('documents/home_section_title',
                    array('module' => 'images',
                          'custom_title_text' => $custom_title_text,
                          'custom_title_link' => $custom_title_link,
                          'custom_rss_link' => $custom_rss_link));
?>
    <div class="home_container_text" id="last_images_section_container"><?php
}
else
{
?>
<div id="nav_images" class="nav_box">
    <div class="nav_box_top"></div>
    <div class="nav_box_content">
        <?php
    echo nav_title('images', __('Latest images'), 'images', 'last', $custom_title_link, $custom_rss_link, __("Subscribe to latest images creations"));
        ?>
        <div class="nav_box_text" id="nav_images_section_container"><?php
}

if (count($items) == 0)
{
    ?>
    <p><?php echo __('No recent images available') ?></p><?php
}
else
{
    ?>
    <div id="last_image_list"><?php
    foreach ($items as $item)
    {
        ?>
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
        </div><?php
    }
    ?>
    </div><?php
}

if ($home_section)
{
?>
</div>
<?php
$cookie_position = array_search('last_images', sfConfig::get('app_personalization_cookie_fold_positions'));
echo javascript_tag('setHomeFolderStatus(\'last_images\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').");");
}
else
{
?>
        </div>
    </div>
    <div class="nav_box_down"></div>
</div>
<?php
}
