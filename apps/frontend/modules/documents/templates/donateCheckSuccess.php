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
Libellez votre chèque au nom de "Camptocamp-Association" et envoyez le à l'adresse suivante :<br>
Camptocamp-Association (c/o B. Besson)<br>
9 rue Marguerite Gonnet<br>
38000 GRENOBLE<br>
FRANCE<br>

penser à changer les boutons de gauche pour renvoyer vers de l'aide via email ou autre

<?php
echo end_content_tag();

include_partial('common/content_bottom');
