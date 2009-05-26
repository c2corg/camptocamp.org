<?php
use_helper('Form', 'Viewer'); 

echo display_title(__('mailing lists'));
?>

<div id="wrapper_context" style="padding-left:20px">
<div id="ombre_haut">
    <div id="ombre_haut_corner_right"></div>
    <div id="ombre_haut_corner_left"></div>
</div>

<div id="content_article">
    <div id="article">
        <div id="mailinglists">
            <p><?php echo __('mailing list explanation %1% %2%',
                            array('%1%' => $email, '%2%' => sfConfig::get('mod_users_ml_owner')))?></p>

            <p><?php echo __('snow lists explanation') ?></p>

            <ul class="action_buttons">
            <?php foreach ($lists as $list => $status): ?>
            <li>
            <?php echo form_tag('users/mailinglists'); ?>
            <strong><?php echo __("$list ML title") ?></strong>
            <?php
            echo input_hidden_tag('listname', $list, array('id' => $list.'_name'));
            echo input_hidden_tag('reason', $status ? 'unsub' : 'sub', array('id' => $list.'_reason'));
            echo submit_tag(__($status ? 'Unsubscribe' : 'Subscribe'),
                            array('class' => $status ? 'action_cancel' : 'action_create'));
            ?></form></li>
            <?php endforeach ?>
            </ul>
        </div>
    </div>
</div>

<?php include_partial('common/content_bottom') ?>
