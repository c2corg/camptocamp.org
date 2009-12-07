<?php if(in_array($module, array('areas', 'articles', 'books', 'huts', 'outings', 'parkings', 'routes', 'sites', 'summits'))): ?>
<br />
<br />
<hr />
<br />
<script type="text/javascript">
//<![CDATA[
module_url = "www.camptocamp.org/<?php echo $module ?>/";
google_i18n = new Array('<?php
$google_i18n = array('first page', 'previous page', 'next page', 'last page', 'More results on Google...', 'Document title', 'Extract', 'No result');
$google_i18n = array_map('__', $google_i18n);
echo implode('\', \'', $google_i18n);
?>');
//]]>
</script>
<div id="google_search">
<?php
use_helper('Form');
$response = sfContext::getInstance()->getResponse();
$response->addJavascript('http://www.google.com/jsapi', 'last');
$response->addJavascript(sfConfig::get('app_static_url') . '/static/js/google_search.js', 'last');
echo __('Search with google');
echo form_tag('http://www.google.com/search', array('method'=>'get', 'onsubmit' => 'siteSearch.execute($F(google_search_input)); return false;'));
?>
<span id="google_search_branding" style="float:left"></span>
<?php echo input_tag('q', null, array('id'=>'google_search_input')); ?>
<?php echo input_hidden_tag('sitesearch', "camptocamp.org/$module"); ?>

<?php echo submit_tag(__('Search'), array('name'=>'google_search_submit', 'class' => 'picto action_filter')); ?>
</form>
<div id="google_search_results"></div>
</div>
<?php endif; ?>
