<?php
use_helper('Language', 'Sections', 'Viewer', 'Ajax', 'AutoComplete', 'Pagination', 'General');

$id = $sf_params->get('id');
if (!isset($highest_summit_name)) {
    // TODO: always get summit name even in archive pages
    $highest_summit_name = '';
}
display_page_header('routes', $document, $id, $metadata, $current_version, $highest_summit_name, __('&nbsp;:').' ');

$static_base_url = sfConfig::get('app_static_url');

// lang-independent content starts here

echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document));

if (!$document->isArchive())
{
    echo '<div class="all_associations col col_30">';
    include_partial('documents/association_plus', array('associated_docs' => $associated_summits, 
                                                    'module' => 'summits', 
                                                    'document' => $document,
                                                    'type' => 'sr', // summit-route
                                                    'strict' => true )); // strict looking for main_id in column main of Association table                         
    if (count($associated_routes))
    {
        include_partial('routes/association_plus', array('associated_docs' => $associated_routes, 
                                                        'module' => 'routes', 
                                                        'document' => $document,
                                                        'type' => 'rr', // route-route
                                                        'strict' => false, // no strict looking for main_id in column main of Association table
                                                        'display_info' => true,
                                                        'title' => 'variants'));
    }
    if (count($associated_sites))
    {
        include_partial('documents/association_plus', array('associated_docs' => $associated_sites, 
                                                        'module' => 'sites', 
                                                        'document' => $document,
                                                        'type' => 'tr', // site-route
                                                        'strict' => false ));
    }
    if (count($associated_huts))
    {
    include_partial('documents/association_plus', array('associated_docs' => $associated_huts, 
                                                    'module' => 'huts', 
                                                    'document' => $document,
                                                    'type' => 'hr', // hut-route
                                                    'strict' => true )); // strict looking for main_id in column main of Association table
    }
    include_partial('documents/association_plus', array('associated_docs' => $associated_parkings, 
                                                    'module' => 'parkings', 
                                                    'document' => $document,
                                                    'type' => 'pr', // parking-route
                                                    'strict' => true ));
    echo '</div>';
    
    echo '<div class="all_associations col_right col_30">';
    include_partial('documents/association', array('associated_docs' => $associated_articles, 'module' => 'articles'));
    include_partial('areas/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
    if (!count($associated_routes))
    {
        include_partial('routes/association_plus', array('associated_docs' => $associated_routes, 
                                                        'module' => 'routes', 
                                                        'document' => $document,
                                                        'type' => 'rr', // route-route
                                                        'strict' => false, // no strict looking for main_id in column main of Association table
                                                        'display_info' => true,
                                                        'title' => 'variants'));
    }
    if (!count($associated_sites))
    {
        include_partial('documents/association_plus', array('associated_docs' => $associated_sites, 
                                                        'module' => 'sites', 
                                                        'document' => $document,
                                                        'type' => 'tr', // site-route
                                                        'strict' => false ));
        }
    if (!count($associated_huts))
    {
        include_partial('documents/association_plus', array('associated_docs' => $associated_huts, 
                                                        'module' => 'huts', 
                                                        'document' => $document,
                                                        'type' => 'hr', // hut-route
                                                        'strict' => true )); // strict looking for main_id in column main of Association table
    }
    echo '</div>';
}
echo end_section_tag();

include_partial('documents/map_section', array('document' => $document,
                                               'displayed_layers'  => array()));

// lang-dependent content
echo start_section_tag('Description', 'description');
if (!isset($associated_books)) $associated_books = null;
include_partial('documents/i18n_section',
                array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                'needs_translation' => $needs_translation, 'associated_books' => $associated_books,
                'images' => $associated_images));
echo end_section_tag();

// associated outings section starts here
if (!$document->isArchive())
{
    echo start_section_tag('Linked outings', 'outings');
    
    if ($nb_outings == 0): ?>
        <p><?php echo __('No linked outing') ?></p>
    <?php else: ?>
        <?php foreach ($associated_outings as $count => $associated_outings_group): ?>
            <div id="outings_group_<?php echo $count ?>"<?php echo $count == 0 ? '' : ' style="display:none"'?>>
            <ul class="children_docs"> 
            <?php foreach ($associated_outings_group as $outing): ?>
                <li class="child_summit"> 
                <?php
                $author_info =& $outing['versions'][0]['history_metadata']['user_private_data'];
                $georef = '';
                if (!$outing->getRaw('geom_wkt') instanceof Doctrine_Null)
                {
                    $georef = ' - ' . picto_tag('action_gps', __('has GPS track'));
                }
                echo link_to($outing->get('name'), 
                             '@document_by_id_lang_slug?module=outings&id=' . $outing->get('id') . '&lang=' . $outing->get('culture') . '&slug=' . get_slug($outing)) .  
                     ' - ' . field_activities_data($outing, true, false) .
                     ' - ' . field_raw_date_data($outing, 'date') .
                     $georef .
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
        <?php endforeach; ?>
    <?php endif;
    if ($nb_outings != 0)
    {
        include_partial('outings/linked_outings', array('id' => $route_ids, 'module' => 'route', 'nb_outings' => $nb_outings));
    }

    if ($sf_user->isConnected())
    {
        echo link_to(picto_tag('picto_add', __('Associate new outing')) .
                     __('Associate new outing'),
                     "outings/edit?link=$id", array('class' => 'add_content'));
    }
    echo end_section_tag();
}

if (!$document->isArchive() && !$document->get('redirects_to'))
{
    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'dissociation' => 'moderator'));
}

include_partial('documents/license', array('license' => 'by-sa'));

echo end_content_tag();

include_partial('common/content_bottom');
