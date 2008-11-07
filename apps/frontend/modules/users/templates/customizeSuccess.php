<?php
use_helper('MyForm', 'Language', 'Javascript', 'Ajax', 'Link');

echo customization_nav('customize');
echo ajax_feedback(true); // true == inline feedback

?>

<div id="fake_div">

<div id="customize">
<?php echo tips_tag('if you select nothing / deselect all, no filter is applied'); ?>
<?php echo c2c_form_remote_tag('users/savefilters') ?>
<div id="home_left_content">
    <fieldset>
      <legend><?php echo __('languages_to_display') ?></legend>
      <?php
        $filtered_languages = c2cPersonalization::getLanguagesFilterCookie();
        echo checkbox_nokey_list('language_filter', $sf_user->getCulturesForDocuments(), $filtered_languages);
      ?>
    </fieldset>

    <fieldset>
      <legend><?php echo __('activities_to_display') ?></legend>
      <?php
        $filtered_activities = c2cPersonalization::getActivitiesFilterCookie();
        $activities_list = array_map('__', sfConfig::get('app_activities_list'));
        echo checkbox_list('activities_filter', $activities_list, $filtered_activities);
      ?>
    </fieldset>
</div><div id="home_right_content">
    <fieldset>
      <legend><?php echo __('places_to_display') ?></legend>
      <?php
        $filtered_places = c2cPersonalization::getPlacesFilter();

        echo select_tag('places_filter', 
                        options_for_select($ranges, $filtered_places), 
                        array('id' => 'places', 
                              'multiple' => true,
                              'style' => 'width:300px; height:200px;'));
      ?>  
      <p class="tips"><?php echo __('unselect dropdown tip') ?></p>
    </fieldset>
</div> 

    <?php echo submit_tag(__('save')) ?>
</form>
</div>
<!-- end div customize -->
