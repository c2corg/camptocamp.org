<?php
use_helper('Language', 'Sections', 'Viewer', 'MyImage', 'Link', 'AutoComplete', 'General');

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$mobile_version = c2cTools::mobileVersion();
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator && !$mobile_version);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected && !$mobile_version);
$section_list = array('map' => (boolean)($document->get('geom_wkt')),
                      'images' => (boolean)count($associated_images));
$lang = $document->getCulture();
$module = $sf_context->getModuleName();

display_page_header('images', $document, $id, $metadata, $current_version,
                    array('nav_options' => $section_list, 'item_type' => 'http://schema.org/ImageObject',
                          'nb_comments' => $nb_comments, 'creator_id' => $creator['id']));

echo start_section_tag('Image', 'view');
echo display_picture($document->get('filename'), 'big', null, $document->get('name'));
if (!$mobile_version): ?>
<p class="tips"><?php echo __('Click to display original image') ?></p>
<?php
endif;
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
if ($is_not_archive && $is_not_merged)
{
    $document->associated_areas = $associated_areas;
}
include_partial('data', array('document' => $document, 'user' => $creator, 'nb_comments' => $nb_comments));
if ($is_not_archive)
{
    echo '<div class="all_associations col_right col_33">';
    include_partial('areas/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
    echo '</div>';
}
echo end_section_tag();

include_partial($mobile_version ? 'documents/mobile_map_section' : 'documents/map_section', array('document' => $document));

if ($is_not_archive && $is_not_merged):
    echo start_section_tag('Linked documents', 'associated_docs');
    
    $id_no_associated_docs = "no_associated_docs";
    $id_list_associated_docs = "list_associated_docs";
    if (!count($associated_documents))
    {
        echo '<p class="default_text" id="' . $id_no_associated_docs . '">' . __("No document uses this picture.") . '</p>';
    }

    if (count($associated_documents) > 0)
    {
        echo '<ul id="' . $id_list_associated_docs . '">';
        foreach ($associated_documents as $doc)
        {
            $doc_id = $doc['id'];
            $module = $doc['module'];
            $type = c2cTools::Module2Letter($module) . 'i';
            $idstring = $type . '_' . $doc_id;

            echo '<li id="', $idstring , '">';

            echo picto_tag('picto_' . $module, __($module));
            echo ' ' . link_to($doc['name'], "@document_by_id_lang_slug?module=$module&id=" . $doc_id . 
                                             '&lang=' . $doc['culture'] . '&slug=' . make_slug($doc['name']));
            
            if ($show_link_to_delete)
            {
                echo c2c_link_to_delete_element($type, $doc_id, $id, false, 1);
            }

            echo '</li>';
        }
        echo '</ul>';
    }
    elseif ($show_link_tool)
    {
        echo  '<ul id="' . $id_list_associated_docs . '"><li style="display:none"></li></ul>';
    }

    if ($show_link_tool)
    {
?>
        <div id="association_tool" class="plus">
        <p><?php echo __('You can associate this picture with any existing document using the following tool:'); ?></p>
<?php
        $linkable_modules = sfConfig::get('app_modules_list');
        unset($linkable_modules[1]); // documents

        echo c2c_form_add_multi_module('images', $id, $linkable_modules, 3, array(
            'field_prefix' => $id_list_associated_docs,
            'hide' => false,
            'removed_id' =>  $id_no_associated_docs));
?>
        </div>
<?php
    }
    
    echo end_section_tag();

    // only display images section if they are some images
    // (since we don't propose the link to add images to an image anyway)
    if (count($associated_images))
    {
        include_partial('documents/images', array('images' => $associated_images,
                                                  'document_id' => $id,
                                                  'dissociation' => 'moderator',
                                                  'is_protected' => $document->get('is_protected')));
    }

    if ($mobile_version) include_partial('documents/mobile_comments', array('id' => $id, 'lang' => $lang, 'nb_comments' => $nb_comments));

    include_partial('documents/annex_docs', array('related_portals' => $related_portals));

endif;

$licenses_array = sfConfig::get('app_licenses_list');
include_partial('documents/license', array('license' => $licenses_array[$document['image_type']],
                                           'large' => $show_link_tool, 'version' => $current_version,
                                           'created_at' => (isset($created_at) ? $created_at :  null),
                                           'timer' => $timer));

echo end_content_tag();

include_partial('common/content_bottom');
