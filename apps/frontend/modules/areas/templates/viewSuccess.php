<?php
use_helper('Language', 'Sections', 'Viewer', 'General', 'Field', 'MyForm');

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$mobile_version = c2cTools::mobileVersion();
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator && !$mobile_version);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected && !$mobile_version);

display_page_header('areas', $document, $id, $metadata, $current_version,
                    array('nb_comments' => $nb_comments));

// lang-independent content starts here
echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document));

if ($is_not_archive)
{
    echo '<div class="all_associations">';
    $associated_areas = array(array('id' => $id, 'name' => $document->getRaw('name')));
    include_partial('areas/association',
                    array('associated_docs' => $associated_areas,
                          'module' => 'areas',
                          'areas' => false,
                          'weather' => true,
                          'avalanche_bulletin' => true));
    echo '</div>';
    
    echo form_tag('documents/portalredirect', array('method' => 'get', 'class' => 'search'));
    echo '<div class="sbox">';
    echo portal_search_box_tag('areas/' . $id, 'areas');
    echo '</div></form>';
}
echo end_section_tag();
                                               
// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation, 'images' => $associated_images));
echo end_section_tag();

include_partial($mobile_version ? 'documents/mobile_map_section' : 'documents/map_section', array('document' => $document));

if ($is_not_archive && $is_not_merged)
{
    echo start_section_tag('Linked documents', 'associated_docs');
    ?>
    <div class="col_left col_50">
    <ul class="children_lists">
        <?php
        $module_list = array('summits', 'routes', 'huts', 'parkings', 'PT access points', 'sites', 'climbing_gym', 'maps', 'books');
        foreach ($module_list as $key => $module): ?><?php
            $criteria = "/$module/list?areas=$id";
            $picto = $module;
            
            if ($module == 'PT access points')
            {
                $criteria = "/parkings/list?areas=$id&tp=1-5-2-4";
                $picto = 'parkings';
            }
            elseif ($module == 'climbing_gym')
            {
                $criteria = "/sites/list?areas=$id&ttyp=12";
                $picto = 'sites';
            }
            ?>
            <li><?php echo picto_tag("picto_$picto") . ' ' . link_to(ucfirst(__($module)), $criteria); ?></li>
        <?php endforeach; ?>
    </ul>
    </div>
    <div class="col_right col_50">
    <ul class="children_lists">
        <?php
        $module_list = array('outings', 'recent conditions', 'soft mobility outings', 'xreports', 'images', 'amateurs', 'pros', 'clubs');
        foreach ($module_list as $key => $module): ?><?php
            $criteria = "/$module/list?areas=$id";
            $picto = $module;
            
            if ($module == 'outings')
            {
                $criteria .= '&orderby=date&order=desc';
            }
            elseif ($module == 'recent conditions')
            {
                $criteria = "/outings/conditions?areas=$id&date=3W&orderby=date&order=desc";
                $picto = 'outings';
            }
            elseif ($module == 'soft mobility outings')
            {
                $criteria = "/outings/list?areas=$id&owtp=yes&orderby=date&order=desc";
                $picto = 'outings';
            }
            elseif ($module == 'amateurs')
            {
                $criteria = "/users/list?areas=$id&ucat=1";
                $picto = 'users';
            }
            elseif ($module == 'pros')
            {
                $criteria = "/users/list?areas=$id&ucat=2";
                $picto = 'users';
            }
            elseif ($module == 'clubs')
            {
                $criteria = "/users/list?areas=$id&ucat=3";
                $picto = 'users';
            }
            ?>
            <li><?php echo picto_tag("picto_$picto") . ' ' . link_to(ucfirst(__($module)), $criteria); ?></li>
        <?php endforeach; ?>
    </ul>
    </div>
    <?php
    echo end_section_tag();
    
    echo start_section_tag('Latest outings', 'outings');
    include_partial('outings/linked_outings', array('id' => $id, 'module' => 'areas', 'items' => $latest_outings, 'nb_outings' => $nb_outings));
    echo end_section_tag();

    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'dissociation' => 'moderator',
                                              'is_protected' => $document->get('is_protected')));

    include_partial('documents/annex_docs', array('related_portals' => $related_portals));
}

include_partial('documents/license', array('license' => 'by-sa', 'version' => $current_version,
                                           'created_at' => (isset($created_at) ? $created_at :  null),
                                           'timer' => $timer));

echo end_content_tag();

include_partial('common/content_bottom');
?>
