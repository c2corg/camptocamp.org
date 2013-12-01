<?php
use_helper('JavascriptQueue');
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
            $js = javascript_queue("$('#$type').delay(3000).animate({opacity:0}, {duration:1500, complete: function() { $(this).hide(); }});");
        }
        else
        {
            $js = javascript_queue("$('#$type').click(function() { $(this).hide(); });");
        }

        return $message . $js;
    }
}
