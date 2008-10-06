<?php use_helper('Javascript', 'Form');

if ($message): ?>
    <div id="edit_me" class="front_message"><?php echo $sf_data->getRaw('message'); // unescaped data : we trust moderators ! ?></div>
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
