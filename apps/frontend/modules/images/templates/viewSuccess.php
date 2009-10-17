<?php
use_helper('Language', 'Sections', 'Viewer', 'MyImage', 'Link', 'AutoComplete', 'General');
$ajax_failure_feedback = sfConfig::get('app_ajax_feedback_div_name_failure');

$static_base_url = sfConfig::get('app_static_url');

$id = $sf_params->get('id');
display_page_header('images', $document, $id, $metadata, $current_version);

echo start_section_tag('Image', 'view');
$lang = $sf_user->getCulture();
$module = $sf_context->getModuleName();
echo display_picture($document->get('filename'));
?>
<p class="tips"><?php echo __('Click to display original image') ?></p>
<?php
echo end_section_tag();

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation,
                                                'images' => $associated_images,
                                                'filter_image_type' => ($document['image_type'] == 1)));
echo end_section_tag();

// lang-independent content starts here
echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document, 'user' => $creator));
if (!$document->isArchive())
{
    echo '<div class="all_associations">';
    include_partial('areas/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
    echo '</div>';
}
echo end_section_tag();

include_partial('documents/map_section', array('document' => $document,
                                               'displayed_layers'  => array('images')));

if (!$document->isArchive() && !$document->get('redirects_to')):
    echo start_section_tag('Linked documents', 'associated_docs');
    if (!count($associated_documents))
    {
        echo '<p class="default_text">' . __("No document uses this picture.") . '</p>';
    }
?>
    <ul id="list_associated_docs">
<?php
    if (count($associated_documents)>0)
    {
        foreach ($associated_documents as $doc)
        {
?>
        <li>
<?php
            $module = $doc['module'];
            echo picto_tag('picto_' . $module, __($module));
            echo ' ' . link_to($doc['name'], "@document_by_id_lang_slug?module=$module&id=" . $doc['id'] . 
                                             '&lang=' . $doc['culture'] . '&slug=' . formate_slug($doc['search_name']));
?>
        </li>
<?php
        }
    }
?>
    </ul>
<?php

    if ($sf_user->isConnected() && !$document->get('is_protected'))
    {
?>
        <div id="plus">
        <p><?php echo __('You can associate this picture with any existing document using the following tool:'); ?></p>
<?php
        $linkable_modules = sfConfig::get('app_modules_list');
        unset($linkable_modules[1]); // documents

        echo c2c_form_add_multi_module('images', $id, $linkable_modules, 3, 'list_associated_docs', false);
?>
        </div>
<?php
    }
    
    echo end_section_tag();

    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'dissociation' => 'moderator'));

endif;

$licenses_array = sfConfig::get('app_licenses_list');
include_partial('documents/license', array('license' => $licenses_array[$document['image_type']]));

echo end_content_tag();

include_partial('common/content_bottom');
