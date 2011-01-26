<?php
use_helper('AutoComplete');
$mobile_version = c2cTools::mobileVersion();
$is_moderator = $sf_user->hasCredential('moderator');


if (count($associated_products) == 0): ?>
    <p><?php echo __('No linked product') ?></p>
<?php else :
    $doc_id = $document->get('id');
    $strict = (int)$strict; // cast so that false is 0 and true is 1.
?>
    <ul class="children_docs">
    <?php foreach ($associated_products as $product):
              $product_id = $product->get('id');
              $idstring = $type . '_' . $product_id;
     ?>
            <li class="child_product" id="<?php echo $idstring ?>">
            <?php
            echo link_to($product->get('name'), '@document_by_id?module=products&id=' . $product_id)
                        . ' - ' . $product['elevation'] . ' ' . __('meters');
            if ($is_moderator && !$mobile_version)
            {
                $idstring = $type . '_' . $product_id;
                echo c2c_link_to_delete_element($type, $doc_id, $product_id, true, $strict);
            }
            ?>
            </li>
    <?php endforeach; ?>
    </ul>
<?php
endif;
