<?php
	require PUN_ROOT.'lang/'.$pun_user['language'].'/pms.php';

	if($pun_config['o_pms_enabled'] && !$pun_user['is_guest'] && $pun_user['g_pm'] == 1)
	{
		$pid = isset($cur_post['poster_id']) ? $cur_post['poster_id'] : $cur_post['id'];
		$user_contacts[] = '<a href="message_send.php?id='.$pid.'&tid='.$id.'">'.$lang_pms['PM'].'</a>';
	}
?>