<script type="text/javascript">
var _gaq = [['_setAccount', '<?php echo c2cTools::mobileVersion() ? sfConfig::get('app_mobile_ganalytics_key') : sfConfig::get('app_ganalytics_key') ?>'],
['_setDomainName', 'none'],['_trackPageview']];
<?php if (!c2cTools::mobileVersion()): ?>_gaq.push(function() {pageTracker = _gat._getTracker('<?php echo sfConfig::get('app_ganalytics_key') ?>')});<?php endif ?>
(function(d, t) {
var g = d.createElement(t), s = d.getElementsByTagName(t)[0];
g.async = 1; g.src = '//www.google-analytics.com/ga.js'; s.parentNode.insertBefore(g, s);
}(document, 'script'));
</script>
