<?php
$item_i18n = $item->getRaw('ProductI18n');
$item_i18n = $item_i18n[0];
?>
{
  "type": "Feature",
  "geometry": <?php echo gisQuery::EWKT2GeoJSON($item->getRaw('geom_wkt')) ?>,
  "properties": {
    "name": <?php echo json_encode($item_i18n['name']) ?>,
    "url": "<?php echo jsonlist_url($item_i18n, 'products') ?>",
    "elevation": <?php echo $item['elevation'] ?>,
    "productTypes": <?php echo json_encode($item['product_type']) ?>,
    <?php if (check_not_empty((string) $item['url'])): ?>
    "website": "<?php echo $item['url'] ?>",
    <?php endif ?>
    "nbLinkedImages": <?php echo isset($item['nb_images']) ?  $item['nb_images'] : 0 ?>,
    "nbComments": <?php echo isset($item['nb_comments']) ? $item['nb_comments'] : 0 ?>,
    <?php
    include_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations']));
    echo ',';
    include_partial('parkings/parkings4jsonlist', array('parkings' => (isset($item['linked_docs']) ? $item['linked_docs'] : array())));
    ?>
  }
}
