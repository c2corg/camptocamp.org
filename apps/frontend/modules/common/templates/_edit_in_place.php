<?php use_helper('Javascript', 'Form');

if (!empty($message) || $sf_user->hasCredential('moderator')):
    $output_message = empty($message) ? __('No message defined. Click to edit')
                                      : $sf_data->getRaw('message'); // unescaped data : we trust moderators ! 
    ?>
    <div id="edit_me" class="front_message"><?php echo $output_message; ?></div>
    <?php 
    if ($sf_user->hasCredential('moderator'))
    {
        echo javascript_tag("Object.extend(Ajax.InPlaceEditor.prototype, {
    onLoadedExternalText: function(transport) {
        Element.removeClassName(this.form, this.options.loadingClassName);
        this.editField.disabled = false;
        this.editField.value = transport.responseText;
        Field.scrollFreeActivate(this.editField);
    }
});

Object.extend(Ajax.InPlaceEditor.prototype, {
    getText: function() {
        return (this.element.innerHTML).replace(/<br>/, '<br />');
    }
});
");
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
