<?php 
use_helper('AutoComplete', 'Ajax', 'Field', 'General');

$needs_add_display = ($sf_user->isConnected() && (!$document->get('is_protected') || $sf_user->hasCredential('moderator')));
$show_link_to_delete = $sf_user->hasCredential('moderator');
$updated_failure = sfConfig::get('app_ajax_feedback_div_name_failure');

if ($needs_add_display || count($associated_docs)):
?>

<div class="one_kind_association" id="<?php echo $type ?>_association">

<?php 
$strict = (int)$strict; // cast so that false is 0 and true is 1.
$id = $document->get('id');
$type_list = $type . '_list';
?>

<div class="association_content">
<?php
echo '<div class="assoc_img picto_'.$module.'" title="'.ucfirst(__($module)).'">';
if (!isset($title) && count($associated_docs))
{
    echo '<span>'.ucfirst(__($module)).__('&nbsp;:').'</span>';
}
echo '</div>';
if (isset($title))
{
    $print = (count($associated_docs)) ? '' : ' no_print';
    echo '<div id="_' . $title . '" class="section_subtitle' . $print . '">' . __($title) . '</div>';
}
foreach ($associated_docs as $doc): ?>
    <?php
    $doc_id = $doc['id'];
    $idstring = $type . '_' . $doc_id;
    ?>
    <div class="linked_elt" id="<?php echo $idstring ?>">
        <?php
        echo link_to($doc['name'], "@document_by_id_lang_slug?module=$module&id=$doc_id&lang=" . $doc['culture'] . '&slug=' . formate_slug($doc['search_name']));
        if (isset($display_info) && $display_info)
        {
            echo '<div class="short_data">';
            echo summarize_route($doc, true, true);
        }
        if ($show_link_to_delete)
        {
            echo c2c_link_to_delete_element($type, $doc_id, $id, false, $strict);
        }
        if (isset($display_info) && $display_info)
        {
            echo '</div>';
        }
        ?>
    </div>
<?php endforeach;

if ($needs_add_display): // display plus sign and autocomplete form
     ?>
    <div id="<?php echo $type_list ?>"></div>
    <?php
    $maintypeid = 'main_' . $type . '_id';
    $main_module = $document->get('module');
    $linked_module_param = $type . '_document_module';
    echo c2c_form_remote_add_element("$main_module/addAssociation?form_id=$type&main_id=$id&$linked_module_param=routes&div=1", $type_list);
    ?>
    <div class="add_assoc">
        <div id="<?php echo $type ?>_add">
        <?php echo link_to_function(picto_tag('picto_add', __('Link an existing document')),
                                    "showForm('$type')",
                                    array('class' => 'add_content')); ?>
    </div>
        <div id="<?php echo $type ?>_hide" style="display: none">
        <?php echo link_to_function(picto_tag('picto_rm', __('hide form')),
                                    "hideForm('$type')",
                                    array('class'=>'add_content')); ?>
    </div>

        <div id="<?php echo $type ?>_form" style="display: none;">
        <?php
echo input_hidden_tag('rsummit_id', '0');
echo __('Summit : ');
echo input_auto_complete_tag('summits_name', 
                            '', // default value in text field 
                            "summits/autocomplete",
                            array('size' => '50', 'id' => 'rsummits_name'), 
                            array('after_update_element' => "function (inputField, selectedItem) { 
                                                                $('rsummit_id').value = selectedItem.id;
                                                                ". remote_function(array(
                                                                                        'update' => array(
                                                                                                        'success' => 'div_' . $maintypeid, 
                                                                                                        'failure' => $updated_failure),
                                                                                        'url' => 'summits/getroutes',
                                                                                        'with' => "'summit_id=' + $('rsummit_id').value + '&div_id=" . $maintypeid . "'",
                                                                                        'loading'  => "Element.show('indicator');", // does not work for an unknown reason
                                                                                        'complete' => "Element.hide('indicator');",
                                                                                        'success'  => "Element.show('associated_sr');",
                                                                                        'failure'  => "Element.show('$updated_failure');" . 
                                                                visual_effect('fade', $updated_failure, array('delay' => 2, 'duration' => 3)))) ."}",
                                  'min_chars' => sfConfig::get('app_autocomplete_min_chars'), 
                                  'indicator' => 'indicator')); 
        echo '<div id="associated_sr" style="display:none;">';
        echo '<span id="div_' .$maintypeid . '"></span>';
        
        echo submit_tag(__('Link'), array('class' =>  'picto action_create'));
        
        ?>
        </div> <!-- associated_sr -->
    </div>
    </div>
    </form>
<?php endif ?>

</div>
</div> <!-- one_kind_association -->

<?php endif ?>
