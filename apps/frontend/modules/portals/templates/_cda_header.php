<?php 
use_helper('Language', 'Link', 'Flash', 'MyForm', 'Javascript', 'Ajax', 'General');
echo ajax_feedback();
$static_base_url = sfConfig::get('app_static_url');
?>

<div id="header">
    <div id="cda_logo">&nbsp;</div>
    <div id="banner_title">
        <h1><?php echo __('changerdapproche') ?></h1>
        <p id="cda_title">changer<strong>dapproche.org</strong></p>
        <p id="cda_sub_title"><?php echo __('cda sub title') ?></p>
    </div>
    <a href="http://www.mountainwilderness.fr/"><span id="mw_logo" title="Mountain Wilderness"></span></a>
    <?php
  echo link_to(content_tag('span', '',
                           array('id' => 'banner_logo', 'title' => 'Camptocamp.org')),
               '@homepage');
    ?>
  <div id="banner_middle">
    <div id="log">
      <div class="log_elt">
      <?php if ($sf_user->isConnected()): ?>
        <?php include_partial('users/logged_in'); ?>
      <?php else: ?>
        <?php echo login_link_to() ?>
      <?php endif ?>
        | <?php echo customize_link_to() ?>
      <?php
    $perso = c2cPersonalization::getInstance();
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
        
        echo link_to_remote($html, 
                      array('update' => '', 
                            'url'    => "/common/switchallfilters", // FIXME: replace by a routing rule.
                            'loading' => "Element.show('indicator')",
                            'success' => "Element.toggle('filter_switch_on'); Element.toggle('filter_switch_off'); window.location.reload();"));
    }
    ?>
       | <?php echo select_interface_language() ?>
      </div>
    </div>
  </div>
</div>

<?php

foreach (array('notice', 'warning', 'error') as $key => $value)
{
    echo display_flash_message($value);
}
