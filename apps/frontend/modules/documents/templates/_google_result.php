<?php if(in_array($module, array('areas', 'articles', 'books', 'huts', 'outings', 'parkings', 'routes', 'sites', 'summits'))): ?>
<br />
<div id="google_search">
<?php
use_helper('Form');
$response = sfContext::getInstance()->getResponse();
$response->addJavascript('http://www.google.com/jsapi', 'last');
$response->addJavascript(sfConfig::get('app_static_url') . '/static/js/google_search.js?' . sfSVN::getHeadRevision('google_search.js'), 'last');
?>
<span id="google_search_branding" style="float:left"></span><?php echo __('Results from google for %1%', array('%1%', $query_string)); ?><br /><br />
<div id="google_search_results"></div>
</div>
<script language="Javascript" type="text/javascript">
//<![CDATA[
module_url = "www.camptocamp.org/<?php echo $module ?>/";
google_i18n = new Array('<?php
$google_i18n = array('first page', 'previous page', 'next page', 'last page', 'More results on Google...', 'Document title', 'Extract', 'No result');
$google_i18n = array_map('__', $google_i18n);
echo implode('\', \'', $google_i18n);
?>');
Event.observe(window, 'load', function() { siteSearch.execute('<?php echo $query_string ?>'); });
//]]>
</script>
<?php endif; ?>
