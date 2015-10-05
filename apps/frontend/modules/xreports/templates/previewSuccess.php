<?php 
use_helper('Viewer', 'Sections');

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

<a href="javascript:void(0)" class="close_btn" onclick="$('#preview, #form_buttons_up').hide();"><?php echo __('close') ?></a>

<h2><?php echo __($title) ?></h2>
<p class="preview_warning"><?php echo __($warning) ?></p>

<?php
echo display_title(isset($title_prefix) ? $title_prefix.__('&nbsp;:').' '.$document->get('name') : $document->get('name'));

echo start_preview_section_tag('Accident infos', 'data', 'info');
include_partial('data', array('document' => $document, 'preview' => true));
echo end_preview_section_tag();

echo start_preview_section_tag('Accident description', 'description', 'desc');
?><div class="article_contenu"><?php 
include_partial('i18n', array('document' => $document, 'needs_translation' => false,
                              'associated_books' => isset($associated_books) ? $associated_books : null,
                              'images' => $associated_images, 'filter_image_type' => $filter_image_type, 'preview' => true));
?></div><?php 
echo end_preview_section_tag();

echo start_preview_section_tag('Accident profil', 'profil', 'profil');
include_partial('profil', array('document' => $document, 'preview' => true));
echo end_preview_section_tag();

?><div class="clearer"></div>
<br /><hr />
<div class="title">
<?php echo '<a href="#form_desc"><span class="tips">[' . __('Go back to form') . ']</span></a>' ?>
</div>
<br/><br/>
<a href="javascript:void(0)" class="close_btn" onclick="$('#preview, #form_buttons_up').hide();"><?php echo __('close') ?></a>
