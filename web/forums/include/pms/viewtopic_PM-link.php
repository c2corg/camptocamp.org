<?php
	require PUN_ROOT.'lang/'.$pun_user['language'].'/pms.php';

	if($pun_config['o_pms_enabled'] && !$pun_user['is_guest'] && $pun_user['g_pm'] == 1)
	{
		if (isset($poster_id))
        {
            $user_contacts[] = '<a href="message_send.php?id='.$poster_id.'&pid='.$cur_post['id'].'">'.$lang_pms['PM'].'</a>';
        }
        else
        {
            $user_contacts[] = '<a href="message_send.php?id='.$cur_post['id'].'&tid='.$id.'">'.$lang_pms['PM'].'</a>';
        }
	}
?>