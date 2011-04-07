<?php $k = c2cTools::mobileVersion() ? sfConfig::get('app_mobile_ganalytics_key') : sfConfig::get('app_ganalytics_key'); ?>
<script type="text/javascript">
var _gaq = [['_setAccount', '<?php echo $k ?>'],['_setDomainName', 'none'],['_trackPageview']];
<?php if (!c2cTools::mobileVersion()): ?>_gaq.push(function() {pageTracker = _gat._getTracker('<?php echo sfConfig::get('app_ganalytics_key') ?>')});<?php endif ?>
(function(d, t) { var g = d.createElement(t), s = d.getElementsByTagName(t)[0];
g.async = 1; g.src = '//www.google-analytics.com/ga.js'; s.parentNode.insertBefore(g, s); }(document, 'script'));
<?php 
// addthis script must be added after ga tracker for google analytics integration, it will be loaded asynchronously
if ($addthis): ?>(function() {var head = $$('head')[0]; var script = new Element('script',{async: 1, src: 'http://s7.addthis.com/js/250/addthis_widget.js#domready=1'});head.appendChild(script);})();<?php endif ?>
</script>
