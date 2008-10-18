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

<a href="javascript:void(0)" class="close_btn" onclick="$('preview').hide()"><?php echo __('close') ?></a>

<h2><?php echo __($title) ?></h2>
<p class="preview_warning"><?php echo __($warning) ?></p>

<?php echo display_title($document->get('name')) ?>


<div id="title"><?php echo __('Information') ?></div><hr />
<div id="data_section_container">
<?php include_partial('data', array('document' => $document)); ?>
</div>


<div id="title"><?php echo __('Description') ?></div><hr />
<?php include_partial('i18n', array('document' => $document)); ?>

<div class="clear"></div><br />

<a href="javascript:void(0)" class="close_btn" onclick="$('preview').hide()"><?php echo __('close') ?></a>
