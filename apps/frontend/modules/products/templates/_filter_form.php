<?php
use_helper('FilterForm', 'Form', 'General', 'MyForm');

echo around_selector('farnd');
$ranges_raw = $sf_data->getRaw('ranges');
$selected_areas_raw = $sf_data->getRaw('selected_areas');
include_partial('areas/areas_selector', array('ranges' => $ranges_raw, 'selected_areas' => $selected_areas_raw, 'use_personalization' => true));

include_partial('products_filter', array('autofocus' => true));
echo '<br />' . georef_selector();
?>
<br />
<?php
// FIXME : dont show tpty select due to Doctrine bug - see ticket #687
include_partial('parkings/parkings_filter', array('show_tpty' => false)) ?>
<br />
<?php echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('fcult') ?>
<br />
<?php
include_partial('documents/filter_sort');
