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
Merci pour votre soutien !<br>
Libellez votre chèque au nom de "Camptocamp-Association" et envoyez le à l'adresse suivante :<br>
Camptocamp-Association (c/o B. Besson)<br>
9 rue Marguerite Gonnet<br>
38000 GRENOBLE<br>
FRANCE<br>

Mentionnez le num&eacute;ro de transaction <?php echo $uid ?><br>
<?php
echo end_content_tag();

include_partial('common/content_bottom');
