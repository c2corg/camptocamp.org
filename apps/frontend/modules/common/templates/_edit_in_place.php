<?php use_helper('Javascript', 'Form');
if ((!$message || empty($message)) && $sf_user->hasCredential('moderator'))
{
    $default_message = __('No message defined. Click to edit');
}
if ($message || $sf_user->hasCredential('moderator')):
    if (!$message && $sf_user->hasCredential('moderator'))
    {
        $output_message = $default_message;
    }
    else
    {
        $output_message = $sf_data->getRaw('message'); // unescaped data : we trust moderators !
    } ?>
    <div id="edit_me" class="front_message"><?php echo $output_message; ?></div>
    <?php 
    if ($sf_user->hasCredential('moderator'))
    {
        echo input_in_place_editor_tag('edit_me', 'common/edit?lang=' . $sf_user->getCulture(), array(
                'cols'              => 40,
                'rows'              => 2,
                'highlightendcolor' => '#ffffcc',
                'cancel_text'       => __('Cancel'),
                'save_text'         => __('Update')
        ));
    }
endif;
?>
