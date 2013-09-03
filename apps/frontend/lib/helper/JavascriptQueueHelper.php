<?php

use_helper('Javascript');

/*
 * See web/static/js/queue.js
 * The helper wraps the js code so that it becomes the content
 * of a function that will be executed after protoculous/jquery is loaded
 *
 * You can safely refer C2C global var, but you must not refer _q
 */
function javascript_queue($js) {
  echo javascript_tag('(function(C2C, _q) { _q.push(function() {' . $js .
      '}); })(window.C2C = window.C2C || {}, window.C2C._q = window.C2C._q || [])');
}
