<?php
use_helper('Language', 'Sections', 'Viewer', 'MyImage', 'Link', 'AutoComplete'); 
$ajax_failure_feedback = sfConfig::get('app_ajax_feedback_div_name_failure');

$static_base_url = sfConfig::get('app_static_url');

$id = $sf_params->get('id');
display_page_header('images', $document, $id, $metadata, $current_version);

echo start_section_tag('Image', 'view');
$lang = $sf_user->getCulture();
$module = $sf_context->getModuleName();
echo display_picture($document->get('filename'));
?>
<p class="tips"><?php echo __('Click to display original image') ?></p>
<?php
echo end_section_tag();

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages')));
echo end_section_tag();

// lang-independent content starts here
echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document, 'user' => $creator));
if (!$document->isArchive())
{
    echo '<div class="all_associations">';
    include_partial('documents/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
    echo '</div>';
}
echo end_section_tag();

if (!$document->isArchive() && !$document->get('redirects_to')):
    echo start_section_tag('Linked documents', 'associated_docs');
    if (count($associated_docs)>0):
    ?>
    <ul id='list_associated_docs'>
    <?php
        foreach ($associated_docs as $doc): ?>
        <li>
        <?php
            $module = $doc['module'];
            echo image_tag($static_base_url . '/static/images/modules/' . $module . '_mini.png', 
                           array('alt' => __($module), 'title' => __($module)));
            echo ' ' . link_to($doc['name'], "@document_by_id_lang_slug?module=$module&id=" . $doc['id'] . 
                                             '&lang=' . $doc['culture'] . '&slug=' . formate_slug($doc['search_name']));
        ?>
        </li>
    <?php endforeach; ?>
    </ul>
    <?php
    endif;

if ($sf_user->isConnected() && !$document->get('is_protected')):
// FIXME: use CSS instead of inner-tag style
echo __('You can associate this picture with any existing document using the following tool:');
 ?>
<div id="plus" style="margin-top: 10px;">

    <div id="doc_add" style="float: left;">
    <?php echo image_tag($static_base_url . '/static/images/picto/plus.png',
                         array('title' => __('Link an existing document'), 'alt' => __('Link an existing document'))) . ' '; 
    $linkable_modules = sfConfig::get('app_modules_list');
    unset($linkable_modules[1]); // documents
    unset($linkable_modules[2]); // users
    echo select_tag('dropdown_modules', array_map('__', $linkable_modules));
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

    echo c2c_form_remote_add_element("images/addassociation?image_id=$id", 'list_associated_docs');
    ?>
    <div id="ac_form" style="float: left; margin-left: 10px; height: 30px; width: 300px;">
        <?php 
        echo input_hidden_tag('document_id', '0');
        echo c2c_auto_complete('summits', 'document_id'); ?>
    </div>
    </form>
</div> 
<?php 
    elseif (!count($associated_docs)):
        echo __("No document uses this picture.");
    endif; 
    echo end_section_tag();
endif;

include_partial('documents/map_section', array('document' => $document,
                                               'displayed_layers'  => array('images')));

if (!$document->isArchive() && !$document->get('redirects_to'))
{
    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'special_rights' => 'moderator'));
}

// TODO get image license
include_partial('documents/license', array('license' => 'by-nc-nd'));

echo '</div></div>'; // end <div id="article">

include_partial('common/content_bottom');
