<?php
use_helper('Form', 'Viewer', 'WikiTabs', 'MyForm', 'Javascript', 'Ajax', 'SmartFormat');
$id = $sf_params->get('id');
$lang = $sf_params->get('lang');
$version = $sf_params->get('version');
$module = $sf_context->getModuleName();
$linked_doc = isset($linked_doc) ? $linked_doc : null;

if ($linked_doc)
{
    $linked_with = $linked_doc->get('id');
    $linked_name = $linked_doc->get('name');
    $linked_module = $linked_doc->get('module');
}
else
{
    $linked_with = 0;
}

if (!$new_document)
{
    echo display_title($document_name, $module);
    echo '<div id="nav_space">&nbsp;</div>';
    echo tabs_list_tag($id, $lang, $document->isAvailable(), 'edit', $version);
}
else
{
    echo display_title(__("Creating new $module"), $module);
    echo '<div id="nav_space">&nbsp;</div>';
    echo tabs_list_tag($id, $document->getCulture(), $document->isAvailable(), '', NULL);
}
?>

<div id="wrapper_context">
<div id="ombre_haut">
    <div id="ombre_haut_corner_right"></div>
    <div id="ombre_haut_corner_left"></div>
</div>

<div id="content_article">
<div id="article">

<?php
// display warning if editing from an archive version
if (!empty($editing_archive)): ?>
    <p class="warning_message"><?php echo __('Warning: you are editing an archive version!') ?></p>
<?php endif;

if ($linked_with): ?>
    <p class="warning_message">
    <?php echo smart_format(__("This new $module will be linked with $linked_module '[[$linked_module/%2%|%1%]]' (document %2%)",
                               array('%1%' => $linked_name,
                                     '%2%' => $linked_with))); ?>
    </p>
<?php endif;

echo global_form_errors_tag();
echo form_tag("@document_edit?module=$module&id=&lang=", 
              array('multipart' => true, // needed for gpx upload to work
                    'onsubmit' => 'submitonce(this)',
                    'id' => 'editform'));

if ($new_document)
{
    $pseudo_id = $module . '_' . mt_rand();
    echo input_hidden_tag('pseudo_id', $pseudo_id);
}

include_partial("$module/form_fields", array('document'     => $document,
                                             'new_document' => $new_document,
                                             'linked_doc' => $linked_doc));

$editing_archive = isset($editing_archive) ? $editing_archive : false;
echo input_hidden_tag('editing_archive', $editing_archive);

$concurrent_edition = isset($concurrent_edition) ? $concurrent_edition : false;
include_partial('documents/preview', array('concurrent_edition' => $concurrent_edition,
                                           'id'   => $id,
                                           'lang' => $lang));

include_partial('documents/form_buttons', array('document'     => $document,
                                                'new_document' => $new_document));
if ($module == 'articles')
{
    include_partial('articles/license');
}
else
{
    include_partial('documents/license');
}
?>
</form>

<div class="clear"></div>
</div></div>

<?php include_partial('common/content_bottom') ?>
