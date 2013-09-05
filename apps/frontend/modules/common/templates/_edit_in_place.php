<?php use_helper('JavascriptQueue', 'Form');

// note: we don't use the symfony in_place_editor_tag since it directly returns a javascript tag,
// which we want to pass through JavascriptQueue (+ code si quite simple)

if (!empty($message) || $sf_user->hasCredential('moderator')):
    $output_message = empty($message) ? __('No message defined. Click to edit')
                                      : $sf_data->getRaw('message'); // unescaped data : we trust moderators ! 
    ?>
    <div id="edit_me" class="front_message"><?php echo $output_message; ?></div>
    <?php 
    if ($sf_user->hasCredential('moderator'))
    {
        echo javascript_queue("
Object.extend(Ajax.InPlaceEditor.prototype, {
  onLoadedExternalText: function(transport) {
    Element.removeClassName(this.form, this.options.loadingClassName);
    this.editField.disabled = false;
    this.editField.value = transport.responseText;
    Field.scrollFreeActivate(this.editField);
  }
});
new Ajax.InPlaceEditor('edit_me', '" . url_for('@default?module=common&action=edit&lang='.$sf_user->getCulture()) . "', {
  cancelText: '" . __('Cancel') . "',
  okText: '" . __('Update') . "',
  savingText: '" . __('saving...') . "',
  cols: 40,
  rows: 2,
  highlightEndColor: '#ffc'
});
");

/*        echo input_in_place_editor_tag('edit_me', 'common/edit?lang=' . $sf_user->getCulture(), array(
                'cols'              => 40,
                'rows'              => 2,
                'highlightendcolor' => '#ffffcc',
                'cancel_text'       => __('Cancel'),
                'save_text'         => __('Update')
        ));*/
    }
endif;
?>
