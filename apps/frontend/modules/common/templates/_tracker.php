<?php
$k = c2cTools::mobileVersion() ? sfConfig::get('app_mobile_ganalytics_key') : sfConfig::get('app_ganalytics_key');
// keep connection status with analytics
$status = $sf_user->isConnected() ? 'Member' : 'Visitor';
// track forum id on viewtopic and viewforum pages
$forum_track = isset($tracker_forum_id) ? ",['_setCustomVar',2,'Forum Id','$tracker_forum_id',3]": '';
?>
<script type="text/javascript">
var _gaq = [['_setAccount','<?php echo $k ?>'],['_setDomainName','none'],['_setCustomVar',1,'Status','<?php echo $status?>',2]<?php echo $forum_track ?>,['_trackPageview']];
(function(d, t) { var g = d.createElement(t), s = d.getElementsByTagName(t)[0];
g.async = 1; g.src = '//www.google-analytics.com/ga.js'; s.parentNode.insertBefore(g, s); }(document, 'script'));
<?php 
// addthis script must be added after ga tracker for google analytics integration, it will be loaded asynchronously
if ($addthis): ?>
(function(d) { var a = d.createElement('script'), h = d.getElementsByTagName('head')[0];
a.async = 1; a.src = '//s7.addthis.com/js/250/addthis_widget.js#domready=1'; h.appendChild(a); }(document));
<?php endif ?></script>
