<?php 
use_helper('Viewer');

if ($concurrent_edition)
{
    $title = 'Concurrent edition';
    $warning = 'Concurrent edition warning';
}
else
{
    $title = 'Editing preview';
    $warning = 'Preview warning';
}
?>

<a href="javascript:void(0)" class="close_btn" onclick="$('preview').hide(); $('form_buttons_up').hide();"><?php echo __('close') ?></a>

<h2><?php echo __($title) ?></h2>
<p class="preview_warning"><?php echo __($warning) ?></p>

<?php echo display_title(isset($title_prefix) ? $title_prefix.__('&nbsp;:').' '.$document->get('name') : $document->get('name')) ?>


<div class="title" id="preview_info">
<?php echo '<a href="#form_info">' . __('Information') . '<span class="tips">[' . __('Go back to form') . ']</span></a>'?>
</div><hr />
<div id="data_section_container">
<?php include_partial('data', array('document' => $document)); ?>
</div>


<div class="title" id="preview_desc">
<?php echo '<a href="#form_desc">' . __('Description') . '<span class="tips">[' . __('Go back to form') . ']</span></a>'?>
</div><hr />
<?php include_partial('i18n', array('document' => $document, 'needs_translation' => false,
                                    'images' => $associated_images, 'filter_image_type' => $filter_image_type)); ?>

<div class="clear"></div>
<br /><hr />
<div class="title">
<?php echo '<a href="#form_desc"><span class="tips">[' . __('Go back to form') . ']</span></a>' ?>
</div>
<br/><br/>
<a href="javascript:void(0)" class="close_btn" onclick="$('preview').hide(); $('form_buttons_up').hide();"><?php echo __('close') ?></a>
