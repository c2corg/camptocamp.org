<script>
<?php
/* In this section we put code for immediately hiding some sections according
 * to preferences stored in fold cookie
 *
 * The sections are hidden (or not) right after they are created in the DOM in
 * order to prevent visual glitches. Thus, the javascript has to be ready from
 * the beginning. As the file is kept very small (less than 1kb) it is ok to inline
 * it, avoiding an extra request.
 *
 * Following js code is the minified version of the web/static/fold_init*.js files
 * We have different vesions depending on the different use cases. Basically, the
 * following sections can be concerned:
 * - left navigation on standard version (every page)
 * - sections on home and portals
 * - map section (n that cases, some additional code is added by the map partials)
 */

$mobile = c2cTools::mobileVersion();
$module = $sf_context->getModuleName();
$action = $sf_context->getActionName();

if ($mobile):
  if ($action == 'home' || ($module == 'portals' && $action == 'view')):
  // fold_init_home_mobile.js ~ 490b
?>
(function(e,t){e.shouldHide=function(e,n){var o=/fold=([tfx]{20});/.exec(t.cookie)
if(o)switch(o[1].charAt(e)){case"t":return!1
case"f":return!0}return!n},e.setSectionStatus=function(n,o,i){if(e.shouldHide(o,i)){if("map_container"==n)return!0
var a=open_close[0]
t.getElementById(n+"_section_container").style.display="none",t.getElementById(n+"_toggle").title=a
var s=t.getElementById(n).getElementsByClassName("nav_box_top")
s&&(s.classNam+=" small")}}})(window.C2C=window.C2C||{},document)
<?php
  else:
  // fold init_default_mobile.js ~280b
?>
(function(e,t){e.shouldHide=function(e,n){var o=/fold=([tfx]{20});/.exec(t.cookie)
if(o)switch(o[1].charAt(e)){case"t":return!1
case"f":return!0}return!n},e.setSectionStatus=function(t,n,o){return e.shouldHide(n,o)?"map_container"==t:void 0}})(window.C2C=window.C2C||{},document)
<?php
  endif;
else:
  if ($action == 'home' || ($module == 'portals' && $action == 'view')):
  // fold_init_home.js ~710b
?>
(function(e,t){e.shouldHide=function(e,n){var o=/fold=([tfx]{20});/.exec(t.cookie)
if(o)switch(o[1].charAt(e)){case"t":return!1
case"f":return!0}return!n},e.setSectionStatus=function(n,o,i){if(e.shouldHide(o,i))if("nav"==n){if(/MSIE [67].0/.exec(navigator.userAgent))return
t.getElementById("wrapper_context").className+=" no_nav"
for(var a=t.getElementsByClassName("nav_box"),s=0;s<a.length;s++)a[s].style.display="none"}else{if("map_container"==n)return!0
var c=open_close[0]
t.getElementById(n+"_section_container").style.display="none",t.getElementById(n+"_toggle").title=c
var r=t.getElementById(n).getElementsByClassName("nav_box_top")
r&&(r.classNam+=" small")}}})(window.C2C=window.C2C||{},document)
<?php
  else:
  // fold_init_default.js ~410b
?>
(function(e,t){e.shouldHide=function(e,n){var o=/fold=([tfx]{20});/.exec(t.cookie)
if(o)switch(o[1].charAt(e)){case"t":return!1
case"f":return!0}return!n},e.setSectionStatus=function(n,o,i){if(e.shouldHide(o,i))if("nav"==n){if(/MSIE [67].0/.exec(navigator.userAgent))return
var a=t.getElementById("content_box")
a&&(a.className+=" wide")}else if("map_container"==n)return!0}})(window.C2C=window.C2C||{},document)
<?php
  endif;
endif;
?>
</script>
