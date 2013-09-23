<?php
$item_i18n = $item->getRaw('HutI18n');
$item_i18n = $item_i18n[0];
?>
{
  "name": <?php echo json_encode($item_i18n['name']) ?>,
  "url": "<?php echo jsonlist_url($item_i18n, 'huts') ?>",
  "type": <?php echo $item['shelter_type'] ?>,
  "elevation": <?php echo $item['elevation'] ?>,
  <?php if (is_scalar($item['lat'])): ?>
  "latitude": <?php echo $item['lat'] ?>,
  "longitude": <?php echo $item['lon'] ?>,
  <?php endif; if (is_scalar($item['staffed_capacity']) && $item['staffed_capacity'] >= 0): ?>
  "staffedCapacity": <?php echo $item['staffed_capacity'] ?>,
  <?php endif; if (is_scalar($item['unstaffed_capacity']) && $item['unstaffed_capacity'] >= 0): ?>
  "unstaffedCapacity": <?php echo $item['unstaffed_capacity'] ?>,
  <?php endif ?>
  "activities": <?php echo json_encode(BaseDocument::convertStringToArray($item['activities'])) ?>,
  <?php if (check_not_empty((string) $item['phone'])): ?>
  "phone": <?php echo json_encode($item['phone']) ?>,
  <?php endif; if (check_not_empty((string) $item['url'])): ?>
  "website": "<?php echo $item['url'] ?>",
  <?php endif ?>
  "nbLinkedImages": <?php echo isset($item['nb_images']) ?  $item['nb_images'] : 0 ?>,
  "nbComments": <?php echo isset($item['nb_comments']) ? $item['nb_comments'] : 0 ?>,
  "nbLinkedRoutes": <?php echo isset($item['nb_linked_docs']) ? $item['nb_linked_docs'] : 0 ?>,
  <?php
  include_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations']));
  echo ',';
  include_partial('parkings/parkings4jsonlist', array('parkings' => (isset($item['linked_docs']) ? $item['linked_docs'] : array())));
  ?>
}
