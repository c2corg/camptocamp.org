<?php
use_helper('Javascript');
/**
* Display flash message if needed
*/
function display_flash_message($type)
{
    $flash = sfContext::getInstance()->getUser()->getAttribute($type, null, 'symfony/flash');
    
    if ($flash)
    {
        // show feedback div, highlight it, and then fade it out and remove it
        $js = javascript_tag("function feedback(div_id) {
var div = $(div_id); if(!div.visible()){ div.show(); }
new Effect.Highlight(div_id, { afterFinish: function() { new Effect.Fade(div_id, { duration: 1.5, delay: 3}); }});}");
        
        $message = '<div class="' . $type . '" id="' . $type . '"><div>' . $flash . '</div></div>';
        $message .= javascript_onLoad("feedback('$type');");
        return $js . $message;
    }
}

function javascript_onLoad($todo)
{
    return javascript_tag("Event.observe(window, 'load', function() { $todo });");
}
