<?php
$item_i18n = $item->getRaw('ArticleI18n');
$item_i18n = $item_i18n[0];
?>
{
  "type": "Feature",
  "geometry": null,
  "properties": {
    "name": <?php echo json_encode($item_i18n['name']) ?>,
    "url": "<?php echo jsonlist_url($item_i18n, 'articles') ?>",
    "type": <?php echo $item['article_type'] ?>,
    "categories":  <?php echo json_encode(BaseDocument::convertStringToArray($item['categories'])) ?>,
    "activities":  <?php echo json_encode(BaseDocument::convertStringToArray($item['activities'])) ?>,
    "nbLinkedImages": <?php echo isset($item['nb_images']) ?  $item['nb_images'] : 0 ?>,
    "nbComments": <?php echo isset($item['nb_comments']) ? $item['nb_comments'] : 0 ?>
  }
}
