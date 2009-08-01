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
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'), 'needs_translation' => $needs_translation));
echo end_section_tag();

if (!$document->isArchive() && !$document->get('redirects_to'))
{
    echo start_section_tag('Linked documents', 'associated_docs');
    ?>
    <ul id="list_associated_docs">
        <?php
        foreach (array('summits', 'routes', 'outings', 'recent conditions', 'huts', 'parkings', 'sites', 'climbing_gym', 'images') as $module): ?><?php
            $criteria = "/$module/list?areas=$id";
            
            if ($module == 'outings')
            {
                $criteria .= '&orderby=date&order=desc';
            }
            else if ($module == 'recent conditions')
            {
                $criteria = "/outings/conditions?areas=$id&date=3W&orderby=date&order=desc";
            }
            else if ($module == 'climbing_gym')
            {
                $criteria = "/sites/list?areas=$id&styp=12";
            }
            ?>
            <li><?php echo link_to(ucfirst(__($module)), $criteria); ?></li>
        <?php endforeach; ?>
    </ul>
    <?php
    echo end_section_tag();
    
    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'dissociation' => 'moderator'));
}

include_partial('documents/license', array('license' => 'by-sa'));

echo '</div></div>'; // end <div id="article">

include_partial('common/content_bottom');
?>
