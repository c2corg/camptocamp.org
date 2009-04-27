<?php
use_helper('MyForm', 'Language', 'Javascript', 'Ajax', 'Link');

echo customization_nav('customize');
echo ajax_feedback(true); // true == inline feedback
                          // FIXME this conflicts with the one already declared (same ids)
?>

<div id="fake_div">

<div id="customize">
<?php
echo tips_tag('if you select nothing / deselect all, no filter is applied');
echo c2c_form_remote_tag('users/savefilters');
$perso = c2cPersonalization::getInstance();
?>
<div id="home_left_content">
    <fieldset>
      <legend><?php echo __('languages_to_display') ?></legend>
      <?php
      echo checkbox_nokey_list('language_filter', $sf_user->getCulturesForDocuments(),
                               $perso->getLanguagesFilter(), false, false);
      ?>
    </fieldset>

    <fieldset>
      <legend><?php echo __('activities_to_display') ?></legend>
      <?php
      $activities_list = sfConfig::get('app_activities_list');
      echo checkbox_list('activities_filter', $activities_list, $perso->getActivitiesFilter());
      ?>
    </fieldset>
</div><div id="home_right_content">
    <fieldset>
      <legend>
      <?php echo __('places_to_display') ?>
      <?php  echo link_to_remote(__('ranges'),
                                 array('update' => 'pref_area_selector',
                                       'url' => '/areas/getmultipleselect?area_type=1&sep_prefs=false&height=200&select_id=places_filter',
                                       'loading' => 'Element.show("indicator")',
                                       'complete' => 'Element.hide("indicator")')) . ' - ' .
                  link_to_remote(__('regions'),
                                 array('update' => 'pref_area_selector',
                                       'url' => '/areas/getmultipleselect?area_type=3&sep_prefs=false&height=200&select_id=places_filter',
                                       'loading' => 'Element.show("indicator")',
                                       'complete' => 'Element.hide("indicator")')) . ' - ' .
                  link_to_remote(__('countries'),
                                 array('update' => 'pref_area_selector',
                                       'url' => '/areas/getmultipleselect?area_type=2&sep_prefs=false&height=200&select_id=places_filter',
                                       'loading' => 'Element.show("indicator")',
                                       'complete' => 'Element.hide("indicator")'));
                    ?>
      </legend>
      <div id="pref_area_selector">
      <?php
      echo select_tag('places_filter', 
                      options_for_select($ranges, $perso->getPlacesFilter()), 
                      array('id' => 'places', 
                            'multiple' => true,
                            'style' => 'width:300px; height:200px;'));
      echo input_hidden_tag('places_filter_type', $area_type);
      ?>
      </div>
      <p class="tips"><?php echo __('unselect dropdown tip') ?></p>
    </fieldset>
</div> 

    <?php echo submit_tag(__('save')) ?>
</form>
</div>
<!-- end div customize -->
