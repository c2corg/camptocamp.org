<?php
use_helper('AutoComplete');
if (count($associated_summits) == 0): ?>
    <p><?php echo __('No linked summit') ?></p>
<?php
else : 
    $doc_id = $document->get('id');
    $strict = (int)$strict; // cast so that false is 0 and true is 1.
?>
    <ul class="children_docs">
    <?php foreach ($associated_summits as $summit):
              $summit_id = $summit->get('id');
              $idstring = $type . '_' . $summit_id;
    ?>
            <li class="child_summit" id="<?php echo $idstring ?>">
            <?php
            echo link_to($summit->get('name'), '@document_by_id?module=summits&id=' . $summit_id)
                        . ' - ' . $summit['elevation'] . __('meters');
            if ($sf_user->hasCredential('moderator'))
            {
                $idstring = $type . '_' . $summit_id;
                echo c2c_link_to_delete_element($type, $doc_id, $summit_id, true, $strict);
            }
            ?>
            </li>
    <?php endforeach; ?>
    </ul>
<?php
endif;
