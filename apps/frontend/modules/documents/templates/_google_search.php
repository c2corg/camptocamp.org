<?php
if(in_array($module, array('areas', 'articles', 'books', 'huts', 'outings', 'parkings', 'routes', 'sites', 'summits'))): ?>
<br />
<br />
<hr />
<br />
<?php
use_helper('Form', 'JavascriptQueue');
sfContext::getInstance()->getResponse()->addJavascript('/static/js/google_search.js', 'last');
$google_i18n = array('first page', 'previous page', 'next page', 'Document title', 'Extract', 'No result');
$google_i18n = array_map('__', $google_i18n);
$cse = sfConfig::get('app_google_cse');

echo javascript_queue("C2C.GoogleSearch = C2C.GoogleSearch || {}; 
C2C.GoogleSearch.i18n = new Array('" . implode("', '", $google_i18n) . "');" .
"C2C.GoogleSearch.base_url = 'https://www.googleapis.com/customsearch/v1?key=" . sfConfig::get('app_google_api_key') .
'&cx=' . $cse[$module] . "&callback=C2C.GoogleSearch.handleResponse';
C2C.GoogleSearch.alternate_url = 'http://www.google.com/cse?cx=" . $cse[$module] ."';");
?>
<div id="google_search">
<?php
echo __('Search with google');
echo form_tag('http://www.google.com/search', array('method'=>'get',
    'onsubmit' => 'C2C.GoogleSearch.q=jQuery("#google_search_input").val(); C2C.GoogleSearch.search(); return false;'));
echo input_tag('q', null, array('id'=>'google_search_input',
                                'data-lang' => __('meta_language'),
                                'onblur' => "if (this.value == '') this.className = '';",
                                'onfocus' => "this.className = 'no-logo';"));
echo input_hidden_tag('sitesearch', "camptocamp.org/$module"); ?>
<div id="google_search_submit" onclick="jQuery(this).parent().submit();"></div>
</form>
<div id="google_search_results"></div>
</div>
<?php endif;
