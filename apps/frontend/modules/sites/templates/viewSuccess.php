<?php
use_helper('Language', 'Sections', 'Viewer');

$id = $sf_params->get('id');
display_page_header('sites', $document, $id, $metadata, $current_version);

// lang-independent content starts here

echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document));

if (!$document->isArchive())
{
    echo '<div class="all_associations">';
    include_partial('documents/association_plus', array('associated_docs' => $associated_sites, 
                                                    'module' => 'sites',  // this is the module of the documents displayed by this partial
                                                    'document' => $document,
                                                    'type' => 'tt', // site-site
                                                    'strict' => false )); // no strict looking for main_id in column main of Association table
                                                    // warning : strict is set to false since association can be with other sites

    include_partial('documents/association_plus', array('associated_docs' => $associated_parkings, 
                                                    'module' => 'parkings',  // this is the module of the documents displayed by this partial
                                                    'document' => $document,
                                                    'type' => 'pt', // parking-site
                                                    'strict' => true )); // strict looking for main_id in column main of Association table
                                                    // warning : strict is false since association can be with other sites

    include_partial('documents/association_plus', array('associated_docs' => $associated_huts, 
                                                    'module' => 'huts', 
                                                    'document' => $document,
                                                    'type' => 'ht', // hut-site
                                                    'strict' => true )); 

    include_partial('documents/association_plus', array('associated_docs' => $associated_summits, 
                                                    'module' => 'summits',  // this is the module of the documents displayed by this partial
                                                    'document' => $document,
                                                    'type' => 'st', // summit-site
                                                    'strict' => true )); // strict looking for main_id in column main of Association table

    include_partial('documents/association_plus', array('associated_docs' => $associated_books,
                                                        'module' => 'books',
                                                        'document' => $document,
                                                        'type' => 'bt', // book-site
                                                        'strict' => true));

    include_partial('documents/association', array('associated_docs' => $associated_articles, 'module' => 'articles'));
    include_partial('documents/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
    echo '</div>';
}
echo end_section_tag();

include_partial('documents/map_section', array('document' => $document,
                                               'displayed_layers'  => array('sites')));

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages')));
echo end_section_tag();



// associated outings section starts here
if (!$document->isArchive()):
$nb_outings = count($associated_outings);
echo start_section_tag('Linked outings', 'outings');
if ($nb_outings == 0):
?>
    <p><?php echo __('No linked outing') ?></p>
<?php else: ?>
<ul class="children_docs">
<?php foreach ($associated_outings as $outing): ?>
        <li class="child_summit">
        <?php
        $author_info =& $outing['versions'][0]['history_metadata']['user_private_data'];
        echo link_to($outing->get('name'), '@document_by_id?module=outings&id=' . $outing->get('id')) .  
                     //' - ' . field_activities_data($outing, true) .
                     ' - ' .  field_raw_date_data($outing, 'date') .
                     ' - ' . link_to($author_info[$author_info['name_to_use']],
                                     '@document_by_id?module=users&id=' . $author_info['id']);
        ?>
        </li>
<?php endforeach; ?>
</ul>
<?php
endif;

if ($sf_user->isConnected())
{
    echo link_to(image_tag('/static/images/picto/plus.png',
                           array('title' => __('Associate new outing'),
                                 'alt' => __('Associate new outing'))) .
                 __('Associate new outing'),
                 "outings/edit?link=$id", array('class' => 'add_content'));
}
echo end_section_tag();
endif;

if (!$document->isArchive() && !$document->get('redirects_to'))
{
    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'special_rights' => false)); // FIXME: what does that mean, special_rights ?
}

include_partial('documents/license');

echo '</div></div>'; // end <div id="article">

include_partial('common/content_bottom');
?>
