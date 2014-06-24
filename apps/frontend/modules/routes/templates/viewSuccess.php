<?php
use_helper('Language', 'Sections', 'Viewer', 'Ajax', 'AutoComplete', 'Pagination', 'General', 'Date');

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$mobile_version = c2cTools::mobileVersion();
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator && !$mobile_version);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected);
$activities = $document->getRaw('activities');
$lang = $document->getCulture();

if (!isset($highest_summit_name)) {
    // TODO: always get summit name even in archive pages
    $highest_summit_name = '';
}
display_page_header('routes', $document, $id, $metadata, $current_version,
                    array('prepend' => $highest_summit_name, 'separator' =>  __('&nbsp;:').' ',
                          'item_type' => 'http://schema.org/Article', 'nb_comments' => $nb_comments));

// lang-independent content starts here
echo start_section_tag('Information', 'data');
$has_associated_huts = count($associated_huts);
include_partial('data', array('document' => $document, 'has_associated_huts' => $has_associated_huts, 'nb_comments' => $nb_comments));

if ($is_not_archive)
{
    if ($is_not_merged)
    {
        $summit_ids = $parking_ids = array();
        foreach ($associated_summits as $doc)
        {
            $summit_ids[] = $doc['id'];
        }
        foreach ($associated_parkings as $doc)
        {
            $parking_ids[] = $doc['id'];
        }
        $summit_ids = implode('-', $summit_ids);
        $parking_ids = implode('-', $parking_ids);
        
        echo '<div class="all_associations col col_33">';
        include_partial('documents/association',
                        array('associated_docs' => $associated_summits, 
                              'module' => 'summits',
                              'route_list_module' => 'parkings',
                              'route_list_ids' => $parking_ids,
                              'route_list_linked' => false, 
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'sr', // summit-route
                              'strict' => true )); // strict looking for main_id in column main of Association table                         
        
        include_partial('documents/association',
                        array('associated_docs' => $associated_sites, 
                              'module' => 'sites', 
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'tr', // site-route
                              'strict' => true ));
        
        include_partial('documents/association',
                        array('associated_docs' => $associated_huts, 
                              'module' => 'huts',
                              'route_list_module' => 'summits',
                              'route_list_ids' => $summit_ids,
                              'route_list_linked' => true, 
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'hr', // hut-route
                              'ghost_module' => 'summits',
                              'ghost_type' => 'sr', // summit-route
                              'strict' => true )); // strict looking for main_id in column main of Association table
        
        include_partial('documents/association',
                        array('associated_docs' => $associated_parkings, 
                              'module' => 'parkings',
                              'route_list_module' => 'summits',
                              'route_list_ids' => $summit_ids,
                              'route_list_linked' => true, 
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'pr', // parking-route
                              'strict' => true ));
        echo '</div>';
    }
    
    echo '<div class="all_associations col_right col_33">';
    $avalanche_bulletin = array_intersect(array(1,2,5), $activities);
    include_partial('areas/association',
                    array('associated_docs' => $associated_areas,
                          'module' => 'areas',
                          'weather' => true,
                          'avalanche_bulletin' => $avalanche_bulletin));
    
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
    
    echo '</div>';
    
    if ($is_not_merged)
    {
        echo '<div class="all_associations col_right col_66 no_print">';
        include_partial('routes/association',
                        array('associated_docs' => $associated_routes, 
                              'module' => 'routes', 
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'rr', // route-route
                              'strict' => false, // no strict looking for main_id in column main of Association table
                              'display_info' => true,
                              'title' => 'variants'));
        
        if ($show_link_tool && !$mobile_version)
        {
            $modules_list = array('summits', 'sites', 'huts', 'parkings', 'routes', 'books', 'articles');
            $options = array('field_prefix' => 'multi_1');

            // try to determine the "center" of the route:
            // - centroid if it has track
            // - highest linked summits with coordinates
            if (check_not_empty_doc($document, 'lon'))
            {
                $options['suggest_near_docs'] = array('lon' => $document['lon'], 'lat' => $document['lat']);
            }
            else
            {
                $summits_with_geom = array_filter($sf_data->getRaw('associated_summits'), function ($n) { return isset($n['pointwkt']); });
                if (count($summits_with_geom))
                {
                    $ref_summit = c2cTools::extractHighest($summits_with_geom);
                    $options['suggest_near_docs'] = array('lon' => $ref_summit['lon'], 'lat' => $ref_summit['lat']);
                }
            }
            if (isset($options['suggest_near_docs']))
            {
                $options['suggest_exclude'] = array(
                    'summits' => get_directly_linked_ids($associated_summits),
                    'sites' => get_directly_linked_ids($associated_sites),
                    'huts' => get_directly_linked_ids($associated_huts),
                    'parkings' => get_directly_linked_ids($associated_parkings));
            }
            
            echo c2c_form_add_multi_module('routes', $id, $modules_list, 3, $options);
        }
        echo '</div>';
    }
    
    include_partial('documents/geom_warning', array('document' => $document, 'message' => 'No GPX track, please edit this document to add some'));
}
echo end_section_tag();

// lang-dependent content
echo start_section_tag('Description', 'description');
if (!isset($associated_books)) $associated_books = null;
include_partial('documents/i18n_section',
                array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                'needs_translation' => $needs_translation, 'associated_books' => $associated_books,
                'images' => $associated_images));
echo end_section_tag();

// map
if ($is_not_archive && $is_not_merged)
{
    $document->parkings = $associated_parkings;
    $document->summits = $associated_summits;
    $document->huts = $associated_huts;
}
include_partial($mobile_version ? 'documents/mobile_map_section' : 'documents/map_section',
                array('document' => $document));

if ($is_not_archive && $is_not_merged)
{
    // associated outings section starts here
    echo start_section_tag('Linked outings', 'outings');
    
    if ($nb_outings == 0)
    {
    ?>
        <p class="default_text"><?php echo __('No linked outing') ?></p>
    <?php
    }
    else
    {
        if (!isset($nb_main_outings))
        {
            $nb_main_outings = $nb_outings;
        }
        if (!isset($nb_routes_outings))
        {
            $nb_routes_outings = 0;
        }
        
        // main outings (= outings associated to this route)
        if ($nb_main_outings > 100 || $nb_routes_outings > 0)
        {
            echo '<p>'
               . __('Outings linked to this route')
               . ($nb_main_outings > 100 ? ' (100/' . $nb_main_outings . ')' : '')
               . __('&nbsp;:')
               . '</p>';
        }
        
        foreach ($associated_outings as $count => $associated_outings_group): ?>
            <div id="outings_group_<?php echo $count ?>"<?php echo $count == 0 ? '' : ' style="display:none"'?>><?php
            if ($mobile_version)
            {
                echo '<ul class="children_docs">';
            }
            else
            {
                echo '<table class="children_docs"><tbody>';
            }
            $culture = $sf_user->getCulture();
            $date = 0;
            foreach ($associated_outings_group as $outing):
                echo !$mobile_version ? '<tr><td>' : '<li>';
                $timedate = $outing->get('date');
                if ($timedate != $date || $mobile_version)
                {
                    echo '<time datetime="' . $timedate . '">' . format_date($timedate, 'D') . '</time>';
                    $date = $timedate;
                }
                echo (!$mobile_version ? '</td><td>' : ' - ' );
                echo field_activities_data($outing, array('raw' => true));
                echo (!$mobile_version ? '</td><td>' : ' - ' );
                $author_info =& $outing['versions'][0]['history_metadata']['user_private_data'];
                $georef = '';
                if (!$outing->getRaw('geom_wkt') instanceof Doctrine_Null)
                {
                    $georef = ($mobile_version ? ' - ' : '')
                            . picto_tag('action_gps', __('has GPS track'));
                }
                
                $images = '';
                if (isset($outing['nb_images']))
                {
                    if ($mobile_version)
                    {
                        $images = ' - '
                                . picto_tag('picto_images_light')
                                . '&nbsp;'
                                . $outing['nb_images'];
                    }
                    else
                    {
                        $images = picto_tag('picto_images_light',
                                            format_number_choice('[1]1 image|(1,+Inf]%1% images',
                                                                 array('%1%' => $outing['nb_images']),
                                                                 $outing['nb_images']));
                    }
                }
                $lang = $outing->get('culture');
                echo link_to($outing->get('name'), 
                             '@document_by_id_lang_slug?module=outings&id=' . $outing->get('id') . '&lang=' . $lang . '&slug=' . get_slug($outing),
                             array('hreflang' => $lang))
                   . (!$mobile_version ? '</td><td>' : '' )
                   . $georef
                   . (!$mobile_version ? '</td><td>' : '')
                   . $images
                   . (!$mobile_version ? '</td><td>' : ' - ')
                   . link_to($author_info['topo_name'],
                                     '@document_by_id?module=users&id=' . $author_info['id']);
                echo !$mobile_version ? '</td></tr>' : '</li>';
            endforeach;
            echo !$mobile_version ? '</tbody></table>' : '</ul>';
            
            if (count($associated_outings) > 1)
                echo simple_pager_navigation($count, count($associated_outings), 'outings_group_'); ?>
           </div>
        <?php endforeach;
        
        // main outings list link
        include_partial('outings/linked_outings', array('id' => $id, 'module' => 'routes', 'nb_outings' => $nb_main_outings));
        
        // routes outings (= outings associated to routes associated to this route)
        if ($nb_routes_outings > 0)
        {
            echo '<p><br />'
               . __('Outings linked to linked routes')
               . ($nb_routes_outings > 10 ? ' (10/' . $nb_routes_outings . ')' : '')
               . __('&nbsp;:')
               . '</p>';
        
            foreach ($routes_outings as $count => $associated_outings_group): ?>
                <div id="routings_group_<?php echo $count ?>"<?php echo $count == 0 ? '' : ' style="display:none"'?>><?php
                if ($mobile_version)
                {
                    echo '<ul class="children_docs">';
                }
                else
                {
                    echo '<table class="children_docs"><tbody>';
                }
                $culture = $sf_user->getCulture();
                $date = 0;
                foreach ($associated_outings_group as $outing):
                    echo !$mobile_version ? '<tr><td>' : '<li>';
                    $timedate = $outing->get('date');
                    if ($timedate != $date || $mobile_version)
                    {
                        echo '<time datetime="' . $timedate . '">' . format_date($timedate, 'D') . '</time>';
                        $date = $timedate;
                    }
                    echo (!$mobile_version ? '</td><td>' : ' - ' );
                    echo field_activities_data($outing, array('raw' => true));
                    echo (!$mobile_version ? '</td><td>' : ' - ' );
                    $author_info =& $outing['versions'][0]['history_metadata']['user_private_data'];
                    $georef = '';
                    if (!$outing->getRaw('geom_wkt') instanceof Doctrine_Null)
                    {
                        $georef = ($mobile_version ? ' - ' : '')
                                . picto_tag('action_gps', __('has GPS track'));
                    }
                    
                    $images = '';
                    if (isset($outing['nb_images']))
                    {
                        if ($mobile_version)
                        {
                            $images = ' - '
                                    . picto_tag('picto_images_light')
                                    . '&nbsp;'
                                    . $outing['nb_images'];
                        }
                        else
                        {
                            $images = picto_tag('picto_images_light',
                                                format_number_choice('[1]1 image|(1,+Inf]%1% images',
                                                                     array('%1%' => $outing['nb_images']),
                                                                     $outing['nb_images']));
                        }
                    }
                    $lang = $outing->get('culture');
                    echo link_to($outing->get('name'), 
                                 '@document_by_id_lang_slug?module=outings&id=' . $outing->get('id') . '&lang=' . $lang . '&slug=' . get_slug($outing),
                                 array('hreflang' => $lang))
                       . (!$mobile_version ? '</td><td>' : '' )
                       . $georef
                       . (!$mobile_version ? '</td><td>' : '')
                       . $images
                       . (!$mobile_version ? '</td><td>' : ' - ')
                       . link_to($author_info['topo_name'],
                                         '@document_by_id?module=users&id=' . $author_info['id']);
                    echo !$mobile_version ? '</td></tr>' : '</li>';
                endforeach;
                echo !$mobile_version ? '</tbody></table>' : '</ul>';
                
                if (count($routes_outings) > 1)
                    echo simple_pager_navigation($count, count($routes_outings), 'routings_group_'); ?>
               </div>
            <?php endforeach;
            
            // routes outings list link
            include_partial('outings/linked_outings', array('id' => $ids, 'module' => 'routes', 'nb_outings' => $nb_routes_outings));
        }
    }

    // new outing button
    if ($show_link_tool)
    {
        echo '<div class="add_content">'
             . link_to(picto_tag('picto_add', __('Associate new outing')) .
                       __('Associate new outing'),
                       "outings/edit?link=$id")
             . '</div>';
    }
    echo end_section_tag();

    // associated images section
    include_partial('documents/images',
                    array('images' => $associated_images,
                          'document_id' => $id,
                          'dissociation' => 'moderator',
                          'is_protected' => $document->get('is_protected')));

    if ($mobile_version)
    {
        include_partial('documents/mobile_comments', array('id' => $id, 'lang' => $lang, 'nb_comments' => $nb_comments));
    }

    // annex docs section
    include_partial('documents/annex_docs',
                    array('document' => $document,
                          'related_articles' => $associated_articles,
                          'related_portals' => $related_portals,
                          'show_link_to_delete' => $show_link_to_delete));
}

include_partial('documents/license', array('license' => 'by-sa', 'version' => $current_version,
                                           'created_at' => (isset($created_at) ? $created_at :  null),
                                           'timer' => $timer));

echo end_content_tag();

include_partial('common/content_bottom');
