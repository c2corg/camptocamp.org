<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '<?php echo c2cTools::mobileVersion() ? sfConfig::get('app_mobile_ganalytics_key') : sfConfig::get('app_ganalytics_key') ?>']);
_gaq.push(['_trackPageview']);
_gaq.push(function() {pageTracker = _gat._getTracker('<?php echo sfConfig::get('app_ganalytics_key') ?>')});
(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
