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
<form action="/donate" method="POST">
  <label>Anonyme <input type="checkbox" required /></label>
  <label>Nom / pseudo <input type="text" <?php echo isset($name) ? 'value="'.$name.'"' : '' ?> required /></label>
  <label>email <input type="email" <?php echo isset($email) ? 'value="'.$email.'"' : '' ?> required /></label>
  <label>montant <input type="number" min=1 <?php echo isset($amount) ? 'value="'.$amount.'"' : '' ?></label>
</form>
Texte qui explique les diff&eacute;rentes options de paiement, &eacute;ventuellement qui rappelle &agrav; quoi &ccedil;a sert etc

penser à changer les boutons de gauche pour renvoyer vers de l'aide via email ou autre

<input type="submit" class="donate-submit" value="Payer avec paypal TODO" name="paypal" />
<input type="submit" class="donate-submit" value="Payer en ligne TODO" name="cc" />
<input type="submit" class="donate-submit" value="Payer par chèque" name="check" />
<input type="submit" class="donate-submit" value="Payer par virement bancaire" name="transfer" />

<?php
echo end_content_tag();

include_partial('common/content_bottom');
