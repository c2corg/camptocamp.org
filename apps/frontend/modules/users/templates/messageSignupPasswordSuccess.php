<?php echo __('Hello %1%', array('%1%' => $login_name)) ?>
<br />
<br />
<?php echo __('Thanks for signing up to Camptocamp.org. Here are your login info.') ?>
<br />
<?php echo __('Username:') . ' ' . $login_name ?>
<br />
<?php echo __('Password:') . ' ' . $password ?>
<br />
<br />
<?php echo __('To activate your account, please login to the website. You will be able to change your password. Inactivated account will be deleted after %1% days.', array('%1%' => sfConfig::get('app_pending_users_lifetime'))) ?>
<br />
<?php echo link_to(__('Login and edit profile'), '@user_edit', 'absolute=true') ?>
<br />
<br />
<?php echo __('The Camptocamp.org team') ?>
<br />
<?php echo link_to('Camptocamp.org', '@homepage', 'absolute=true') ?>
