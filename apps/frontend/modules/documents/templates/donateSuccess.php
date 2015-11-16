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
Nous vous demandons de remplir un formulaire très simple. Il a pour objectif de pouvoir vous envoyer des informations régulières sur l'avancement de la campagne de dons et la réalisation des nouvelles fonctionalités de camptocamp. Si vous souhaitez rester anonyme, cochez la case prévue à cet effet, vous n'apparaîtrez pas dans la liste des donateurs.(*)
</p><p>
Enfin, nous vous proposons 4 modes de paiements: chèque (euro uniquement), virement bancaire, carte bancaire et paypal. Pour les paiements par chèque, paypal ou les virements pensez à indiquer la mention 'campagne de dons 2015' et votre nom/pseudo/identifiant afin qu'on identifie facilement l'origine de votre versement.
</p><p>
Merci encore pour votre soutien !
</p>

<form action="/donate" class="donate-form" method="POST">
  <div class="donate-left">
    <label>Nom / pseudo&nbsp;: <input name="name" type="text" <?php echo isset($name) ? 'value="'.$name.'"' : '' ?> required /></label>
    <br><br>
    <label>Email&nbsp;: <input name="email" type="email" <?php echo isset($email) ? 'value="'.$email.'"' : '' ?> required /></label>
    <br><br>
    <label>Montant&nbsp;: <input name="amount" type="number" min=1 <?php echo isset($amount) ? 'value="'.$amount.'"' : '' ?> required /> </label>
    <select name="currency">
      <option value="EUR" selected>&euro;</option>
      <option value="CHF">CHF</option>
    </select>
    <br><br>
    <label>Je souhaite que mon don reste anonyme <input name="anonymous" type="checkbox" /></label>
  </div>
  <div class="donate-right">
    <button type="submit" class="donate-submit" value ="transfer" name="transfer"><span class="fa fa-sign-in" /> Payer par virement bancaire</button>
    <button type="submit" class="donate-submit" value="check" name="check"><span class="fa fa-edit" /> Payer par ch&egrave;que</button>
    <button type="submit" class="donate-submit" value="cc" name="cc"><span class="fa fa-credit-card" /> Payer par carte bancaire</button>
    <button type="submit" class="donate-submit" value="paypal" name="paypal"><span class="fa fa-paypal" />  Payer avec Paypal</button>
  </div>
</form>
<br>
<p>
    <small>(*) Toutes les informations personnelles seront effacées à l'issue des développements de la nouvelle version du site. Si vous souhaitez connaître vos données personnelles en notre possession ou voulez les supprimer, contactez nous à l'adresse suivante : donation@camptocamp.org.</small>
</p>
<?php
echo end_content_tag();

include_partial('common/content_bottom');
