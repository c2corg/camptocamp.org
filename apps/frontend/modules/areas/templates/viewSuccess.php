<?php
use_helper('Language', 'Sections', 'Viewer');

$id = $sf_params->get('id');
display_page_header('areas', $document, $id, $metadata, $current_version);

// lang-independent content starts here
echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document));
echo end_section_tag();

include_partial('documents/map_section', array('document' => $document,
                                               'displayed_layers'  => array()));
                                               
// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages')));
echo end_section_tag();

if (!$document->isArchive() && !$document->get('redirects_to'))
{
    echo start_section_tag('Linked documents', 'associated_docs');
    ?>
    <ul id="list_associated_docs">
        <?php foreach (array('summits', 'routes', 'outings', 'huts', 'parkings', 'sites', 'images') as $module): ?>
            <li><?php echo link_to(ucfirst(__($module)), "/$module/list?areas=$id"); ?></li>
        <?php endforeach; ?>
    </ul>
    <?php
    echo end_section_tag();
    
    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'special_rights' => 'moderator'));
}

include_partial('documents/license');

echo '</div></div>'; // end <div id="article">

include_partial('common/content_bottom');
?>
