<?php 
use_helper('Pagination', 'GeoJson');

$id = $sf_params->get('id');
$lang = $sf_params->get('lang');
$module = $sf_context->getModuleName();

$text_html = in_array('html', $format);
$add_gpx_track = in_array('track', $format);
$add_all_fields = in_array('full', $format);
$add_conditions = $add_all_fields || in_array('cond', $format);

if (!isset($items) && $nb_results > 0)
{
    $items = $pager->getResults('array', ESC_RAW);
    $items = Language::parseListItems($items, c2cTools::module2model($module));
}
elseif ($nb_results == 0)
{
    $items = array();
    $totalItems = $startIndex = $count = $hasPreviousPage = $hasNextPage = 0;
}

if (isset($pager))
{
    $totalItems = $pager->getNbResults();
    $startIndex = $pager->getMaxPerPage() * ($pager->getPage() - 1) + 1;
    $count = min($pager->getMaxPerPage(), $pager->getNbResults() - ($pager->getPage() - 1) * ($pager->getMaxPerPage()));
    $hasPreviousPage = ($pager->getPage() != 1);
    $hasNextPage = ($pager->getPage() != $pager->getLastPage() && $nb_results);
}

// compute prev and next uris
if ($hasPreviousPage || $hasNextPage)
{
    $uri = _addUrlParameters(_getBaseUri(), array('page'));
    $uri .= _getSeparator($uri) . 'page=';
}

$features = array();
foreach ($items as $item)
{
    $features[] = json_decode( get_partial($module . '/jsonlist_body'
                             , array( 'item' => $item
                                    , 'text_html' => $text_html
                                    , 'add_gpx_track' => $add_gpx_track
                                    , 'add_conditions' => $add_conditions
                                    , 'add_all_fields' => $add_all_fields
                                    )));
}
echo json_encode(array(
    'type' => 'FeatureCollection',
    'metadata' => array(
        'totalItems' => $totalItems,
        'nbItems' => $count,
        'startIndex' => $startIndex,
        'currentPage' => sfContext::getInstance()->getRequest()->getUri(),
        'nextPage' => $hasNextPage ? absolute_link(url_for($uri . $pager->getNextPage())) : null,
        'previousPage' => $hasPreviousPage ? absolute_link(url_for($uri . $pager->getPreviousPage())) : null,
        'baseLanguage' => __('meta_language'),
        'generated' => date(DATE_RFC2822)
    ),
    'features' => $features
), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
