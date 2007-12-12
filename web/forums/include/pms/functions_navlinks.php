<?php
	require PUN_ROOT.'lang/'.$pun_user['language'].'/pms.php';

	if($pun_config['o_pms_enabled'] && $pun_user['g_pm'] == 1)
		$links[] = '<li id="navpm"><a href="message_list.php">'.$lang_pms['Messages'].'</a>';	
?>