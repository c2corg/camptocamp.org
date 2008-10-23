<?php
use_helper('Language', 'Sections', 'Viewer');

$id = $sf_params->get('id');
display_page_header('parkings', $document, $id, $metadata, $current_version);

// lang-independent content starts here

echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document));

if (!$document->isArchive())
{
    echo '<div class="all_associations">';
    include_partial('documents/association', array('associated_docs' => $associated_sites, 'module' => 'sites'));
    include_partial('documents/association', array('associated_docs' => $associated_huts, 'module' => 'huts'));
    include_partial('documents/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
    echo '</div>';
}
echo end_section_tag();

include_partial('documents/map_section', array('document' => $document,
                                               'displayed_layers'  => array('summits', 'parkings')));

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages')));
echo end_section_tag();

if (!$document->isArchive() && !$document->get('redirects_to'))
{
    echo start_section_tag('Linked routes', 'linked_routes');
    include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                  'document' => $document,
                                                  'type' => 'pr', // route-parking, reversed
                                                  'strict' => true));
    echo end_section_tag();

    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'special_rights' => 'moderator'));
}

include_partial('documents/license');

echo '</div></div>'; // end <div id="article">

include_partial('common/content_bottom');
?>
