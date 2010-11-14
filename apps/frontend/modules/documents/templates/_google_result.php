<?php if(in_array($module, array('areas', 'articles', 'books', 'huts', 'outings', 'parkings', 'routes', 'sites', 'summits'))): ?>
<br />
<div id="google_search">
<?php
use_helper('Form');
$response = sfContext::getInstance()->getResponse();
$response->addJavascript('/static/js/google_search.js', 'last');
echo image_tag('http://www.google.com/uds/css/small-logo.png');
echo __('Results from google for %1%', array('%1%' => $query_string)); ?>
<div id="google_search_results"></div>
</div>
<script type="text/javascript">
//<![CDATA[
Event.observe(window, 'load', function() { 
GoogleSearch.i18n = new Array('<?php
$google_i18n = array('first page', 'previous page', 'next page', 'Document title', 'Extract', 'No result');
$google_i18n = array_map('__', $google_i18n);
echo implode('\', \'', $google_i18n);
?>');
<?php $cse = sfConfig::get('app_google_cse'); ?>
GoogleSearch.base_url = 'https://www.googleapis.com/customsearch/v1?key=<?php echo sfConfig::get('app_google_api_key') ?>&cx=<?php echo $cse[$module] ?>&callback=GoogleSearch.handleResponse';
GoogleSearch.q = '<?php echo urlencode($query_string) ?>';
GoogleSearch.search();
});
//]]></script>
<?php endif; ?>
