function touchHandler(event)
{
    var touches = event.changedTouches,
        first = touches[0],
        type = "";
    switch(event.type)
    {
        case "touchstart": type = "mousedown"; break;
        case "touchmove":  type = "mousemove"; break;        
        case "touchend":   type = "mouseup";   break;
        default: return;
    }
    var simulatedEvent = document.createEvent("MouseEvent");
    simulatedEvent.initMouseEvent(type, true, true, window, 1,
                              first.screenX, first.screenY,
                              first.clientX, first.clientY, false,
                              false, false, false, 0/*left*/, null);
    first.target.dispatchEvent(simulatedEvent);
    event.preventDefault();
} 

function move_table(value) {
  $$('table.list')[0].style.marginLeft = '-'+value+'px';
}

function init_slider() {
  var slider = $$('.slider')[0];
  var wrapper_size = $('wrapper_context').getWidth()-5; // because of the border, but not really important
  var table_size = $$('table.list')[0].getWidth();

  // first, determine if we need the slider or not
  if (wrapper_size >= table_size) {
    slider.hide();
    return;
  }

  new Control.Slider(slider.down('.handle'), slider, {
    range: $R(0, table_size-wrapper_size),
    sliderValue: 0,
    onSlide: move_table,
    onChange: move_table
  });

  // add specific event handler for iphone, android...
  // see http://rossboucher.com/2008/08/19/iphone-touch-events-in-javascript/
  slider.addEventListener("touchstart", touchHandler, true);
  slider.addEventListener("touchmove", touchHandler, true);
  slider.addEventListener("touchend", touchHandler, true);
  slider.addEventListener("touchcancel", touchHandler, true);
}


//Event.observe(window, 'load', function() {
  init_slider();
//});
