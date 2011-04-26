<?php use_helper('Form', 'MyForm', 'Link'); ?>

<div id="fake_div">

<?php echo form_tag('@login', array('id' => 'loginForm')) ?>
<?php include_partial('users/loginFields'); ?>
<p>
  <?php echo c2c_submit_tag(__('Connect')) ?>
  &nbsp;
  <?php echo forgot_link_to() ?>
  &nbsp;
  <?php echo signup_link_to() ?>
</p>
</form>
