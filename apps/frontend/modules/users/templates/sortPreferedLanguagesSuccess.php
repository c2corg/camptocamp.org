<?php use_helper('Link', 'Language', 'MyForm', 'MyMinify', 'JavascriptQueue'); 

echo '<div id="fake_div">';

echo customization_nav('langpref');

?>

<div id="customize" class="form-row">
<?php echo fieldset_tag('Favorite language:'); ?>
    <ol id="languages-order">
        <?php foreach ($sf_user->getPreferedLanguageList() as $language_code): ?>
          <li id="<?php echo "lang_" . $language_code ?>"><?php echo format_language_c2c($language_code) ?></li>
        <?php endforeach ?>
    </ol>
<?php
    echo end_fieldset_tag();
    echo __('Reorder these languages according to your preferences, using drag-and-drop');

    echo javascript_queue("$.ajax({
  url: '" . minify_get_combined_files_url('/static/js/jquery.sortable.js') . "',
  dataType: 'script',
  cache: true })
.done(function() {
  $('#languages-order').sortable({forcePlaceholderSize: true}).on('sortupdate', function() {
    $('#indicator').show();
    $.post('" . url_for('users/sortPreferedLanguages') . "',
                $('#languages-order li').map(function() { return 'order[]=' + this.id.match(/^lang_(.*)$/)[1]; }).get().join('&'))
      .always(function() { $('#indicator').hide(); })
      .done(function(data) { C2C.showSuccess(data); });
  });
});");
?>
</div>
<!-- end div customize -->
