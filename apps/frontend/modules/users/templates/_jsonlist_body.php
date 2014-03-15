<?php
$item_i18n = $item->getRaw('UserI18n');
$item_i18n = $item_i18n[0];
?>
{
  "type": "Feature",
  "geometry": null,
  "properties": {
    "name": <?php echo json_encode($item_i18n['name']) ?>,
    "activities": <?php echo json_encode(BaseDocument::convertStringToArray($item['activities'])) ?>,
    <?php if (check_not_empty($item->getRaw('category'))):  ?>
    "category": <?php echo $item['category'] ?>,
    <?php endif ?>
  }
}
