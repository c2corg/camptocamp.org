<?php 
use_helper('Language', 'Link', 'Forum', 'Flash', 'MyForm', 'Javascript', 'Ajax', 'General');
echo ajax_feedback();
foreach (array('notice', 'warning', 'error') as $key => $value)
{
    echo display_flash_message($value);
}
?>

<div id="header">
  <div id="banner_middle">
    <div id="log">
        <div id="mobile_home">
        <?php echo link_to('<span>&nbsp;</span>', '@homepage', array('title' => 'Camptocamp.org')); ?>
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
                       array('id' => 'name_to_use', 'data-user-id' => $sf_user->getId(),
                             'class'=>'logged_as', 'title'=>__('Your are connected as ')));
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
          $alist = sfConfig::get('app_activities_list');
          array_shift($alist);
          $light = array_fill(0, count($alist), '');
          $activities_class = array();

          if ($main_filter_switch_on && count($act_filter))
          {
              $unselected_act = array();
              foreach ($alist as $k => $act)
               {
                  if (!in_array($k+1, $act_filter)) $unselected_act[] = $k;
              }

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
        <div id="quick_switch<?php echo empty($activities_class) ? '' : '" class="' . implode(' ', $activities_class) ?>">
        <?php
          foreach ($alist as $act_id => $activity)
          {
              $alt = ($act_filter == array($act_id))
                     ? __('switch_off_activity_personalisation')
                     : __('switch_to_' . $alist[$act_id]) ;
              $image_tag = picto_tag('activity_' . ($act_id+1) . $light[$act_id], $alt);
              echo link_to($image_tag, '@quick_activity?activity=' . ($act_id+1), array('class' => 'qck_sw'));
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
<?php
$lang = $sf_user->getCulture();
$is_connected = $sf_user->isConnected();
include_partial('common/mobile_menu',
                array('sf_cache_key' => ($is_connected ? 'connected' : 'not_connected') . '_' . $lang,
                      'lang' => $lang,
                      'is_connected' => $is_connected));
?>
</div>
