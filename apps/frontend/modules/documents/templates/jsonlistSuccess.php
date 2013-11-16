<?php 
use_helper('Pagination', 'Link');

$id = $sf_params->get('id');
$lang = $sf_params->get('lang');
$module = $sf_context->getModuleName();

if (!isset($items) && $nb_results > 0)
{
    $items = $pager->getResults('array', ESC_RAW);
    $items = Language::parseListItems($items, c2cTools::module2model($module));

    $totalItems = $pager->getNbResults();
    $startIndex = $pager->getMaxPerPage() * ($pager->getPage() - 1) + 1;
    $count = min($pager->getMaxPerPage(), $pager->getNbResults() - ($pager->getPage() - 1) * ($pager->getMaxPerPage()));
    $hasPreviousPage = ($pager->getPage() != 1);
    $hasNextPage = ($pager->getPage() != $pager->getLastPage() && $nb_results);
}
elseif ($nb_results == 0)
{
    $items = array();
    $totalItems = $startIndex = $count = $hasPreviousPage = $hasNextPage = 0;
}

// compute prev and next uris
if ($hasPreviousPage || $hasNextPage)
{
    $uri = _addUrlParameters(_getBaseUri(), array('page'));
    $uri .= _getSeparator($uri) . 'page=';
}
?>
{                                                                                          
  "type": "application/json",                                                              
  "totalItems": <?php echo $totalItems; ?>,
  "count": <?php echo $count; ?>,
  "startIndex": <?php echo $startIndex; ?>,
<?php if ($hasNextPage): ?>
  "nextPage": "<?php echo absolute_link(url_for($uri . $pager->getNextPage())); ?>",
<?php endif; if ($hasPreviousPage): ?>
  "previousPage": "<?php echo absolute_link(url_for($uri . $pager->getPreviousPage())); ?>",
<?php endif; ?>
  "items": [
  <?php
  $sep = '';
  foreach ($items as $item)
  {
      echo $sep;
      include_partial($module . '/jsonlist_body',  array('item' => $item));
      $sep = ',';
  } ?>
  ]
}
