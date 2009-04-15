<?php
if (!isset($default_open))
{
    $default_open = true;
}
$html_content = __('prepare_outing_box');
if (!empty($html_content)):
?>
<div id="nav_prepare">
    <div class="nav_box_top"></div>
    <div class="nav_box_content">
        <?php echo nav_title('prepare', __('Prepare outing'), 'outings'); ?>
        <div class="nav_box_text" id="nav_prepare_section_container">
            <?php echo $html_content ?>
            <p class="nav_box_bottom_link"><?php echo link_to(__('More links'), getMetaArticleRoute('prepare_outings')) ?></p>
        </div>
        <?php
        echo javascript_tag("setHomeFolderStatus('nav_prepare', ".((!$default_open) ? 'false' : 'true').", '".__('section open')."');");
        ?>
    </div>
    <div class="nav_box_down"></div>
</div>
<?php
endif;
