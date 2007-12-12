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
        $js = javascript_tag("
        function feedback(div_id)
        {
            var div = $(div_id);

            // is div already visible ?
            if(!div.visible())
            {
                // if not, show it
                div.show();
            }

            // highlight the div
            new Effect.Highlight(div_id, 
                {
                    // when highlight is finished, remove div
                    afterFinish: function()
                    {
                        new Effect.Fade(div_id, { duration: 1.5, delay: 3});
                    }
                }
            );
        }");
        
        $message = '<div class="' . $type . '" id="' . $type . '"><div>' . $flash . '</div></div>';
        $message .= javascript_onLoad("feedback('$type');");
        return $js . $message;
    }
}

function javascript_onLoad($todo)
{
    return javascript_tag("
        Event.observe(window, 'load', function() {
          $todo
        });"
    );
}