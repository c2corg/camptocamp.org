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
        $message = '<div class="' . $type . '" id="' . $type . '"><div>' . $flash . '</div></div>';

        if (!c2cTools::mobileVersion())
        {
            // show feedback div, highlight it, and then fade it out and remove it
            $js = javascript_tag("Event.observe(window, 'load', function() {
                var div_id = '$type'; var div = $(div_id); if(!div.visible()) { div.show(); }
                new Effect.Highlight(div_id, { afterFinish: function() { new Effect.Fade(div_id, { duration: 1.5, delay: 3}); }});
            });");
        }
        else
        {
            $js = javascript_tag("Event.observe('$type', 'click', function() { this.hide(); });");
        }

        return $message . $js;
    }
}
