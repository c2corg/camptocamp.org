<?php
echo '('.f_link_to($UnreadMsg, 'message_list.php').')';

if ($UnreadMsg && !c2cTools::mobileVersion())
{
    use_helper('JavascriptQueue');
    echo javascript_queue("$.ajax({ url: '" . minify_get_combined_files_url('/static/js/tinycon.min.js') . "', dataType: 'script', cache: true })
      .done(function() { Tinycon.setBubble($UnreadMsg); });");
}
?>
