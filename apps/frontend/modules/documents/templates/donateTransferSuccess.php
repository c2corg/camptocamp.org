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
Texte qui explique comment faire un virement (IBAN,...) Quide de chf/eur ?

penser à changer les boutons de gauche pour renvoyer vers de l'aide via email ou autre

<?php
echo end_content_tag();

include_partial('common/content_bottom');
