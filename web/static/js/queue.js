// execute all javascript that has been pushed in C2C.q_
//
// this is used when we have pieces of javascript that is easier to put into
// the templates as inline script but that we want to be executed once protoculous/jquery is loaded
//
// use the JavascriptQueueHelper for easy integration within symfony

(function(queue) {

  for (var i=0; i<queue.length; i++) {

    // check that we have here some code to be executed
    if (typeof queue[i] !== "function") continue;

    // execute function
    queue[i]();

  }

})(window.C2C && window.C2C._q || []);
