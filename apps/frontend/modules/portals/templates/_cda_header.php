<?php 
use_helper('Language', 'Link', 'Flash', 'MyForm', 'Javascript', 'Ajax', 'General');
echo ajax_feedback();
$cda_config = sfConfig::get('app_portals_cda');
?>

<div id="header">
  <div id="cda_logo">&nbsp;</div>
  <div id="banner_title">
    <h1><?php echo __('changerdapproche') ?></h1>
    <p id="cda_title"><a href="http://changerdapproche.org/">changer<strong>dapproche.org</strong></a></p>
    <p id="cda_sub_title"><?php echo __('cda sub title') ?></p>
  </div>
  <div id="c2c_mw_logos">
    <a href="http://www.mountainwilderness.fr/"><span id="mw_logo" title="Mountain Wilderness"></span></a>
    <?php
  echo link_to(content_tag('span', '',
                           array('id' => 'banner_logo', 'title' => 'Camptocamp.org')),
               '@homepage');
    ?>
  </div>
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
        echo filters_switcher_link($perso->isMainFilterSwitchOn());
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
