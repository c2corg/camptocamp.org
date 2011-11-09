<?php
use_helper('Button', 'Form', 'Viewer', 'MyForm'); 

echo display_title(__('mailing lists'));
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
        <div id="mailinglists">
            <p><?php echo __('mailing list explanation %1% %2%',
                            array('%1%' => $email, '%2%' => sfConfig::get('mod_users_ml_owner')))?></p>

            <p><?php echo __('snow lists explanation') ?></p>

            <h3><?php echo __('Lists you have subscribed to') ?></h3>

            <?php if (!count($subscribed_lists)) :
                echo '<p>' . __('You have not subscribed to any list') . '</p>';
            else :?>
            <?php $table_list_even_odd = 0; ?>
            <table class="list">
            <thead>
            <tr><th><?php echo __('Snow list') ?></th><th></th></tr>
            </thead>
            <?php foreach ($subscribed_lists as $list): ?>
            <tr class="<?php echo ($table_list_even_odd++ % 2 == 0) ? 'table_list_even' : 'table_list_odd' ?>">
                <td><?php echo __("$list ML title") ?></td>
                <td>
            <?php
            echo form_tag('users/mailinglists');
            echo input_hidden_tag('listname', $list, array('id' => $list.'_name'));
            echo input_hidden_tag('reason', 'unsub', array('id' => $list.'_reason'));
            echo c2c_submit_tag(__('Unsubscribe'), array('picto' => 'action_cancel'));
            ?></form></td>
            </tr>
            <?php endforeach ?>
            </table>
            <?php endif ?>

            <h3><?php echo __('Available lists') ?></h3>
            <?php if (!count($available_lists)) :
                echo '<p>' . __('There is no available list') . '</p>';
            else :?>
            <?php $table_list_even_odd = 0; ?>
            <table class="list">
            <thead>
            <tr><th><?php echo __('Snow list') ?></th><th></th></tr>
            </thead>
            <?php foreach ($available_lists as $list): ?>
            <tr  class="<?php echo ($table_list_even_odd++ % 2 == 0) ? 'table_list_even' : 'table_list_odd' ?>">
            <td>
            <?php echo __("$list ML title") ?>
            </td>
            <td>
            <?php
            echo form_tag('users/mailinglists');
            echo input_hidden_tag('listname', $list, array('id' => $list.'_name'));
            echo input_hidden_tag('reason', 'sub', array('id' => $list.'_reason'));
            echo c2c_submit_tag(__('Subscribe'), array('picto' => 'action_create'));
            ?></form></td></tr>
            <?php endforeach ?>
            </table>
            <?php endif ?>
        </div>

<?php
echo end_content_tag();

include_partial('common/content_bottom') ?>
