<?php
if (!isset($open))
{
    $open = true;
}
$html_content = __('prepare_outing_box');
if (!empty($html_content)):
?>
<div id="nav_prepare">
    <div class="nav_box_top"></div>
    <div class="nav_box_content">
        <?php echo nav_title('prepare', __('Prepare outing'), 'outings', $open); ?>
        <div class="nav_box_text" id="nav_prepare_section_container" <?php if (!$open) echo 'style="display: none;"'; ?>>
            <?php echo $html_content ?>
            <p class="nav_box_bottom_link"><?php echo link_to(__('More links'), getMetaArticleRoute('prepare_outings')) ?></p>
        </div>
    </div>
    <div class="nav_box_down"></div>
</div>
<?php
endif;
