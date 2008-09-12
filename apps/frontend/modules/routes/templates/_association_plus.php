<?php 
use_helper('AutoComplete', 'Ajax');

$needs_add_display = ($sf_user->isConnected() && !$document->get('is_protected'));
$updated_failure = sfConfig::get('app_ajax_feedback_div_name_failure');

if ( $needs_add_display || count($associated_docs) ):
?>

<div class="one_kind_association" id="<?php echo $type ?>_association">

<?php 
$strict = (int)$strict; // cast so that false is 0 and true is 1.
$id = $document->get('id');
$type_list = $type . '_list';
?>

<div class="association_content">
<?php
echo '<div class="assoc_img assoc_img_'.$module.'" title="'.__($module).'"></div>';
foreach ($associated_docs as $doc): ?>
    <?php
    $doc_id = $doc['id'];
    $idstring = $type . '_' . $doc_id;
    ?>
    <div class="linked_elt" id="<?php echo $idstring ?>">
        <?php
        echo link_to($doc['name'], "@document_by_id?module=$module&id=$doc_id");
        if (isset($display_info) && $display_info)
        {
            echo ' - ' . $doc['height_diff_up'] . ' ' . __('meters')
                 . ' - ' . field_data_from_list_if_set($doc, 'facing', 'app_routes_facings', false, true)
                 . ' - ' . field_route_ratings_data($doc);
        }
        if ($sf_user->hasCredential('moderator'))
        {
            echo c2c_link_to_delete_element(
                       "documents/addRemoveAssociation?main_".$type."_id=$doc_id&linked_id=$id&mode=remove&type=$type&strict=$strict",
                       "del_$idstring",
                       $idstring);
        }
        ?>
    </div>
<?php endforeach; ?>

<div id="<?php echo $type_list ?>"></div>

<?php
if ($needs_add_display): // display plus sign and autocomplete form
    $form = $type . '_ac_form';
    $add = $type . '_add';
    $minus = $type . '_hide_form';
    $maintypeid = 'main_' . $type . '_id';
    echo c2c_form_remote_add_element("documents/addRemoveAssociation?linked_id=$id&mode=add&type=$type", $type_list);
    ?>
    <div class="add_assoc">
    <div id="<?php echo $add ?>">
        <?php echo link_to_function(image_tag("/static/images/picto/plus.png",
                                                           array('title' => __('add'),
                                                                 'alt' => __('add'))
                                                          ),
                                                 "showForm('$form', '$add', '$minus')",
                                                 array('class' => 'add_content')
                                                ) ?>
    </div>
    <div id="<?php echo $minus ?>" style="display: none;">
        <?php echo link_to_function(image_tag("/static/images/picto/close.png",
                                                           array('title' => __('hide form'),
                                                                 'alt' => __('hide form'))),
                                                          "hideForm('$form', '$add', '$minus')",
                                                          array('class'=>'add_content')) ?>
    </div>

    <div id="<?php echo $form ?>" style="display: none;">
        <?php
echo input_hidden_tag('summit_id', '0');
echo __('Summit : ');
echo input_auto_complete_tag('summits_name', 
                            '', // default value in text field 
                            "summits/autocomplete",
                            array('size' => '20'), 
                            array('after_update_element' => "function (inputField, selectedItem) { 
                                                                $('summit_id').value = selectedItem.id;
                                                                ". remote_function(array(
                                                                                        'update' => array(
                                                                                                        'success' => 'div_' . $maintypeid, 
                                                                                                        'failure' => $updated_failure),
                                                                                        'url' => 'summits/getroutes',
                                                                                        'with' => "'summit_id=' + $('summit_id').value + '&div_id=" . $maintypeid . "'",
                                                                                        'loading'  => "Element.show('indicator');", // does not work for an unknown reason
                                                                                        'complete' => "Element.hide('indicator');",
                                                                                        'success'  => "Element.show('associated_sr');",
                                                                                        'failure'  => "Element.show('$updated_failure');" . 
                                                                visual_effect('fade', $updated_failure, array('delay' => 2, 'duration' => 3)))) ."}",
                                  'min_chars' => sfConfig::get('app_autocomplete_min_chars'), 
                                  'indicator' => 'indicator')); 
        echo '<div id="associated_sr" style="display:none;">';
        echo '<span id="div_' .$maintypeid . '"></span>';
        
        echo submit_tag(__('Add'), array(
                                    'style' =>  'padding-left: 20px;
                                                padding-right: 5px;
                                                background: url(/static/images/picto/plus.png) no-repeat 2px center;'));
        
        ?>
        </div> <!-- associated_sr -->
    </div>
    </div>
    </form>
<?php endif ?>

</div>
</div> <!-- one_kind_association -->

<?php endif ?>
