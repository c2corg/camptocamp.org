<?php
use_helper('Language', 'Sections', 'Viewer'); 

$id = $sf_params->get('id');
display_page_header('maps', $document, $id, $metadata, $current_version);

// lang-independent content starts here
echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document));
if (!$document->isArchive())
{
    echo '<div class="all_associations">';
    include_partial('areas/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    echo '</div>';
}
echo end_section_tag();

include_partial('documents/map_section', array('document' => $document,
                                               'displayed_layers'  => array('maps')));

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation, 'images' => $associated_images));
echo end_section_tag();

if (!$document->isArchive() && !$document->get('redirects_to'))
{
    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'dissociation' => 'moderator'));
}

include_partial('documents/license', array('license' => 'by-sa'));

echo '</div></div>'; // end <div id="article">

include_partial('common/content_bottom');
?>
