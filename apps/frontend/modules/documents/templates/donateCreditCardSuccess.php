<?php
use_helper('Button', 'Form', 'Viewer', 'MyForm', 'Forum');
$mobile_version = c2cTools::mobileVersion();
if (!$mobile_version):
?>

<div id="nav_space">&nbsp;</div>
<div id="nav_tools">
  <div id="nav_tools_top"></div>
  <div id="nav_tools_content">
    <ul>
      <li><?php echo f_link_to(__('Report problem'),
                               'misc.php?email=' . sfConfig::get('app_donate_user') . '&doc=' . urlencode($_SERVER['REQUEST_URI']),
                               array('title' => __('Report problem'),
                                     'class' => 'action_report nav_edit')); ?></li>
      <li><?php echo button_help('help') ?></li>
    </ul>
  </div>
  <div id="nav_tools_down"></div>
</div>
                                        
<?php
endif;
echo display_content_top('list_content');
echo start_content_tag();
                                               
?>
<p>
Merci de votre soutien pour camptocamp !
</p>
<p>
Pour proc&eacute;der au paiement de <?php echo $amount; ?> <?php echo ($currency == 'CHF' ? 'CHF' : '&euro;'); ?> par carte bancaire en faveur de l'association Camptocamp, cliquez sur le bouton qui s'affiche ci-dessous. Vous serez alors redirig&eacute; vers le site de paiement en ligne. 
</p>
<p>                                                     
penser &agrave; changer les boutons de gauche pour renvoyer vers de l'aide via email ou autre
</p>                     
<?php
echo '<form method="post" action="' . sfConfig::get('app_donate_vads_url') . '">';
foreach ($params as $key => $value)
{
    echo '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
}
?>
  <input type="submit" name="pay" value="Procéder au paiement" />
</form>

<?php
echo end_content_tag();
                                                 
include_partial('common/content_bottom');

