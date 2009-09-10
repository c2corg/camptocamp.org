<?php
use_helper('AutoComplete');

if (count($associated_books) == 0 && !$needs_add_display): ?>
    <p><?php echo __('No linked book') ?></p>
<?php
else : 
    $doc_id = $document->get('id');
    $strict = (int)$strict; // cast so that false is 0 and true is 1.
    if (count($associated_books) > 0):
    $extra_book = false;
    ?>
    <ul class="children_docs">
    <?php foreach ($associated_books as $book):
            $book_id = $book->get('id');
            $idstring = $type . '_' . $book_id;
            $class = 'child_book';
            if (!$extra_book && isset($book['parent_id']))
            {
                $class .= ' separator';
                $extra_book = true;
            }
            echo '<li class="' . $class . '" id="' . $idstring .'">';
            echo link_to($book->get('name'), '@document_by_id?module=books&id=' . $book_id);
            if (isset($book['author']) && trim($book['author']) != '')
            {
                echo ' - ' . $book['author'];
            }
            if (!isset($book['parent_id']) && $sf_user->hasCredential('moderator'))
            {
                $idstring = $type . '_' . $book_id;
                echo c2c_link_to_delete_element(
                                    "documents/addRemoveAssociation?main_".$type."_id=$doc_id&linked_id=$book_id&mode=remove&type=$type&strict=$strict",
                                    "del_$idstring",
                                    $idstring);
            }
            ?>
            </li>
    <?php endforeach; ?>
    </ul> <?php
    endif;
    
    if ($needs_add_display): // display plus sign and autocomplete form
        $type_list = $type . '_list';
        ?>
        <div id="<?php echo $type_list ?>"></div>
        <?php 
        $form = $type . '_ac_form';
        $add = $type . '_add';
        $minus = $type . '_hide_form';
        echo c2c_form_remote_add_element("documents/addRemoveAssociation?linked_id=$doc_id&mode=add&type=$type", $type_list);
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
    <?php
    endif;

endif;
