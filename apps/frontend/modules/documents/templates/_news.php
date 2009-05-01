<?php use_helper('Forum');

if (count($items) != 0):

if (!isset($default_open))
{
    $default_open = true;
}
?>
<div id="nav_news">
    <div class="nav_box_top"></div>
    <div class="nav_box_content">
        <?php echo nav_title('news', __('c2corg news'), 'list', $open); ?>
        <div class="nav_box_text" id="nav_news_section_container">
            <ul>
            <?php foreach ($items as $item): ?>
                <li><?php echo f_link_to($item['subject'], 'viewtopic.php?id=' . $item['id'] . '&action=new') ?></li>
            <?php endforeach ?>
        </div>
        <?php
        echo javascript_tag("setHomeFolderStatus('nav_news', ".((!$default_open) ? 'false' : 'true').", '".__('section open')."');");
        ?>
    </div>
    <div class="nav_box_down"></div>
</div>

<?php endif;
