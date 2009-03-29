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
<?php echo __('Information') . link_to('[' . __('Go back to form') . ']', '#form_info') ?>
</div><hr />
<div id="data_section_container">
<?php include_partial('data', array('document' => $document)); ?>
</div>


<div class="title" id="preview_desc">
<?php echo __('Description') . link_to('[' . __('Go back to form') . ']', '#form_desc') ?>
</div><hr />
<?php include_partial('i18n', array('document' => $document)); ?>

<div class="clear"></div><br />

<a href="javascript:void(0)" class="close_btn" onclick="$('preview').hide(); $('form_buttons_up').hide();"><?php echo __('close') ?></a>
