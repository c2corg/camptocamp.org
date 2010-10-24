<?php 
use_helper('Language', 'Link', 'Forum', 'Flash', 'MyForm', 'Javascript', 'Ajax', 'General');
echo ajax_feedback();
$static_base_url = sfConfig::get('app_static_url');
?>

<div id="header">
  <div id="banner_middle">
    <div id="log">
        <div id="mobile_home">
        <?php echo link_to('<span>&nbsp;</span>', '@homepage'); ?>
        </div>
      <div class="log_elt">
      <?php
      echo select_interface_language(),  ' ';
      if ($sf_user->isConnected() &&
          PunbbMsg::GetUnreadMsg($sf_user->getId()))
      {
          echo f_link_to(picto_tag('action_contact', __('mailbox')),
                    'message_list.php');
      } ?>
      </div>
      <div class="log_elt" id="user_mngt">
      <?php if ($sf_user->isConnected()): ?>
        <strong><?php
        echo link_to($sf_user->getUsername(),
                     '@document_by_id?module=users&id='.$sf_user->getId(),
                     array('id' => 'name_to_use', 'class'=>'logged_as', 'title'=>__('Your are connected as ')));
        ?></strong>
        <?php echo link_to(picto_tag('action_cancel', __('Logout')), '@logout');
           else:
        echo login_link_to();
           endif ?>
        | <?php echo customize_link_to() ?>
      </div>
      <?php
        $perso = c2cPersonalization::getInstance();
        $act_filter = $perso->getActivitiesFilter();
        $main_filter_switch_on = $perso->isMainFilterSwitchOn();
        $static_base_url = sfConfig::get('app_static_url');
        $alist = sfConfig::get('app_activities_list');
        array_shift($alist);
        $light = array(1 => '', 2 => '', 3 => '', 4 => '', 5 => '', 6 => '');
        $activities_class = array();

        if ($main_filter_switch_on && count($act_filter))
        {
            $unselected_act = array_diff(array(1, 2, 3, 4, 5, 6), $act_filter);
            foreach ($unselected_act as $act_id)
            {
                $light[$act_id] = '_light';
            }
            foreach ($act_filter as $act_id)
            {
                $activities_class[] = 'act' . $act_id;
            }
        }
      ?>
      <div class="log_elt" id="quick_switch<?php echo empty($activities_class) ? '' : '" class="' . implode(' ', $activities_class) ?>">
      <?php
        foreach ($alist as $id => $activity)
        {
            $act_id = $id + 1;
            $alt = ($act_filter == array($act_id))
                   ? __('switch_off_activity_personalisation')
                   : __('switch_to_' . $alist[$act_id-1]) ;
            $image_tag = picto_tag('activity_' . $act_id . $light[$act_id], $alt);
            echo link_to($image_tag, '@quick_activity?activity=' . ($act_id), array('class' => 'qck_sw'));
        }

        if ($perso->areFiltersActive())
        {
            $options_on = $options_off = array();
            $options_on['id'] = 'filter_switch_on';
            $options_off['id'] = 'filter_switch_off';

            if ($perso->isMainFilterSwitchOn())
            {
                $options_off['style'] = 'display: none;';
            }
            else
            {
                $options_on['style'] = 'display: none;';
            }

            $html = picto_tag('action_on', __('some filters active'), $options_on);
            $html .= picto_tag('action_off', __('some filters have been defined but are not activated'), $options_off);

            if (defined('PUN_ROOT'))
            {
                // we are in the forum
                // it is not possible to activate/disactivate filter because the FiltersSwitchFilter will not get executed.
                // moreover, forums are not filtered on activities, regions, langs.
                echo $html;
            }
            else
            {
                echo link_to_remote($html,
                              array('update' => '',
                                    'url'    => "/common/switchallfilters", // FIXME: replace by a routing rule.
                                    'loading' => "Element.show('indicator')",
                                    'success' => "Element.toggle('filter_switch_on'); Element.toggle('filter_switch_off'); window.location.reload();"));
            }
        }
    ?>
    </div>
    </div>
  </div>
</div>
<div id="menu">
    <?php include_partial('common/mobile_menu', array('lang' => $sf_user->getCulture(), 'is_connected' => $sf_user->isConnected())); ?>
</div>

<?php
foreach (array('notice', 'warning', 'error') as $key => $value)
{
    echo display_flash_message($value);
}
