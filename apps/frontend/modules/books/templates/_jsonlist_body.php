<?php
$item_i18n = $item->getRaw('BookI18n');
$item_i18n = $item_i18n[0];
?>
{
  "type": "Feature",
  "geometry": null,
  "properties": {
    "name": <?php echo json_encode($item_i18n['name']) ?>,
    "url": "<?php echo jsonlist_url($item_i18n, 'books') ?>",
    "author": <?php echo json_encode($item->getRaw('author')) ?>,
    "editor": <?php echo json_encode($item->getRaw('editor')) ?>,
    "publicationDate": <?php echo json_encode($item['publication_date']) ?>,
    "activities": <?php echo json_encode(BaseDocument::convertStringToArray($item['activities'])) ?>,
    "types": <?php echo json_encode(BaseDocument::convertStringToArray($item['book_types'])) ?>,
    "languages": <?php echo json_encode(BaseDocument::convertStringToArray($item['langs'])) ?>,
    "nbLinkedImages": <?php echo isset($item['nb_images']) ?  $item['nb_images'] : 0 ?>,
    "nbComments": <?php echo isset($item['nb_comments']) ? $item['nb_comments'] : 0 ?>
  }
}
