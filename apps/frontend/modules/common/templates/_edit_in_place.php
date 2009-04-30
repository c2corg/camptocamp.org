<?php use_helper('Javascript', 'Form');

if (!empty($message) || $sf_user->hasCredential('moderator')):
    $output_message = empty($message) ? __('No message defined. Click to edit')
                                      : $sf_data->getRaw('message'); // unescaped data : we trust moderators ! 
    ?>
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
