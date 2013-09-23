<?php
$item_i18n = $item->getRaw('SummitI18n');
$item_i18n = $item_i18n[0];
?>
{
  "name": <?php echo json_encode($item_i18n['name']) ?>,
  "url": "<?php echo jsonlist_url($item_i18n, 'summits') ?>",
  "elevation": <?php echo $item['elevation'] ?>,
  <?php if (is_scalar($item['lat'])): ?>
  "latitude": <?php echo $item['lat'] ?>,
  "longitude": <?php echo $item['lon'] ?>,
  <?php endif ?>
  "type": <?php echo $item['summit_type'] ?>,
  "nbLinkedImages": <?php echo isset($item['nb_images']) ?  $item['nb_images'] : 0 ?>,
  "nbComments": <?php echo isset($item['nb_comments']) ? $item['nb_comments'] : 0 ?>,
  "nbLinkedRoutes": <?php echo (isset($item['nb_linked_docs'])) ?  $item['nb_linked_docs'] : 0 ?>,
  <?php include_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations'])) ?> 
}
