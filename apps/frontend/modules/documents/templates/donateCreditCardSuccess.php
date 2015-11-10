<?php
use_helper('Button', 'Form', 'Viewer', 'MyForm');
?>

<div id="nav_space">&nbsp;</div>
<div id="nav_tools">
  <div id="nav_tools_top"></div>
  <div id="nav_tools_content">
    <ul>
      <li><?php echo button_report() ?></li>
      <li><?php echo button_help('help') ?></li>
    </ul>
  </div>
  <div id="nav_tools_down"></div>
</div>
                                        
<?php
echo display_content_top('list_content');
echo start_content_tag();
                                               
?>
Texte qui explique  que redirigÃ vers vers site banque pour un montant de XX
                                                        
penser ï¿½ changer les boutons de gauche pour renvoyer vers de l'aide via email ou autre
                        
<?php
echo '<form method="post" action="' . sfConfig::get('app_donate_vads_url') . '">';
foreach ($params as $key => $value)
{
    echo '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
}
?>
  <input type="submit" name="pay" value="ProcÃ©der au paiement" />
</form>

<?php
echo end_content_tag();
                                                 
include_partial('common/content_bottom');

