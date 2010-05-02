<?php
use_helper('Language', 'Sections', 'Viewer', 'General', 'Field', 'MyForm', 'AutoComplete'); 

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_moderator);
$has_map = $document->getRaw('has_map');
$has_map = !empty($has_map);
$topo_filter = $document->getRaw('topo_filter');

display_page_header('portals', $document, $id, $metadata, $current_version);

// lang-independent content starts here

echo start_section_tag('Portal', 'intro');
echo field_text_data_if_set($document, 'abstract', null, array('needs_translation' => $needs_translation, 'show_images' => false));

if ($is_not_archive)
{
    echo '</div>';
    
    echo form_tag('documents/portalredirect', array('method' => 'get', 'class' => 'search'));
    echo '<div class="sbox">';
    echo portal_search_box_tag($topo_filter);
    echo '</div></form>';
}
echo end_section_tag();

if ($has_map)
{
    include_partial('documents/map_section', array('document' => $document));
}

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation, 'images' => $associated_images));
echo end_section_tag();

echo start_section_tag('Information', 'data');
if ($is_not_archive && $is_not_merged)
{
    $document->associated_areas = $associated_areas;
}
include_partial('data', array('document' => $document));

if ($is_not_archive)
{
    echo '<div class="all_associations">';
    
    include_partial('areas/association',
                    array('associated_docs' => $associated_areas,
                          'module' => 'areas',
                          'weather' => true,
                          'avalanche_bulletin' => true));
    
    if ($is_not_merged)
    {
        if ($show_link_tool)
        {
            $modules_list = array('areas');
            
            echo c2c_form_add_multi_module('portals', $id, $modules_list, 4, 'multi_1', true);
        }
    }
    
    echo '</div>';
}
echo end_section_tag();

if ($is_not_archive && $is_not_merged)
{
    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'dissociation' => 'moderator',
                                              'is_protected' => $document->get('is_protected')));
}

include_partial('documents/license', array('license' => 'by-sa'));

echo end_content_tag();

include_partial('common/content_bottom');
?>
