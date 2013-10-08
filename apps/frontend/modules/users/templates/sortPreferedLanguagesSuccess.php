<?php use_helper('Link', 'Language', 'MyForm', 'Ajax'); 

echo '<div id="fake_div">';

echo customization_nav('langpref');

// handle ajax errors
echo ajax_feedback(true); // true == inline feedback ?>

<div id="customize" class="form-row">
<?php echo fieldset_tag('Favorite language:'); ?>
    <ol id="order">
        <?php foreach ($sf_user->getPreferedLanguageList() as $language_code): ?>
          <li id="<?php echo "lang_" . $language_code ?>"><?php echo format_language_c2c($language_code) ?></li>
        <?php endforeach ?>
    </ol>
<?php
    $div_to_update = sfConfig::get('app_ajax_feedback_div_name_success');
    echo sortable_element('order', array(
        'url'    => 'users/sortPreferedLanguages',
        'loading' => "jQuery('#indicator').show()",
        'update' => $div_to_update,
        'complete' => "jQuery('#indicator').hide(); C2C.showSuccess()"));
    echo end_fieldset_tag();
    echo __('Reorder these languages according to your preferences, using drag-and-drop');
?>
</div>
<!-- end div customize -->
