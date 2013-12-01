<?php if(in_array($module, array('areas', 'articles', 'books', 'huts', 'outings', 'parkings', 'routes', 'sites', 'summits'))): ?>
<br />
<div id="google_search">
<?php
use_helper('Form', 'JavascriptQueue', 'MyMinify');
echo image_tag('//www.google.com/uds/css/small-logo.png');
echo __('Results from google for %1%', array('%1%' => $query_string)); ?>
<div id="google_search_results"></div>
</div>
<?php
$google_i18n = array('first page', 'previous page', 'next page', 'Document title', 'Extract', 'No result');
$google_i18n = array_map('__', $google_i18n);
$cse = sfConfig::get('app_google_cse');
$script = minify_get_combined_files_url('/static/js/google_search.js');

echo javascript_queue("$.extend(C2C.GoogleSearch = C2C.GoogleSearch || {}, {
  i18n: ['" . implode("', '", $google_i18n) . "']," . "
  base_url: 'https://www.googleapis.com/customsearch/v1?key=" . sfConfig::get('app_google_api_key') .
    '&cx=' . $cse[$module] . "&callback=C2C.GoogleSearch.handleResponse',
  q: '" . urlencode($query_string) . "'});
$.ajax({
    url: '$script',
    dataType: 'script',
    cache: true })
.done(function() {
  C2C.GoogleSearch.search();
});");
endif;
