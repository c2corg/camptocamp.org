<?php
use_helper('AutoComplete');
$mobile_version = c2cTools::mobileVersion();
$is_moderator = $sf_user->hasCredential('moderator');

if (count($associated_sites) == 0): ?>
    <p><?php echo __('No linked site') ?></p>
<?php else :
    $doc_id = $document->get('id');
    $strict = (int)$strict; // cast so that false is 0 and true is 1.
?>
    <ul class="children_docs">
    <?php foreach ($associated_sites as $site):
              $site_id = $site->get('id');
              $idstring = $type . '_' . $site_id;
    ?>
            <li class="child_site" id="<?php echo $idstring ?>">
            <?php
            echo link_to($site->get('name'), '@document_by_id?module=sites&id=' . $site_id)
                        . ' - ' . field_data_from_list_if_set($site, 'site_types', 'app_sites_site_types',
                                      array('multiple' => true, 'raw' => true));

            if ($is_moderator && !$mobile_version)
            {
                $idstring = $type . '_' . $site_id;
                echo c2c_link_to_delete_element($type, $doc_id, $site_id, true, $strict);
            }
            ?>
            </li>
    <?php endforeach; ?>
    </ul>
<?php
endif;
