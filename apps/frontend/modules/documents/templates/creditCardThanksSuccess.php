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

if ($status == 'success'):                         
?>      
<p>
Merci pour votre don !
</p>
<p>
penser &agrave; changer les boutons de gauche pour renvoyer vers de l'aide via email ou autre
</p>
<?php
else:
?>
<p>
Le paiement n'a pas aboutit. Merci de nous contacter pour résoudre cet inconv&eacute;nient technique...
</p>
<p>
penser &agrave; changer les boutons de gauche pour renvoyer vers de l'aide via email ou autre
</p>
<?php
endif;
echo end_content_tag();
                              
include_partial('common/content_bottom');

