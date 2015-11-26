<?php
use_helper('AutoComplete');
$mobile_version = c2cTools::mobileVersion();
$is_moderator = $sf_user->hasCredential('moderator');


if (count($associated_xreports) == 0): ?>
    <p><?php echo __('No linked xreport') ?></p>
<?php else :
    $doc_id = $document->get('id');
    $strict = (int)$strict; // cast so that false is 0 and true is 1.
?>
    <ul class="children_docs">
    <?php foreach ($associated_xreports as $xreport):
              $xreport_id = $xreport->get('id');
              $idstring = $type . '_' . $xreport_id;
     ?>
            <li class="child_xreport" id="<?php echo $idstring ?>">
            <?php
            echo link_to($xreport->get('name'), '@document_by_id?module=xreports&id=' . $xreport_id)
                        . ' - ' . $xreport['elevation'] . ' ' . __('meters');
            if ($is_moderator && !$mobile_version)
            {
                $idstring = $type . '_' . $xreport_id;
                echo c2c_link_to_delete_element($type, $doc_id, $xreport_id, true, $strict);
            }
            ?>
            </li>
    <?php endforeach; ?>
    </ul>
<?php
endif;
