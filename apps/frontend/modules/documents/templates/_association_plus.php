<?php 
use_helper('AutoComplete', 'Ajax');

$needs_add_display = ($sf_user->isConnected() && !$document->get('is_protected'));

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
echo '<div class="assoc_img assoc_img_'.$module.'" title="'.ucfirst(__($module)).'">';
if (count($associated_docs))
{
    echo '<span>'.ucfirst(__($module)).__('&nbsp;:').'</span>';
}
echo '</div>';
foreach ($associated_docs as $doc): ?>
    <?php
    $doc_id = $doc['id'];
    $idstring = $type . '_' . $doc_id;
    ?>
    <div class="linked_elt" id="<?php echo $idstring ?>">
        <?php echo link_to($doc['name'], "@document_by_id?module=$module&id=$doc_id");
        if ($sf_user->hasCredential('moderator'))
            echo c2c_link_to_delete_element(
                                    "documents/addRemoveAssociation?main_".$type."_id=$doc_id&linked_id=$id&mode=remove&type=$type&strict=$strict",
                                    "del_$idstring",
                                    $idstring); ?>
    </div>
<?php endforeach; ?>

<div id="<?php echo $type_list ?>"></div>

<?php 
if ($needs_add_display): // display plus sign and autocomplete form
    $form = $type . '_ac_form';
    $add = $type . '_add';
    $minus = $type . '_hide_form';
    echo c2c_form_remote_add_element("documents/addRemoveAssociation?linked_id=$id&mode=add&type=$type", $type_list);
    echo input_hidden_tag('main_' . $type . '_id', '0'); // 0 corresponds to no document
    $static_base_url = sfConfig::get('app_static_url');
    ?>
    <div class="add_assoc">
        <div id="<?php echo $type ?>_add">
            <?php echo link_to_function(image_tag($static_base_url . '/static/images/picto/plus.png',
                                                  array('title' => __('add'), 'alt' => __('add'))),
                                        "showForm('$form', '$add', '$minus')",
                                        array('class' => 'add_content')); ?>
        </div>
        <div id="<?php echo $type ?>_hide_form" style="display: none">
            <?php echo link_to_function(image_tag($static_base_url . '/static/images/picto/close.png',
                                                  array('title' => __('hide form'), 'alt' => __('hide form'))),
                                        "hideForm('$form', '$add', '$minus')",
                                        array('class'=>'add_content')); ?>
        </div>
        <div id="<?php echo $type ?>_ac_form" style="display: none;">
            <?php
            echo c2c_auto_complete($module, 'main_' . $type . '_id'); ?>
        </div>
    </div>
    </form>
<?php endif ?>

</div>
</div> <!-- one_kind_association -->

<?php endif ?>
