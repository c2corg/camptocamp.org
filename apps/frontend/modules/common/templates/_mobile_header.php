<?php 
use_helper('Language', 'Link', 'Flash', 'MyForm', 'Javascript', 'Ajax', 'General');
echo ajax_feedback();
$static_base_url = sfConfig::get('app_static_url');
?>

<div id="header">
  <?php
  echo link_to(content_tag('span', '',
                           array('id' => 'banner_logo', 'title' => 'Camptocamp.org')),
               '@homepage');
  ?>
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
  <div id="pub">
    <div class="right">
        <?php include_component('common', 'banner') ?>
    </div>
  </div>
</div>

<?php
echo generate_path();

foreach (array('notice', 'warning', 'error') as $key => $value)
{
    echo display_flash_message($value);
}
