<?php
use_helper('JavascriptQueue', 'Form', 'MyMinify');

if (!empty($message) || $sf_user->hasCredential('moderator'))
{
    $output_message = empty($message) ? __('No message defined. Click to edit')
                                      : $sf_data->getRaw('message'); // unescaped data : we trust moderators ! 
    ?>
    <div id="edit_me" class="front_message"><?php echo $output_message; ?></div>
    <?php 
    if ($sf_user->hasCredential('moderator'))
    {
        echo javascript_queue("
          $.ajax({
            url: '" . minify_get_combined_files_url('/static/js/$.jeditable.js') . "',
            dataType: 'script',
            cache: true})
          .done(function() {
            $('#edit_me')
              .addClass('editable')
              .editable('"  . url_for('@default?module=common&action=edit&lang='.$sf_user->getCulture()) . "', {
                type: 'textarea',
                submit: '<input type=\"submit\" value=\"" . __('Update') . "\" />',
                cancel: '<input type=\"submit\" value=\"" . __('Cancel') . "\" />',
                indicator: '" . __('saving...') . "',
                tooltip: '" . __('Click to edit') . "',
                onblur: 'ignore',
                rows: 4
              });
          });");
    }
}
?>
