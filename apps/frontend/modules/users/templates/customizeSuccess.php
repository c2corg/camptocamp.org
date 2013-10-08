<?php
use_helper('MyForm', 'Language', 'Javascript', 'Ajax', 'Link');

echo customization_nav('customize');
?>

<div id="fake_div">

<div id="customize">
<?php
echo c2c_form_remote_tag('users/savefilters');
$perso = c2cPersonalization::getInstance();
?>
<div id="home_left_content">
<?php
echo tips_tag('if you select nothing / deselect all, no filter is applied');
?>
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
      echo checkbox_list('activities_filter', $activities_list, $perso->getActivitiesFilter(), true, true, 'checkbox_list', false, 'activity');
      ?>
    </fieldset>
</div><div id="home_right_content">
    <fieldset>
      <legend><?php echo __('places_to_display') ?></legend>
      <p class="select_title">
      <?php  echo link_to_function(__('ranges'), "jQuery('#indicator').show(); 
                    jQuery.ajax('" . url_for('/areas/getmultipleselect?area_type=1&sep_prefs=false&width=300&height=338&select_name=places_filter&select_id=places_filter') . "')
                      .always(function() { jQuery('#indicator').hide(); })
                      .done(function(data) { jQuery('#pref_area_selector').html(data); })") . ' - ' .
                  link_to_function(__('regions'), "jQuery('#indicator').show();
                    jQuery.ajax('" . url_for('/areas/getmultipleselect?area_type=3&sep_prefs=false&width=300&height=338&select_name=places_filter&select_id=places_filter') . "')
                      .always(function() { jQuery('#indicator').hide(); })
                      .done(function(data) { jQuery('#pref_area_selector').html(data); })") . ' - ' .
                  link_to_function(__('countries'), "jQuery('#indicator').show();
                    jQuery.ajax('" . url_for('/areas/getmultipleselect?area_type=2&sep_prefs=false&width=300&height=338&select_name=places_filter&select_id=places_filter') . "')
                      .always(function() { jQuery('#indicator').hide(); })
                      .done(function(data) { jQuery('#pref_area_selector').html(data); })");
      ?>
      </p>
      <div id="pref_area_selector">
      <?php
      //rq FIXME bug in symfony 1.0.11 which does not work with optgroups and escaping, see http://trac.symfony-project.org/ticket/3923
      echo select_tag('places_filter', 
                      options_for_select($sf_data->getRaw('ranges'), $perso->getPlacesFilter()), 
                      array('id' => 'places', 
                            'multiple' => true,
                            'style' => 'width:300px; height:338px;'));
      echo input_hidden_tag('places_filter_type', $area_type);
      ?>
      </div>
      <p class="tips"><?php echo __('unselect dropdown tip') ?></p>
    </fieldset>
</div> 

    <?php echo c2c_submit_tag(__('save'), array('picto' => 'action_create')) ?>
</form>
</div>
<!-- end div customize -->
