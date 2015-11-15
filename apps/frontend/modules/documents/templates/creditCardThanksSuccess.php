<?php
use_helper('Button', 'Form', 'Viewer', 'MyForm');
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
Le paiement n'a pas aboutit. S'il ne s'agit pas d'une annulation intentionnelle de votre part, merci de nous contacter pour résoudre cet inconv&eacute;nient technique...
</p>
<p>
penser &agrave; changer les boutons de gauche pour renvoyer vers de l'aide via email ou autre
</p>
<?php
endif;
echo end_content_tag();
                              
include_partial('common/content_bottom');

