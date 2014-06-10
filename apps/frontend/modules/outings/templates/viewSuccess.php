<?php
use_helper('Language', 'Sections', 'Viewer', 'Ajax', 'AutoComplete', 'Field', 'SmartFormat', 'sfBBCode');

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$lang = $document->getCulture();
$date = field_semantic_date_data($document, 'date');
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$mobile_version =  c2cTools::mobileVersion();
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator && !$mobile_version);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected && !$mobile_version);
$activities = $document->getRaw('activities');
$has_wkt = (boolean)($document->get('geom_wkt'));
$section_list = array('map' => $has_wkt, 'elevation_profile' => $has_wkt, 'images' => $is_connected || count($associated_images));
$nb_comments = PunbbComm::GetNbComments($id.'_'.$lang);

display_page_header('outings', $document, $id, $metadata, $current_version,
                    array('prepend' => $date, 'separator' => ', ', 'nav_options' => $section_list,
                          'item_type' => 'http://schema.org/Article', 'nb_comments' => $nb_comments));

// lang-independent content starts here
echo start_section_tag('Information', 'data');
$participants = explode("\n", $document->get('participants'), 2);
$participants_str = trim($participants[0]);
if (!empty($participants_str))
{
    $participants_0 = parse_links(parse_bbcode_simple($participants_str));
}
else
{
    $participants_0 = '';
}
if (isset($participants[1]))
{
    $participants_1 = _format_text_data('participants', $participants[1], null,
                                        array('needs_translation' => $needs_translation,
                                              'show_label' => $document->isArchive(),
                                              'show_images' => false));
}
else
{
    $participants_1 = '';
}
echo '<div class="all_associations col_left col_66">';
if ($is_not_archive && $is_not_merged)
{
    include_partial('documents/association',
                    array('associated_docs' => $associated_users, 
                          'extra_docs' => array($participants_1),
                          'module' => 'users', 
                          'document' => $document,
                          'inline' => true,
                          'merge_inline' => $participants_0,
                          'show_link_to_delete' => $show_link_to_delete,
                          'type' => 'uo', // user-outing
                          'strict' => true));
    
    include_partial('routes/association',
                    array('associated_docs' => $associated_routes, 
                          'module' => 'routes',  // this is the module of the documents displayed by this partial
                          'document' => $document,
                          'show_link_to_delete' => $show_link_to_delete,
                          'type' => 'ro', // route-outing
                          'strict' => true, // strict looking for main_id in column main of Association table
                          'display_info' => true));

    include_partial('documents/association',
                    array('associated_docs' => $associated_sites, 
                          'module' => 'sites',  // this is the module of the documents displayed by this partial
                          'document' => $document,
                          'show_link_to_delete' => $show_link_to_delete,
                          'type' => 'to', // site-outing
                          'strict' => false)); // no strict looking for main_id in column main of Association table
}
else
{
    echo field_text_data_if_set($document, 'participants', null, array('needs_translation' => $needs_translation, 'show_images' => false));
}
echo '</div>';

if ($is_not_archive)
{
    // if the user is not a moderator, but connected, use javascript to distinguish
    // between document authors and others
    if ($is_connected && !$is_moderator && $is_not_merged)
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
    
    echo '<div class="all_associations col_right col_33">';
    
    if ($is_not_merged)
    {
        include_partial('documents/association', array('associated_docs' => $associated_summits, 'module' => 'summits'));
        
        include_partial('documents/association', array('associated_docs' => $associated_huts, 'module' => 'huts'));
        include_partial('documents/association', array('associated_docs' => $associated_parkings, 'module' => 'parkings'));
    }

    $avalanche_bulletin = array_intersect(array(1,2,5,7), $activities);
    include_partial('areas/association',
                    array('associated_docs' => $associated_areas,
                          'module' => 'areas',
                          'box' => true,
                          'weather' => true,
                          'avalanche_bulletin' => $avalanche_bulletin,
                          'date' => $document->getRaw('date')));
    
    echo '</div>';
}

include_partial('data', array('document' => $document, 'nb_comments' => $nb_comments));

if ($show_link_tool)
{
    $modules_list = array('users', 'routes', 'sites', 'articles');
    
    echo '<div class="all_associations empty_content col_left col_66">';
    echo c2c_form_add_multi_module('outings', $id, $modules_list, 2, array('field_prefix' => 'multi_1'));
    echo '</div>';
}

echo end_section_tag();

if ($is_not_archive && $is_not_merged && $is_connected && !$is_moderator)
{
    echo javascript_tag("if (!document.body.hasAttribute('data-user-author')) document.getElementById('_association_tool').style.display = 'none';");
}

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section',
                array('document' => $document,
                      'languages' => $sf_data->getRaw('languages'),
                      'needs_translation' => $needs_translation,
                      'images' => $associated_images,
                      'associated_areas' => isset($associated_areas) ? $associated_areas : null));
echo end_section_tag();

include_partial($mobile_version ? 'documents/mobile_map_section' : 'documents/map_section', array('document' => $document));

if ($has_wkt)
{
    include_partial('documents/elevation_profile_section', array('id' => $id));
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
        echo '<div id="edit_outing_button" class="add_content">',
             link_to(picto_tag('picto_tools', $txt) . $txt,
                     "@document_edit_archive?module=outings&id=$id&lang=$lang&version=$version"),
             '</div>';
        
        if ($is_not_archive && $is_not_merged && $is_connected && !$is_moderator)
        {
            echo javascript_tag("if (!document.body.hasAttribute('data-user-author')) document.getElementById('edit_outing_button').style.display = 'none';");
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

$version = $is_moderator ? $current_version : 0;
include_partial('documents/license', array('license' => 'by-nc-nd', 'version' => $version, 
                                           'created_at' => (isset($created_at) ? $created_at :  null),
                                           'timer' => $timer));
    
echo end_content_tag();

include_partial('common/content_bottom');
?>
