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
Texte qui explique que ca va renvoyer vers paypal

<form action="https://www.paypal.com/fr/cgi-bin/webscr" method="post">
<input name="cmd" value="_xclick" type="hidden" />
<input name="business" value="registration@camptocamp.org" type="hidden" />
<input name="currency_code" value="<?php echo $currency ?>" type="hidden" />
<input name="amount" value="<?php echo $amount ?>" type="hidden" />
<input type="hidden" name="item_name" value="Soutenir Camptocamp Association" />
<input type="hidden" name="return" value="http://camptocamp.org/" />
<input value="Payer avec Paypal" id="paypal-button" type="submit" />
</form>

penser à changer les boutons de gauche pour renvoyer vers de l'aide via email ou autre

<?php
echo end_content_tag();

include_partial('common/content_bottom');
