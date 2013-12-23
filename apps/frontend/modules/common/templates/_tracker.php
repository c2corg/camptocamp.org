<?php
// google analytics tracker notes
// - we set coockie domain to www.camptocamp.org / m.camptocamp.org because analytic would otherwise use *.camptocamp.org
//   we don't want cookies to be sent with s.camptocamp.org
// - dimensions are used for tracking forum pageviews, and user status (connected or not). Since we use universal analytics,
//   the scope is configured on analyrics website 
// - we use IP anonymization, we loose some geographic accuracy, but...

$k = c2cTools::mobileVersion() ? sfConfig::get('app_mobile_ganalytics_key') : sfConfig::get('app_ganalytics_key');
// keep connection status with analytics
$status = $sf_user->isConnected() ? 'Member' : 'Visitor';
// track forum id on viewtopic and viewforum pages
$forum_track = isset($tracker_forum_id) ? ",'dimension2':'$tracker_forum_id'" : '';
?>
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create','<?php echo $k ?>',{'cookieDomain':window.location.host});
ga('send','pageview',{'anonymizeIp':true,'dimension1':'<?php echo $status?>'<?php echo $forum_track ?>});
<?php 
// addthis script must be added after ga tracker for google analytics integration, it will be loaded asynchronously
if (isset($addthis) && $addthis): ?>
(function(d) { var a = d.createElement('script'), h = d.getElementsByTagName('head')[0];
a.async = 1; a.src = '//s7.addthis.com/js/250/addthis_widget.js#domready=1'; h.appendChild(a); }(document));
<?php endif ?></script>
