// Fix for iPhone viewport scale bug
// adapted from
// http://www.blog.highub.com/mobile-2/a-fix-for-iphone-viewport-scale-bug/
// https://github.com/h5bp/mobile-boilerplate/blob/master/js/helper.js

(function(){
if (/iPhone|iPad|iPod/.test(navigator.userAgent) && !/Opera Mini/.test(navigator.userAgent)) {
  var viewport = $$('meta[name="viewport"]')[0];
  viewport.content = "width=device-width, minimum-scale=1.0, maximum-scale=1.0";
  document.addEventListener("gesturestart", function () {
    viewport.content = "width=device-width, minimum-scale=0.25, maximum-scale=1.6";
  }, false);
}
})();
