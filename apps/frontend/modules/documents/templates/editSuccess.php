<?php
use_helper('Form', 'Viewer', 'WikiTabs', 'MyForm', 'Javascript', 'Ajax', 'SmartFormat');
$mobile_version = c2cTools::mobileVersion();
$id = $sf_params->get('id');
$lang = $sf_params->get('lang');
$version = $sf_params->get('version');
$module = $sf_context->getModuleName();
$linked_doc = isset($linked_doc) ? $linked_doc : null;

if (!$mobile_version)
{
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript('/static/js/tooltips.js', 'last');
}

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
    echo display_title(isset($title_prefix) ? $title_prefix.__('&nbsp;:').' '.$document_name : $document_name, $module);
    if (!$mobile_version)
    {
        echo '<div id="nav_space">&nbsp;</div>';
        echo tabs_list_tag($id, $lang, $document->isAvailable(), 'edit', $version, get_slug($document));
    }
}
else
{
    echo display_title(__("Creating new $module"), $module);
    if (!$mobile_version)
    {
        echo '<div id="nav_space">&nbsp;</div>';
        echo tabs_list_tag($id, $document->getCulture(), $document->isAvailable(), '', NULL, get_slug($document));
    }
}

echo display_content_top('doc_content');
echo start_content_tag($module . '_content');

// display warning if editing from an archive version
if (!empty($editing_archive))
{
    echo '<p class="warning_message">', __('Warning: you are editing an archive version!'), '</p>';
}

if ($new_document && $linked_with): ?>
    <p class="warning_message">
    <?php echo smart_format(__("This new $module will be linked with $linked_module '[[$linked_module/%2%|%1%]]' (document %2%)",
                               array('%1%' => $linked_name,
                                     '%2%' => $linked_with))); ?>
    </p>
<?php endif;

echo global_form_errors_tag();
echo form_tag("@document_edit?module=$module&id=&lang=", 
              array('multipart' => true, // needed for gpx upload to work
                    'onsubmit' => 'C2C.submitonce(this)',
                    'id' => 'editform'));

if ($new_document && $linked_with)
{
    $pseudo_id = $module . '_' . mt_rand();
    echo input_hidden_tag('pseudo_id', $pseudo_id);
}

include_partial("$module/form_fields", array('document' => $document,
                                             'new_document' => $new_document,
                                             'linked_doc' => $linked_doc,
                                             'associated_books' => (isset($associated_books) ? $associated_books : null),
                                             'associated_articles' => (isset($associated_articles) ? $associated_articles : null)));

$editing_archive = isset($editing_archive) ? $editing_archive : false;
?>
<div id="form_buttons_up" style="display:none">
<?php
if (!empty($editing_archive))
{
    echo $warning_archive;
}
echo input_hidden_tag('editing_archive', $editing_archive);
include_partial('documents/form_buttons', array('document'     => $document,
                                                'new_document' => $new_document));
?>
</div>

<?php
$concurrent_edition = isset($concurrent_edition) ? $concurrent_edition : false;
include_partial('documents/preview', array('concurrent_edition' => $concurrent_edition,
                                           'id'   => $id,
                                           'lang' => $lang));

                                           // display warning if editing from an archive version
?>
<div id="form_buttons_down">
<?php
if (!empty($editing_archive))
{
    echo $warning_archive;
}
include_partial('documents/form_buttons', array('document'     => $document,
                                                'new_document' => $new_document));
?>
</div>

<?php
switch ($module)
{
    case 'outings':
    case 'users':
        $license = 'by-nc-nd';
        $template_root = 'documents';
        break;

    case 'articles':
       $license = ($document->get('article_type') == 2) ? 'by-nc-nd' : 'by-sa';
       $template_root = 'articles';
       break;

    case 'images':
        switch ($document->get('image_type'))
        {
            case 1:
                $license = 'by-sa';
                break;
            case 2:
                $license = 'by-nc-nd';
                break;
            case 3:
                $license = 'copyright';
                break;
            default:
                $license = 'by-sa';
        }

        $template_root = 'images';
        break;

    default:
        $license = 'by-sa';
        $template_root = 'documents';
        break;
}
include_partial($template_root.'/license', array('license' => $license));
?>
</form>

<div class="clear"></div>

<?php
echo end_content_tag();

include_partial('common/content_bottom') ?>
