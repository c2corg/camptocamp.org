<?php
if(in_array($module, array('areas', 'articles', 'books', 'huts', 'outings', 'parkings', 'routes', 'sites', 'summits'))): ?>
<br />
<br />
<hr />
<br />
<?php
use_helper('Form', 'JavascriptQueue', 'MyMinify');
$google_i18n = array('first page', 'previous page', 'next page', 'Document title', 'Extract', 'No result');
$google_i18n = array_map('__', $google_i18n);
$cse = sfConfig::get('app_google_cse');
$script = minify_get_combined_files_url('/static/js/google_search.js');

echo javascript_queue("$.extend(C2C.GoogleSearch = C2C.GoogleSearch || {}, {
  i18n: ['" . implode("', '", $google_i18n) . "']," ."
  base_url: 'https://www.googleapis.com/customsearch/v1?key=" . sfConfig::get('app_google_api_key') .
    '&cx=' . $cse[$module] . "&callback=C2C.GoogleSearch.handleResponse',
  alternate_url: 'https://www.google.com/cse?cx=" . $cse[$module] ."'});
$.ajax({
  url: '$script',
  dataType: 'script',
  cache: true })
.done(function() {
  $('#google_search_input').prop('disabled', false);
});");
?>
<div id="google_search">
<?php
echo __('Search with google');
echo form_tag('https://www.google.com/search', array('method'=>'get',
    'onsubmit' => 'C2C.GoogleSearch.q=$("#google_search_input").val(); C2C.GoogleSearch.search(); return false;'));
echo input_tag('q', null, array('id'=>'google_search_input',
                                'data-lang' => __('meta_language'),
                                'onblur' => "if (this.value == '') this.className = '';",
                                'disabled' => 'disabled',
                                'onfocus' => "this.className = 'no-logo';"));
echo input_hidden_tag('sitesearch', "camptocamp.org/$module"); ?>
<div id="google_search_submit" onclick="$(this).parent().submit();"></div>
</form>
<div id="google_search_results"></div>
</div>
<?php endif;
