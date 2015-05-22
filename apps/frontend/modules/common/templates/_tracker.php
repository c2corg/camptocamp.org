<?php
// google analytics tracker notes
// - we set coockie domain to www.camptocamp.org because analytic would otherwise use *.camptocamp.org
//   we don't want cookies to be sent with s.camptocamp.org
// - dimensions are used for tracking forum pageviews, form factor and user status (connected or not).
//   Since we use universal analytics, the scope is configured on analyrics website 
// - we use IP anonymization, we loose some geographic accuracy, but...

// keep connection status with analytics (dimension1)
$status = $sf_user->isConnected() ? 'Member' : 'Visitor';
// track form factor (dimension3)
$form_factor = $sf_user->getAttribute('form_factor');
// track forum id on viewtopic and viewforum pages (dimension2)
$forum_track = isset($tracker_forum_id) ? ",'dimension2':'$tracker_forum_id'" : '';
?>
<script>
window.ga=function(){ga.q.push(arguments)};ga.q=[];ga.l=+new Date;
ga('create','<?php echo sfConfig::get('app_ganalytics_key') ?>',{'cookieDomain':window.location.host});
ga('send','pageview',{'anonymizeIp':true,'dimension1':'<?php echo $status ?>','dimension3':'<?php echo $form_factor ?>'<?php echo $forum_track ?>});
<?php 
// addthis script must be added after ga tracker for google analytics integration, it will be loaded asynchronously
if (isset($addthis) && $addthis): ?>
(function(d) { var a = d.createElement('script'), h = d.getElementsByTagName('head')[0];
a.async = 1; a.src = '//s7.addthis.com/js/250/addthis_widget.js#domready=1'; h.appendChild(a); }(document));
<?php endif ?></script>
<script src="https://www.google-analytics.com/analytics.js" async defer></script>
