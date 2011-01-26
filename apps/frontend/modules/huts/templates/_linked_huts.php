<?php
use_helper('AutoComplete');
$mobile_version = c2cTools::mobileVersion();
$is_moderator = $sf_user->hasCredential('moderator');

if (count($associated_huts) == 0): ?>
    <p><?php echo __('No linked hut') ?></p>
<?php else :
    $doc_id = $document->get('id');
    $strict = (int)$strict; // cast so that false is 0 and true is 1.
?>
    <ul class="children_docs">
    <?php foreach ($associated_huts as $hut):
              $hut_id = $hut->get('id');
              $idstring = $type . '_' . $hut_id;
     ?>
            <li class="child_hut" id="<?php echo $idstring ?>">
            <?php
            echo link_to($hut->get('name'), '@document_by_id?module=huts&id=' . $hut_id)
                        . ' - ' . $hut['elevation'] . ' ' . __('meters');
            if ($is_moderator && !$mobile_version)
            {
                $idstring = $type . '_' . $hut_id;
                echo c2c_link_to_delete_element($type, $doc_id, $hut_id, true, $strict);
            }
            ?>
            </li>
    <?php endforeach; ?>
    </ul>
<?php
endif;
