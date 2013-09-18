<?php
$item_i18n = $item->getRaw('SiteI18n');
$item_i18n = $item_i18n[0];
?>
{
  "name": <?php echo json_encode($item_i18n['name']) ?>,
  "url": "<?php echo jsonlist_url($item_i18n, 'sites') ?>",
  "elevation": <?php echo $item['elevation'] ?>,
  <?php if (is_scalar($item['lat'])): ?>
  "latitude": <?php echo $item['lat'] ?>,
  "longitude": <?php echo $item['lon'] ?>,
  <?php endif ?>
  "routes": <?php echo $item['routes_quantity'] ?>,
  "site_types": <?php echo json_encode(BaseDocument::convertStringToArray($item['site_types'])) ?>,
  "rock_types": <?php echo json_encode(BaseDocument::convertStringToArray($item['rock_types'])) ?>,
  "nbLinkedImages": <?php echo isset($item['nb_images']) ?  $item['nb_images'] : 0 ?>,
  "nbComments": <?php echo isset($item['nb_comments']) ? $item['nb_comments'] : 0 ?>,
  "nbLinkedOutings": <?php echo isset($item['nb_linked_docs']) ? $item['nb_linked_docs'] : 0 ?>,
  <?php
  include_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations']));
  echo ',';
  include_partial('parkings/parkings4jsonlist', array('parkings' => (isset($item['linked_docs']) ? $item['linked_docs'] : array())));
  ?>  
}
