<?php if(in_array($module, array('areas', 'articles', 'books', 'huts', 'outings', 'parkings', 'routes', 'sites', 'summits'))): ?>
<br />
<div id="google_search">
<?php
use_helper('Form', 'JavascriptQueue');
$response = sfContext::getInstance()->getResponse();
$response->addJavascript('/static/js/google_search.js', 'last');
echo image_tag('http://www.google.com/uds/css/small-logo.png');
echo __('Results from google for %1%', array('%1%' => $query_string)); ?>
<div id="google_search_results"></div>
</div>
<?php
$google_i18n = array('first page', 'previous page', 'next page', 'Document title', 'Extract', 'No result');
$google_i18n = array_map('__', $google_i18n);
$cse = sfConfig::get('app_google_cse');
echo javascript_queue("C2C.GoogleSearch = C2C.GoogleSearch || {}; 
C2C.GoogleSearch.i18n = new Array('" . implode("', '", $google_i18n) . "');" .
"C2C.GoogleSearch.base_url = 'https://www.googleapis.com/customsearch/v1?key=" . sfConfig::get('app_google_api_key') .
'&cx=' . $cse[$module] . "&callback=C2C.GoogleSearch.handleResponse';
C2C.GoogleSearch.q = '" . urlencode($query_string) . "';
Event.observe(window, 'load', function() { C2C.GoogleSearch.search(); });");
endif;
