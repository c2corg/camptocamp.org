<div id="fake_div">
<?php use_helper('Form', 'Validation', 'Ajax', 'Link', 'MyForm');

// handle non ajax error form
echo global_form_errors_tag();

// script => true ... ?
echo c2c_form_remote_tag('users/lostPassword');
?>
<div id="reset_password">

    <?php echo tips_tag('To receive a new password, enter your username or your email') ?>
    <?php echo group_tag('Username or Email', 'loginNameOrEmail', 'input_tag', $sf_params->get('loginNameOrEmail'), array('class' => 'long_input')); ?>
    <?php echo c2c_submit_tag(__('retrieve')) ?>
    &nbsp;
    <?php echo login_link_to() ?>

</div>
</form>
