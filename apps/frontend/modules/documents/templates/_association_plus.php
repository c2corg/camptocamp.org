<?php 
use_helper('AutoComplete', 'Ajax', 'General', 'Field');

$needs_add_display = ($sf_user->isConnected() && !$document->get('is_protected'));

if ( $needs_add_display || count($associated_docs) ):
?>

<div class="one_kind_association<?php echo count($associated_docs) == 0 ? ' empty_content' : '' ?>" id="<?php echo $type ?>_association">

<?php 
$strict = (int)$strict; // cast so that false is 0 and true is 1.
$id = $document->get('id');

$type_list = $type . '_list';
?>

<div class="association_content">
<?php
echo '<div class="assoc_img picto_'.$module.'" title="'.ucfirst(__($module)).'">';
if (count($associated_docs))
{
    echo '<span>'.ucfirst(__($module)).__('&nbsp;:').'</span>';
}
echo '</div>';
foreach ($associated_docs as $doc): ?>
    <?php
    $doc_id = $doc['id'];
    $idstring = $type . '_' . $doc_id;
    $class = 'linked_elt';
    if (isset($doc['is_child']) and $doc['is_child'])
    {
        $class .= ' child';
    }
    if (isset($doc['parent_id']))
    {
        $class .= ' extra';
    }
    echo '<div class="' . $class . '" id="' . $idstring . '">' . "\n";
    if ($module != 'users')
    {
        $name = ucfirst($doc['name']);
        $url = "@document_by_id_lang_slug?module=$module&id=$doc_id" . '&lang=' . $doc['culture'] . '&slug=' . formate_slug($doc['search_name']);
    }
    else
    {
        $name = $doc['name'];
        $url = "@document_by_id_lang?module=$module&id=$doc_id" . '&lang=' . $doc['culture'];
    }
    echo link_to($name, $url);
    if (isset($doc['lowest_elevation']) && is_scalar($doc['lowest_elevation']))
    {
        echo '&nbsp; ' . $doc['lowest_elevation'] . __('meters') . __('range separator') . $doc['elevation'] . __('meters');
    }
    else if (is_scalar($doc['elevation']))
    {
        echo '&nbsp; ' . $doc['elevation'] . __('meters');
    }
    if (isset($doc['public_transportation_types']))
    {
        echo '&nbsp; '. field_data_from_list_if_set($doc, 'public_transportation_types', 'app_parkings_public_transportation_types', true, true);
    }

    if (!isset($doc['parent_id']) and $sf_user->hasCredential('moderator'))
    {
        echo ' ' . c2c_link_to_delete_element('documents/addRemoveAssociation?main_' . $type .
                                        "_id=$doc_id&linked_id=$id&mode=remove&type=$type&strict=$strict",
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
    echo c2c_form_remote_add_element("documents/addRemoveAssociation?linked_id=$id&mode=add&type=$type", $type_list);
    echo input_hidden_tag('main_' . $type . '_id', '0'); // 0 corresponds to no document
    $static_base_url = sfConfig::get('app_static_url');
    ?>
    <div class="add_assoc">
        <div id="<?php echo $type ?>_add">
            <?php echo link_to_function(picto_tag('picto_add', __('Link an existing document')),
                                        "showForm('$form', '$add', '$minus')",
                                        array('class' => 'add_content')); ?>
        </div>
        <div id="<?php echo $type ?>_hide_form" style="display: none">
            <?php echo link_to_function(picto_tag('picto_rm', __('hide form')),
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
