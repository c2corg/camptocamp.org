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
Merci pour votre soutien !<br>
Coordonnées bancaires de camptocamp-association :<br>
CREDIT COOPERATIF GRENOBLE<br>
Domiciliation : CREDITCOOP Grenoble<br>
RIB : 42559 00016 21029256603 07<br>
IBAN : FR76 4255 9000 1621 0292 5660 307<br>
BIC : CCOPFRPPXXX<br>

<br>
penser à changer les boutons de gauche pour renvoyer vers de l'aide via email ou autre

<?php
echo end_content_tag();

include_partial('common/content_bottom');
