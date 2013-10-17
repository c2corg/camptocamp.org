<?php 
use_helper('MyForm', 'Javascript');

echo group_tag('Username:', 'login_name', 'input_tag', null, array('autofocus' => 'autofocus', 'class' => 'long_input'));
echo group_tag('Password:', 'password', 'input_password_tag', null, array('class' => 'long_input'));
echo group_tag('Remember me ?', 'remember', 'checkbox_tag', true);
if (c2cTools::mobileVersion())
{
    echo input_hidden_tag('referer', '');
    $js = "document.getElementById('referer').value = document.referrer;";
    echo javascript_tag($js);
}
