<?php
use_helper('Language', 'Sections', 'Viewer', 'AutoComplete', 'General'); 

$id = $sf_params->get('id');
display_page_header('articles', $document, $id, $metadata, $current_version);

// lang-dependent content
echo start_section_tag('Article', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'), 'needs_translation' => $needs_translation));
echo end_section_tag();

// lang-independent content starts here
echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document));
?>
<div class="all_associations">
<?php 
    include_partial('documents/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps')); 
?>
</div>
<?php
echo end_section_tag();

if (!$document->isArchive() && !$document->get('redirects_to')):

    // if the user is not a moderator, and personal article, use javascript to distinguish
    // between document author(s) and others
    $moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
    if (!$moderator && ($document->get('article_type') == 2))
    {
        $associated_users_ids = array();
        foreach ($associated_users as $user)
        {
            $associated_users_ids[] = $user['id'];
        }
        echo javascript_tag('var user_is_author = (['.implode(',', $associated_users_ids).'].indexOf('.$sf_user->getId().') != -1)');
    }

    $static_base_url = sfConfig::get('app_static_url');

    echo start_section_tag('Linked documents', 'associated_docs');
    ?>
    <ul id='list_associated_docs'>
    <?php
        if (!count($associated_docs)): 
            echo __('No associated document found');
        else:
        foreach ($associated_docs as $doc):
        $doc_id = $doc->get('id');
        $module = $doc['module'];
        $type = c2cTools::Model2Letter(substr(ucfirst($module), 0, -1)).'c';
        $idstring = $type . '_' . $doc_id;
    ?>
        <li id="<?php echo $idstring ?>">
        <?php
            echo picto_tag('picto_' . $module, 'title' => __($module));
            echo ' ' . link_to($doc['name'], "@document_by_id_lang_slug?module=$module&id=" . $doc['id'] . 
                                             '&lang=' . $doc['culture'] . '&slug=' . formate_slug($doc['search_name']));
            if ($sf_user->hasCredential('moderator'))
            {
                echo c2c_link_to_delete_element(
                                  "documents/addRemoveAssociation?main_".$type."_id=$doc_id&linked_id=".$document->get('id')."&mode=remove&type=$type&strict=1",
                                  "del_$idstring",
                                  $idstring);
            }
        ?>
        </li>
        <?php endforeach; 
        endif; ?>
    </ul>

    <?php 
    if ($sf_user->isConnected()):
    ?>
    <br />
    <div id="doc_add" style="float: left;">
    <?php
    echo picto_tag('picto_add', __('Link an existing document')) . ' '; 
                                       
    $modules = array('articles', 'summits', 'sites', 'routes', 'huts', 'parkings', 'outings', 'books');
    if ($document->get('article_type') == 2) // only personal articles need user association
    {
        $modules[] = 'users';
    }
    $modules = array_map('__',array_intersect(sfConfig::get('app_modules_list'), $modules));
    echo select_tag('dropdown_modules', $modules);
    ?> 
    </div>

    <?php 
    echo observe_field('dropdown_modules', array(
        'update' => 'ac_form',
        'url' => '/documents/getautocomplete',
        'with' => "'module_id=' + value",
        'script' => 'true',
        'loading' => "Element.show('indicator')",
        'complete' => "Element.hide('indicator')"));

    echo c2c_form_remote_add_element("articles/addassociation?article_id=$id", 'list_associated_docs');
    //echo input_hidden_tag('document_id', '0');
    ?>
    <div id="ac_form" style="float: left; margin-left: 10px; height: 30px; width: 300px;">
        <?php 
        echo input_hidden_tag('document_id', '0'); // added here and commented above
        echo c2c_auto_complete('articles', 'document_id'); ?>
    </div>
    </form>
<?php
    if (!$moderator)
    {
        echo javascript_tag("if (!user_is_author) { $('doc_add').hide(); $('ac_form').hide(); }");
    }
    endif;
echo end_section_tag();
endif;

if (!$document->isArchive() && !$document->get('redirects_to'))
{
    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'dissociation' => 'moderator',
                                              'author_specific' => !$moderator)); 
}

$licenses_array = sfConfig::get('app_licenses_list');
$license = $licenses_array[$document->get('article_type')];
include_partial('documents/license', array('license' => $license));

echo '</div></div>'; // end <div id="article">

include_partial('common/content_bottom');
