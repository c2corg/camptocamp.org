<?php use_helper('Form', 'MyForm', 'Link'); ?>
<div id="fake_div">
<?php
echo form_tag('@login', array('id' => 'loginForm')); 
include_partial('users/loginFields');
if (isset($redirect_param) && !empty($redirect_param))
{
    echo input_hidden_tag('redirect', $redirect_param);
}
?>
<p>
  <?php echo c2c_submit_tag(__('Connect')) ?>
  &nbsp;
  <?php echo forgot_link_to() ?>
  &nbsp;
  <?php echo signup_link_to() ?>
</p>
</form>
