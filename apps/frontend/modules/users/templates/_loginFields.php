<?php 
use_helper('MyForm');

echo group_tag('Username:', 'login_name', 'input_tag', null, array('class' => 'long_input'));
echo group_tag('Password:', 'password', 'input_password_tag', null, array('class' => 'long_input'));
echo group_tag('Remember me ?', 'remember', 'checkbox_tag');
?>
