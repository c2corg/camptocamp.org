<?php
$item_i18n = $item->getRaw('PortalI18n');
$item_i18n = $item_i18n[0];
?>
{
  "type": "Feature",
  "geometry": null,
  "properties": {
    "name": <?php echo json_encode($item_i18n['name']) ?>,
    "url": "<?php echo jsonlist_url($item_i18n, 'portals') ?>",
    "activities": <?php echo json_encode(BaseDocument::convertStringToArray($item['activities'])) ?>,
    "nbLinkedImages": <?php echo isset($item['nb_images']) ?  $item['nb_images'] : 0 ?>,
    "nbComments": <?php echo isset($item['nb_comments']) ? $item['nb_comments'] : 0 ?>,
    <?php include_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations'])) ?>
  }
}
