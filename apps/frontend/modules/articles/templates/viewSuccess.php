<?php
use_helper('Language', 'Sections', 'Viewer', 'AutoComplete'); 

$id = $sf_params->get('id');
display_page_header('articles', $document, $id, $metadata, $current_version);

// lang-dependent content
echo start_section_tag('Article', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages')));
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
            echo image_tag($static_base_url . '/static/images/modules/' . $module . '_mini.png', 
                    array('alt' => __($module), 'title' => __($module)));
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
    <div id="doc_add" style="float: left;">
    <?php     
    echo image_tag($static_base_url . '/static/images/picto/plus.png',
                   array('title' => __('add'), 'alt' => __('add'))) . ' '; 
                                       
    $modules = array('articles', 'summits', 'books', 'huts', 'outings', 'routes', 'sites');
    if ($document->get('article_type') == 2) // only personal articles need user association
    {
        $modules[] = 'users';
    }
    $modules = array_map('__',array_intersect(sfConfig::get('app_modules_list'), $modules));
    asort($modules);
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
endif;
echo end_section_tag();
endif;

if (!$document->isArchive() && !$document->get('redirects_to'))
{
    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'special_rights' => 'moderator')); 
}

$license = $document->get('article_type') == 2 ? 'by-nc-nd' : 'by-nc-sa';
include_partial('documents/license', array('license' => $license));

echo '</div></div>'; // end <div id="article">

include_partial('common/content_bottom');
