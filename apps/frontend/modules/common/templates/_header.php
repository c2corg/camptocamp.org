<?php 
use_helper('Language', 'Link', 'Flash', 'MyForm', 'Javascript', 'Ajax');
echo ajax_feedback();
$static_base_url = sfConfig::get('app_static_url');
?>

<div id="header">
  <div id="banner_logo">
  <?php
  echo link_to(image_tag($static_base_url . '/static/images/logo.png',
                         array('alt' => 'Camptocamp.org')),
               '@homepage');
  ?>
  </div>
  <div id="banner_middle">
    <div id="log">
      <div class="context_log_padding">
      <?php echo __('Interface language:') . '&nbsp;' . select_interface_language() ?>
      </div>
      <?php include_partial('common/search_form') ?>
      <div id="float_left"><div class="context_log_padding">
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
        $image_on = image_tag($static_base_url . '/static/images/picto/bulb.gif',
                              array('alt' => '(ON)',
                                    'title' => __('some filters active'),
                                    'id' => 'filter_indicator'));
    
        $image_off = image_tag($static_base_url . '/static/images/ie/light.gif',
                               array('alt' => '(OFF)',
                                     'title' => __('some filters have been defined but are not activated'),
                                     'id' => 'filter_indicator'));
    
        if ($perso->isMainFilterSwitchOn())
        {
            $html = '<div id="filter_switch_on">';
            $html .= $image_on;
            $html .= '</div><div id="filter_switch_off" style="display: none;">';
            $html .= $image_off;
        }
        else
        {
            $html = '<div id="filter_switch_on" style="display: none;">';
            $html .= $image_on;
            $html .= '</div><div id="filter_switch_off">';
            $html .= $image_off;
        }
        $html .= '</div>';
        
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
      </div></div>
    </div>
  </div>
  <div id="pub">
    <div align="right">
        <?php include_component('common', 'banner') ?>
    </div>
  </div>
</div>

<div id="menu">
    <div id="menu_border_left">&nbsp;</div>
    <?php include_partial('common/menu', array('sf_cache_key' => $sf_user->getCulture() . '-' . (int)$sf_user->isConnected())); ?>
    <div id="menu_border_right">&nbsp;</div>
</div>

<?php
echo generate_path();

foreach (array('notice', 'warning', 'error') as $key => $value)
{
    echo display_flash_message($value);
}
