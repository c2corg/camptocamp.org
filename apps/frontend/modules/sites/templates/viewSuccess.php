<?php
use_helper('Language', 'Sections', 'Viewer', 'Pagination', 'General');

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$lang = $document->getCulture();
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$mobile_version = c2cTools::mobileVersion();
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator && !$mobile_version);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected);
$site_types = $document->getRaw('site_types');
$section_list = array('map' => (boolean)($document->get('geom_wkt')));
$nb_comments = PunbbComm::GetNbComments($id.'_'.$lang);

display_page_header('sites', $document, $id, $metadata, $current_version,
                    array('nav_options' => $section_list, 'item_type' => 'http://schema.org/Landform', 'nb_comments' => $nb_comments));

// lang-independent content starts here

echo start_section_tag('Information', 'data');
if ($is_not_archive && $is_not_merged)
{
    $document->associated_areas = $associated_areas;
}
include_partial('data', array('document' => $document, 'nb_comments' => $nb_comments));

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
        
        if ($show_link_tool && !$mobile_version)
        {
            $modules_list = array('summits', 'sites', 'huts', 'parkings', 'routes', 'books', 'articles');

            $options = array('field_prefix' => 'multi_1');
            if (check_not_empty_doc($document, 'lon'))
            {
                $options['suggest_near_docs'] = array('lon' => $document['lon'], 'lat' => $document['lat']);
                $options['suggest_exclude'] = array(
                    'summits' => get_directly_linked_ids($associated_summits),
                    'sites' => array_merge(get_directly_linked_ids($associated_sites), array((int)$id)),
                    'huts' => get_directly_linked_ids($associated_huts),
                    'parkings' => get_directly_linked_ids($associated_parkings));
            }

            echo c2c_form_add_multi_module('sites', $id, $modules_list, 13, $options);
        }
    }
    
    echo '</div>';
    include_partial('documents/geom_warning', array('document' => $document));
}
echo end_section_tag();

// lang-dependent content
echo start_section_tag('Description', 'description');
if (!isset($associated_books)) $associated_books = null;
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                'needs_translation' => $needs_translation, 'associated_books' => $associated_books,
                'images' => $associated_images));
echo end_section_tag();

if ($is_not_archive && $is_not_merged)
{
    $document->parkings = $associated_parkings;
}

include_partial($mobile_version ? 'documents/mobile_map_section' : 'documents/map_section', array('document' => $document));

// associated outings section starts here
if ($is_not_archive && $is_not_merged)
{
    if ($nb_outings > 0 || !in_array(12, $site_types))
    {
        echo start_section_tag('Linked outings', 'outings');
    }
    
    if ($nb_outings == 0)
    {
        if (!in_array(12, $site_types))
        {
            ?>
                <p class="default_text"><?php echo __('No linked outing to this site') ?></p>
            <?php
        }
    }
    else
    {
        foreach ($associated_outings as $count => $associated_outings_group): ?>
            <div id="outings_group_<?php echo $count ?>"<?php echo $count == 0 ? '' : ' style="display:none"'?>>
            <table class="children_docs"><tbody>
            <?php $culture = $sf_user->getCulture();
            $date = 0;
            foreach ($associated_outings_group as $outing):
                ?><tr><td><?php
                $timedate = $outing->get('date');
                if ($timedate != $date)
                {
                    echo '<time datetime="' . $timedate . '">' . format_date($timedate, 'D') . '</time>';
                    $date = $timedate;
                }
                ?></td><td><?php
				echo field_activities_data($outing, array('raw' => true));
                ?></td><td><?php
                $author_info =& $outing['versions'][0]['history_metadata']['user_private_data'];
                $lang = $outing->get('culture');
                echo link_to($outing->get('name'), 
                             '@document_by_id_lang_slug?module=outings&id=' . $outing->get('id') . '&lang=' . $lang . '&slug=' . get_slug($outing),
                             array('hreflang' => $lang)) .
                     ' - ' . link_to($author_info['topo_name'],
                                     '@document_by_id?module=users&id=' . $author_info['id']) .
                     (isset($outing['nb_images']) ? 
                         ' - ' . picto_tag('picto_images', __('nb_linked_images')) . '&nbsp;' . $outing['nb_images']
                         : '');
                ?></td></tr><?php
           endforeach ?>
           </tbody></table>
               <?php if (count($associated_outings) > 1)
                         echo simple_pager_navigation($count, count($associated_outings), 'outings_group_'); ?>
           </div>
        <?php endforeach;
        
        include_partial('outings/linked_outings', array('id' => $ids, 'module' => 'sites', 'nb_outings' => $nb_outings));
    }

    if ($show_link_tool && !in_array(12, $site_types))
    {
        echo '<div class="add_content">'
             . link_to(picto_tag('picto_add', __('Associate new outing')) .
                       __('Associate new outing'),
                       "outings/edit?link=$id")
             . '</div>';
    }
    if ($nb_outings > 0 || !in_array(12, $site_types))
    {
        echo end_section_tag();
    }

    include_partial('documents/images',
                    array('images' => $associated_images,
                          'document_id' => $id,
                          'dissociation' => 'moderator',
                                              'is_protected' => $document->get('is_protected')));

    if ($mobile_version) include_partial('documents/mobile_comments', array('id' => $id, 'lang' => $lang, 'nb_comments' => $nb_comments));
}

include_partial('documents/license', array('license' => 'by-sa', 'version' => $current_version, 
                                           'created_at' => (isset($created_at) ? $created_at :  null),
                                           'timer' => $timer));

echo end_content_tag();

include_partial('common/content_bottom');
?>
