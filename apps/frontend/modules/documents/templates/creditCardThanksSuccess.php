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

if ($status == 'success'):                         
?>      
<p>
Merci pour votre don ! La transaction est enregistr&eacute;e sous le num&eacute;ro <?php echo $trans_id; ?>. Pour toute question suppl&eacute;mentaire, n'h&eacute;sitez pas &agrave; nous contacter &agrave; <a href="mailto:donation@camptocamp.org">donation@camptocamp.org</a>.
</p>
<p>
Vous pouvez reprendre une activité normale et revenir par exemple &agrave; la <a href="/">page d'accueil</a>.
</p>
<?php
else:
?>
<p>
Le paiement n'a pas abouti. S'il ne s'agit pas d'une annulation intentionnelle de votre part, merci de nous contacter &agrave; <a href="mailto:donation@camptocamp.org">donation@camptocamp.org</a> pour résoudre cet inconv&eacute;nient technique...
</p>
<?php
endif;
echo end_content_tag();
                              
include_partial('common/content_bottom');

