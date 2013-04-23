<?php echo __('Hello %1%', array('%1%' => $login_name)) ?>
<br />
<br />
<?php echo __('A new password has been asked for your account.') ?>
<br />
<br />
<?php echo __('Username:') . ' ' . $login_name ?>
<br />
<?php echo __('Password:') . ' ' . $password ?>
<br />
<br />
<?php echo __('Ignore this mail if you have not asked for a new password.') ?>
<br />
<br />
<?php echo __('Thanks for using Camptocamp.org!') ?>
<br />
<?php echo link_to('Camptocamp.org', '@homepage', 'absolute=true') ?>
