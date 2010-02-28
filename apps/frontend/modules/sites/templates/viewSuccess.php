<?php
use_helper('Language', 'Sections', 'Viewer', 'Pagination', 'General');

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected);

display_page_header('sites', $document, $id, $metadata, $current_version);

// lang-independent content starts here

echo start_section_tag('Information', 'data');
if ($is_not_archive && $is_not_merged)
{
    $document->associated_areas = $associated_areas;
}
include_partial('data', array('document' => $document));

if ($is_not_archive)
{
    echo '<div class="all_associations">';
    
    if ($is_not_merged)
    {
        include_partial('documents/association',
                        array('associated_docs' => $associated_summits, 
                              'module' => 'summits',  // this is the module of the documents displayed by this partial
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'st', // summit-site
                              'strict' => true )); // strict looking for main_id in column main of Association table
        
        include_partial('documents/association',
                        array('associated_docs' => $associated_sites, 
                              'module' => 'sites',  // this is the module of the documents displayed by this partial
                              'reduce_name' => true,
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'tt', // site-site
                              'strict' => false )); // no strict looking for main_id in column main of Association table
                              // warning : strict is set to false since association can be with other sites
        
        include_partial('routes/association',
                        array('associated_docs' => $associated_routes,
                              'module' => 'routes',
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'tr',
                              'strict' => true,
                              'display_info' => true));
        
        include_partial('documents/association',
                        array('associated_docs' => $associated_huts, 
                              'module' => 'huts', 
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'ht', // hut-site
                              'strict' => true )); 
        
        include_partial('documents/association',
                        array('associated_docs' => $associated_parkings, 
                              'module' => 'parkings',  // this is the module of the documents displayed by this partial
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'pt', // parking-site
                              'strict' => true )); // strict looking for main_id in column main of Association table
    }
    
    include_partial('areas/association',
                    array('associated_docs' => $associated_areas,
                          'module' => 'areas',
                          'weather' => true));
    
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
    
    if ($is_not_merged)
    {
        include_partial('documents/association',
                        array('associated_docs' => $associated_articles, 
                              'module' => 'articles',
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'tc',
                              'strict' => true));
        
        if ($show_link_tool)
        {
            $modules_list = array('summits', 'sites', 'huts', 'parkings', 'routes', 'books', 'articles');
            
            echo c2c_form_add_multi_module('sites', $id, $modules_list, 13, 'multi_1', true);
        }
    }
    
    echo '</div>';
}
echo end_section_tag();

include_partial('documents/map_section', array('document' => $document,
                                               'displayed_layers'  => array('sites')));

// lang-dependent content
echo start_section_tag('Description', 'description');
if (!isset($associated_books)) $associated_books = null;
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                'needs_translation' => $needs_translation, 'associated_books' => $associated_books,
                'images' => $associated_images));
echo end_section_tag();



// associated outings section starts here
if ($is_not_archive && $is_not_merged)
{
    echo start_section_tag('Linked outings', 'outings');
    if ($nb_outings == 0)
    {
    ?>
        <p class="default_text"><?php echo __('No linked outing to this site') ?></p>
    <?php
    }
    else
    {
        foreach ($associated_outings as $count => $associated_outings_group): ?>
            <div id="outings_group_<?php echo $count ?>"<?php echo $count == 0 ? '' : ' style="display:none"'?>>
                <ul class="children_docs">
                <?php foreach ($associated_outings_group as $outing): ?>
                    <li class="child_summit">
                    <?php
                    $author_info =& $outing['versions'][0]['history_metadata']['user_private_data'];
                    echo link_to($outing->get('name'),
                                 '@document_by_id_lang_slug?module=outings&id=' . $outing->get('id') . '&lang=' . $outing->get('culture') . '&slug=' . get_slug($outing)) .
                         //' - ' . field_activities_data($outing, true, false) .
                         ' - ' . field_raw_date_data($outing, 'date') .
                         ' - ' . link_to($author_info['topo_name'],
                                         '@document_by_id?module=users&id=' . $author_info['id']) .
                         (isset($outing['nb_images']) ?
                             ' - ' . picto_tag('picto_images', __('nb_images')) . '&nbsp;' . $outing['nb_images']
                             : '');
                    ?>
                    </li>
                <?php endforeach ?>
               </ul>
               <?php if (count($associated_outings) > 1)
                         echo simple_pager_navigation($count, count($associated_outings), 'outings_group_'); ?>
           </div>
        <?php endforeach;
        
        include_partial('outings/linked_outings', array('id' => $ids, 'module' => 'sites', 'nb_outings' => $nb_outings));
    }

    if ($show_link_tool)
    {
        echo '<div class="add_content">'
             . link_to(picto_tag('picto_add', __('Associate new outing')) .
                       __('Associate new outing'),
                       "outings/edit?link=$id")
             . '</div>';
    }
    echo end_section_tag();

    include_partial('documents/images',
                    array('images' => $associated_images,
                          'document_id' => $id,
                          'dissociation' => 'moderator',
                                              'is_protected' => $document->get('is_protected')));
}

include_partial('documents/license', array('license' => 'by-sa'));

echo end_content_tag();

include_partial('common/content_bottom');
?>
