<?php
use_helper('Language', 'Sections', 'Viewer', 'Ajax', 'AutoComplete', 'Field', 'SmartFormat', 'sfBBCode', 'Pagination'); 

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$lang = $document->getCulture();
$date = field_semantic_date_data($document, 'date');
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$mobile_version = c2cTools::mobileVersion();
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator && !$mobile_version);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected && !$mobile_version);
$section_list = array('map' => (boolean)($document->get('geom_wkt')), 'images' => $is_connected || count($associated_images));

display_page_header('xreports', $document, $id, $metadata, $current_version,
                    array('prepend' => $date, 'separator' => ', ', 'nav_options' => $section_list, 'item_type' => 'http://schema.org/Article',
                          'nb_comments' => $nb_comments));

// lang-independent content starts here

echo start_section_tag('Accident infos', 'data');
if ($is_not_archive && $is_not_merged)
{
    $document->associated_areas = $associated_areas;
}
include_partial('data', array('document' => $document, 'nb_comments' => $nb_comments));

if ($is_not_archive)
{
    echo '<div class="all_associations">';
    
    // if the user is not a moderator, but connected, use javascript to distinguish
    // between document authors and others
    
    if ($is_not_merged)
    {
        include_partial('documents/association',
                        array('associated_docs' => $associated_users, 
                              'module' => 'users', 
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'ux', // user-xreport
                              'strict' => true));
        
        include_partial('routes/association',
                        array('associated_docs' => $associated_routes, 
                              'module' => 'routes',  // this is the module of the documents displayed by this partial
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'rx', // route-xreport
                              'strict' => true, // strict looking for main_id in column main of Association table
                              'display_info' => true));

        include_partial('documents/association',
                        array('associated_docs' => $associated_sites, 
                              'module' => 'sites',  // this is the module of the documents displayed by this partial
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'tx', // site-xreport
                              'strict' => false)); // no strict looking for main_id in column main of Association table
        if ($is_connected && !$is_moderator)
        {
            $associated_users_ids = array();
            foreach ($associated_users as $user)
            {
                $associated_users_ids[] = $user['id'];
            }
            echo javascript_tag('if (['.implode(',', $associated_users_ids).'].indexOf(parseInt(document.getElementById("name_to_use").getAttribute("data-user-id"))) != -1) {
              document.body.setAttribute("data-user-author", true);
            }');
        }
    }
    
    include_partial('areas/association',
                    array('associated_docs' => $associated_areas,
                          'module' => 'areas'));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
    
    if ($is_not_merged && $show_link_tool)
    {
        $modules_list = array('routes', 'sites', 'outings', 'users', 'articles');
        
        echo c2c_form_add_multi_module('xreports', $id, $modules_list, 7, 'multi_1', true);
    }
    
    echo '</div>';
}
echo end_section_tag();

// map
if ($is_not_archive && $is_not_merged)
{
    include_partial($mobile_version ? 'documents/mobile_map_section' : 'documents/map_section', array('document' => $document));
}

// lang-dependent content
echo start_section_tag('Accident description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation, 'images' => $associated_images));
echo end_section_tag();

// profil
if ($is_connected && $is_moderator)
{
    echo start_section_tag('Profil', 'profil');
    include_partial('profil', array('document' => $document));
    echo end_section_tag();
}

if ($is_not_archive && $is_not_merged && (count($associated_images) || $is_connected))
{
    include_partial('documents/images',
                    array('images' => $associated_images,
                          'document_id' => $id,
                          'dissociation' => 'moderator',
                          'author_specific' => !$is_moderator,
                          'is_protected' => $document->get('is_protected')));
}

if ($mobile_version)
{
    include_partial('documents/mobile_comments', array('id' => $id, 'lang' => $lang, 'nb_comments' => $nb_comments));

    if ($is_connected)
    {
        $version = $document->getVersion();
        $txt = __('Edit');
        echo '<div id="edit_xreport_button" class="add_content">',
             link_to(picto_tag('picto_tools', $txt) . $txt,
                     "@document_edit_archive?module=xreports&id=$id&lang=$lang&version=$version"),
             '</div>';
        
        if ($is_not_archive && $is_not_merged && $is_connected && !$is_moderator)
        {
            echo javascript_tag("if (!document.body.hasAttribute('data-user-author')) document.getElementById('edit_xreport_button').style.display = 'none';");
        }
    }
}


if ($is_not_archive)
{
    include_partial('documents/annex_docs',
                    array('document' => $document,
                          'related_articles' => $associated_articles,
                          'related_portals' => $related_portals,
                          'show_link_to_delete' => $show_link_to_delete));
}

include_partial('documents/license', array('license' => 'by-nc-nd', 'version' => $current_version, 
                                           'created_at' => (isset($created_at) ? $created_at :  null),
                                           'timer' => $timer));

echo end_content_tag();

include_partial('common/content_bottom');
?>
