<?php use_helper('Form', 'MyForm', 'Link'); ?>

<div id="fake_div">

<?php
if (empty($redirect_param))
{
    $form_action = '@login';
}
else
{
    $form_action = "@login_redirect?redirect=$redirect_param";
}
echo form_tag($form_action, array('id' => 'loginForm')) ?>
<?php include_partial('users/loginFields'); ?>
<p>
  <?php echo c2c_submit_tag(__('Connect')) ?>
  &nbsp;
  <?php echo forgot_link_to() ?>
  &nbsp;
  <?php echo signup_link_to() ?>
</p>
</form>
