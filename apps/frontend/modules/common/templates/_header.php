<?php 
use_helper('Language', 'Link', 'Flash', 'MyForm', 'Javascript', 'Ajax', 'General');
echo ajax_feedback();
?>
<div id="header">
  <?php
  echo link_to(content_tag('span', '',
                           array('id' => 'banner_logo', 'title' => 'Camptocamp.org')),
               '@homepage'); ?>
  <div id="banner_middle">
    <div id="log">
      <div class="log_elt">
      <?php echo __('Interface language:') . ' ' . select_interface_language() ?>
      </div>
      <?php include_partial('common/search_form') ?>
      <div class="log_elt">
      <?php if ($sf_user->isConnected()): ?>
        <?php include_partial('users/logged_in'); ?>
      <?php else: ?>
        <?php echo login_link_to() ?>
        | <?php echo signup_link_to() ?>
      <?php endif ?>
        | <?php echo customize_link_to() ?>
      <?php
    $perso = c2cPersonalization::getInstance();
    if ($perso->areFiltersActive())
    {
        echo filters_switcher_link($perso->isMainFilterSwitchOn());
    }
    ?>
      </div>
    </div>
  </div>
  <div id="pub">
    <div class="right">
        <?php include_component('common', 'banner') ?>
    </div>
  </div>
</div>

<div id="menu">
    <?php include_partial('common/menu', array('lang' => $sf_user->getCulture(), 'is_connected' => $sf_user->isConnected())); ?>
    <div>&nbsp;</div>
</div>

<?php
echo generate_path();
// display flash message if present
foreach (array('notice', 'warning', 'error') as $key => $value)
{
    echo display_flash_message($value);
}
