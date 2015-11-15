<?php
use_helper('Button', 'Form', 'Viewer', 'MyForm', 'Forum');
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
echo display_content_top('list_content');
echo start_content_tag();

?>

<p>
Texte qui explique les diff&eacute;rentes options de paiement, &eacute;ventuellement qui rappelle &agrave; quoi &ccedil;a sert etc
</p>
<p>
penser � changer les boutons de gauche pour renvoyer vers de l'aide via email ou autre
</p>

<form action="/donate" class="donate-form" method="POST">
  <div class="donate-left">
    <label>Nom / pseudo&nbsp;: <input name="name" ctype="text" <?php echo isset($name) ? 'value="'.$name.'"' : '' ?> required /></label>
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
<?php
echo end_content_tag();

include_partial('common/content_bottom');
