<?php
/***********************************************************************

  Copyright (C) 2002, 2003, 2004  Rickard Andersson (rickard@punbb.org)

  This file is part of PunBB.

  PunBB is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  PunBB is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
  MA  02111-1307  USA

************************************************************************/


define('PUN_ROOT', './');
$show_ban_message = false;

require PUN_ROOT.'include/common.php';
require PUN_ROOT.'include/parser.php';

if(!$pun_config['o_pms_enabled'] || $pun_user['g_pm'] == 0)
	message($lang_common['No permission']);

if ($pun_user['is_guest'])
	message($lang_common['Login required']);

if(isset($_GET['user_id']) && $pun_user['g_id'] == PUN_ADMIN)
{
    $user_id = (int)$_GET['user_id'];
    $param_user_id = '?user_id=' . $user_id;
    $param_user_id_1 = '&user_id=' . $user_id;
    $param_user_id_2 = '&amp;user_id=' . $user_id;
}
else
{
    $user_id = $pun_user['id'];
    $param_user_id = '';
    $param_user_id_1 = '';
    $param_user_id_2 = '';
}
    
// Load the message.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/pms.php';
require PUN_ROOT.'lang/'.$pun_user['language'].'/topic.php';
require PUN_ROOT.'lang/'.$pun_user['language'].'/misc.php';

// Inbox or Sent?
if(isset($_GET['box']))
	$box = (int)($_GET['box']);
else
	$box = 0;

$box != 1 ? $box = 0 : $box = 1;
$box != 1 ? $status = 0 : null;
$box == 0 ? $name = $lang_pms['Inbox'] : $name = $lang_pms['Outbox'];
//$name plus the link to the other box
$page_name = $name;

// Delete multiple posts
if( isset($_POST['delete_messages']) || isset($_POST['delete_messages_comply']) )
{
	if( isset($_POST['delete_messages_comply']) )
	{
		//Check this is legit
		confirm_referrer('message_list.php');
		
		// Delete messages 
		$db->query('DELETE FROM '.$db->prefix.'messages WHERE id IN('.$_POST['messages'].') AND owner=\''.$user_id.'\'') or error('Unable to delete messages.', __FILE__, __LINE__, $db->error());
		redirect('message_list.php?box='.$_POST['box'].$param_user_id_1, $lang_pms['Deleted redirect']);
	}
	else
	{
		$page_title = $lang_pms['Multidelete'].' / '.pun_htmlspecialchars($pun_config['o_board_title']);
		$idlist = $_POST['delete_messages'];
        $footer_style = 'message_list';
		require PUN_ROOT.'header.php';
?>
<div class="blockform">
	<h2><span><?php echo $lang_pms['Multidelete'] ?></span></h2>
	<div class="box">
		<form method="post" action="message_list.php<?php echo $param_user_id ?>">
			<input type="hidden" name="messages" value="<?php echo implode(',', array_values($idlist)) ?>">
			<input type="hidden" name="box" value="<?php echo $_POST['box']; ?>">
			<div class="inform">
				<fieldset>
					<div class="infldset">
						<p class="warntext"><strong><?php echo $lang_pms['Delete messages comply'] ?></strong></p>
					</div>
				</fieldset>
			</div>
			<p><input type="submit" name="delete_messages_comply" value="<?php echo $lang_pms['Delete'] ?>" /><a href="javascript:history.go(-1)"><?php echo $lang_common['Go back'] ?></a></p>
		</form>
	</div>
</div>
<?php
		require PUN_ROOT.'footer.php';
	}
}

// Mark all messages as read
else if (isset($_GET['action']) && $_GET['action'] == 'markall')
{
	$db->query('UPDATE '.$db->prefix.'messages SET showed=1 WHERE owner='.$user_id) or error('Unable to update message status', __FILE__, __LINE__, $db->error());
	$p = (!isset($_GET['p']) || $_GET['p'] <= 1) ? 1 : $_GET['p'];
	redirect('message_list.php?box='.$box.'&p='.$p.$param_user_id_1, $lang_pms['Read redirect']);
}

$page_title = $lang_pms['Private Messages'].' - '.$name.' / '.pun_htmlspecialchars($pun_config['o_board_title']);

// Get message count
$result = $db->query('SELECT count(*) FROM '.$db->prefix.'messages WHERE status='.$box.' AND owner='.$user_id) or error('Unable to count messages', __FILE__, __LINE__, $db->error());
list($num_messages) = $db->fetch_row($result);

//What page are we on?
$num_pages = ceil($num_messages / $pun_config['o_pms_mess_per_page']);
$p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : $_GET['p'];
$start_from = $pun_config['o_pms_mess_per_page'] * ($p - 1);
$limit = $start_from.','.$pun_config['o_pms_mess_per_page'];

$footer_style = 'message_list';
require PUN_ROOT.'header.php';
?>
<div class="block2col">
	<div class="blockmenu">
		<h2><span><?php echo $lang_pms['Private Messages'] ?></span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
                    <li><a href="message_send.php"><?php echo $lang_pms['New message']; ?></a></li>
					<li <?php if ($box == 0) echo 'class="isactive"' ?>><a href="message_list.php?box=0<?php echo $param_user_id_2 ?>"><?php echo $lang_pms['Inbox'] ?></a></li>
					<li <?php if ($box == 1) echo 'class="isactive"' ?>><a href="message_list.php?box=1<?php echo $param_user_id_2 ?>"><?php echo $lang_pms['Outbox'] ?></a></li>
                    <li><a href="<?php echo 'message_list.php?action=multidelete&amp;box='.$box.'&amp;p='.$p.$param_user_id_2.'">'.$lang_pms['Multidelete']; ?></a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="linkst">
		<div class="inbox">
			<p class="pagelink conl"><?php echo $lang_common['Pages'].': '.paginate($num_pages, $p, 'message_list.php?box='.$box.$param_user_id_2) ?></p>
			<p class="postlink conr"><a href="message_send.php"><?php echo $lang_pms['New message']; ?></a></p>
		</div>
	</div>

<?php
//Are we viewing a PM?
if(isset($_GET['id'])){
	//Yes! Lets get the details	
	$id = intval($_GET['id']);

	// Set user
	$result = $db->query('SELECT status,owner FROM '.$db->prefix.'messages WHERE id='.$id) or error('Unable to get message status', __FILE__, __LINE__, $db->error());
	list($status, $owner) = $db->fetch_row($result);
	$status == 0 ? $where = 'u.id=m.sender_id' : $where = 'u.id=m.owner';

	$result = $db->query('SELECT m.id AS mid,m.subject,m.sender_ip,m.message,m.smileys,m.posted,m.showed,u.id,u.group_id as g_id,g.g_user_title,u.username,u.registered,u.email,u.title,u.url,u.icq,u.msn,u.aim,u.yahoo,u.location,u.use_avatar,u.email_setting,u.num_posts,u.admin_note,u.signature FROM '.$db->prefix.'messages AS m,'.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON u.group_id = g.g_id WHERE '.$where.' AND m.id='.$id) or error('Unable to fetch message and user info', __FILE__, __LINE__, $db->error());
	$cur_post = $db->fetch_assoc($result);
	
	if ($owner != $user_id)
		message($lang_common['No permission']);

	if ($cur_post['showed'] == 0 && $param_user_id == '')
		$db->query('UPDATE '.$db->prefix.'messages SET showed=1 WHERE id='.$id) or error('Unable to update message info', __FILE__, __LINE__, $db->error());

	if ($cur_post['id'] > 0)
	{
		$username = '<a href="/users/'.$cur_post['id'].'">'.pun_htmlspecialchars($cur_post['username']).'</a>';
		$cur_post['username'] = $cur_post['username'];
		$user_title = get_title($cur_post);
		
		if ($pun_config['o_censoring'] == '1')
			$user_title = censor_words($user_title);
		
		if ($pun_config['o_avatars'] == '1' && $cur_post['use_avatar'] == '1' && $pun_user['show_avatars'] != '0')
		{
			if ($img_size = @getimagesize($pun_config['o_avatars_dir'].'/'.$cur_post['id'].'.gif'))
				$user_avatar = '<img src="'.$pun_config['o_avatars_dir'].'/'.$cur_post['id'].'.gif" '.$img_size[3].' alt="" />';
			else if ($img_size = @getimagesize($pun_config['o_avatars_dir'].'/'.$cur_post['id'].'.jpg'))
				$user_avatar = '<img src="'.$pun_config['o_avatars_dir'].'/'.$cur_post['id'].'.jpg" '.$img_size[3].' alt="" />';
			else if ($img_size = @getimagesize($pun_config['o_avatars_dir'].'/'.$cur_post['id'].'.png'))
				$user_avatar = '<img src="'.$pun_config['o_avatars_dir'].'/'.$cur_post['id'].'.png" '.$img_size[3].' alt="" />';
		}
		else
			$user_avatar = '';

			// We only show location, register date, post count and the contact links if "Show user info" is enabled
		if ($pun_config['o_show_user_info'] == '1')
		{
			if ($cur_post['location'] != '')
			{
				if ($pun_config['o_censoring'] == '1')
					$cur_post['location'] = censor_words($cur_post['location']);

				$user_info[] = '<dd>'.$lang_topic['From'].': '.pun_htmlspecialchars($cur_post['location']);
			}

			// $user_info[] = '<dd>'.$lang_common['Registered'].': '.date($pun_config['o_date_format'], $cur_post['registered']);

			if ($pun_config['o_show_post_count'] == '1' || $pun_user['g_id'] < PUN_GUEST)
				$user_info[] = '<dd>'.$lang_common['Posts'].': '.$cur_post['num_posts'];

			// Now let's deal with the contact links (E-mail and URL)
			if (($cur_post['email_setting'] == '0' && !$pun_user['is_guest']) || $pun_user['g_id'] < PUN_GUEST)
				$user_contacts[] = '<a href="mailto:'.$cur_post['email'].'">'.$lang_common['E-mail'].'</a>';
			else if ($cur_post['email_setting'] == '1' && !$pun_user['is_guest'])
				$user_contacts[] = '<a href="misc.php?email='.$cur_post['id'].'">'.$lang_common['E-mail'].'</a>';
                require(PUN_ROOT.'include/pms/viewtopic_PM-link.php'); 
			if ($cur_post['url'] != '')
				$user_contacts[] = '<a href="'.pun_htmlspecialchars($cur_post['url']).'">'.$lang_topic['Website'].'</a>';
		}
		
		//Moderator and Admin stuff
		if ($pun_user['g_id'] < PUN_GUEST)
		{
			$user_info[] = '<dd>IP: <a href="moderate.php?get_host='.$cur_post['id'].'">'.$cur_post['sender_ip'].'</a>';

			if ($cur_post['admin_note'] != '')
				$user_info[] = '<dd>'.$lang_topic['Note'].': <strong>'.pun_htmlspecialchars($cur_post['admin_note']).'</strong>';
		}
		// Generation post action array (reply, delete etc.)
		if(!$status)
			$post_actions[] = '<li><a href="message_send.php?id='.$cur_post['id'].'&amp;reply='.$cur_post['mid'].'">'.$lang_pms['Reply'].'</a>';
	
		$post_actions[] = '<li><a href="message_delete.php?id='.$cur_post['mid'].'&amp;box='.(int)$_GET['box'].'&amp;p='.(int)$_GET['p'].'">'.$lang_pms['Delete'].'</a>';
	
		if(!$status)
			$post_actions[] = '<li><a href="message_send.php?id='.$cur_post['id'].'&amp;quote='.$cur_post['mid'].'">'.$lang_pms['Quote'].'</a>';

	}
	// If the sender has been deleted
	else
	{
		$result = $db->query('SELECT id,sender,message,posted FROM '.$db->prefix.'messages WHERE id='.$id) or error('Unable to fetch message and user info', __FILE__, __LINE__, $db->error());
		$cur_post = $db->fetch_assoc($result);

		$username = pun_htmlspecialchars($cur_post['sender']);
		$user_title = "Deleted User";

		$post_actions[] = '<li><a href="message_delete.php?id='.$cur_post['id'].'&amp;box='.(int)$_GET['box'].'&amp;p='.(int)$_GET['p'].'">'.$lang_pms['Delete'].'</a>';
	}
	
	// Perform the main parsing of the message (BBCode, smilies, censor words etc)
	$cur_post['smileys'] = isset($cur_post['smileys']) ? $cur_post['smileys'] : $pun_user['show_smilies'];
	$cur_post['message'] = parse_message($cur_post['message'], (int)($cur_post['smileys']));
	
	// Do signature parsing/caching
	if (isset($cur_post['signature']) && $pun_user['show_sig'] != '0')
	{
		$signature = parse_signature($cur_post['signature']);
	}
	
?>

	<div id="p<?php echo $cur_post['id'] ?>" class="blockpost row_odd firstpost">
		<h2><span><?php echo format_time($cur_post['posted']) ?></span></h2>
		<div class="box">
			<div class="inbox">
				<div class="postleft">
					<dl>
						<dt><strong><?php echo $username ?></strong></dt>
						<dd class="usertitle"><strong><?php echo $user_title ?></strong></dd>
						<dd class="postavatar"><?php if (isset($user_avatar)) echo $user_avatar ?></dd>
	<?php if (isset($user_info)) if (count($user_info)) echo "\t\t\t\t\t".implode('</dd>'."\n\t\t\t\t\t", $user_info).'</dd>'."\n"; ?>
	<?php if (isset($user_contacts)) if (count($user_contacts)) echo "\t\t\t\t\t".'<dd class="usercontacts">'.implode('&nbsp;&nbsp;', $user_contacts).'</dd>'."\n"; ?>
					</dl>
				</div>
				<div class="postright">
					<div class="postmsg">
						<?php echo $cur_post['message']."\n" ?>
					</div>
	<?php if (isset($signature)) echo "\t\t\t\t".'<div class="postsignature"><hr />'.$signature.'</div>'."\n"; ?>
				</div>
				<div class="clearer"></div>
				<div class="postfootright"><?php echo (count($post_actions)) ? '<ul>'.implode($lang_topic['Link separator'].'</li>', $post_actions).'</li></ul></div>'."\n" : '<div>&nbsp;</div></div>'."\n" ?>
			</div>
		</div>
	</div>
	<div class="clearer"></div>
<?php	
}

?>
<form method="post" action="message_list.php<?php echo $param_user_id ?>">
<div class="blocktable">
	<h2><span><?php echo $name ?></span></h2>
	<div class="box">
		<div class="inbox">
			<table>
			<thead>
				<tr>
<?php
		if($pun_user['g_pm_limit'] != 0 && $pun_user['g_id'] > PUN_GUEST){
			// Get total message count
			$result = $db->query('SELECT count(*) FROM '.$db->prefix.'messages WHERE owner='.$user_id) or error('Unable to count messages', __FILE__, __LINE__, $db->error());
			list($tot_messages) = $db->fetch_row($result);
			$proc = ceil($tot_messages / $pun_user['g_pm_limit'] * 100);
			$status = ' - '.$lang_pms['Status'].' '.$proc.'%';
		}
		else 
			$status = '';
?>
					<th class="tcl"><?php echo $lang_pms['Subject'] ?><?php echo $status ?></th>
					<th><?php if($box == 0) echo $lang_pms['Sender']; else echo $lang_pms['Receiver']; ?></th>
					<?php if(isset($_GET['action']) && $_GET['action'] == 'multidelete') { ?>
					<th class="tcr"><?php echo $lang_pms['Date'] ?></th>
					<th><?php echo $lang_pms['Delete'] ?></th>
					<?php } else { ?>
					<th class="tcr"><?php echo $lang_pms['Date'] ?></th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
<?php

// Fetch messages
$result = $db->query('SELECT * FROM '.$db->prefix.'messages WHERE owner='.$user_id.' AND status='.$box.' ORDER BY posted DESC LIMIT '.$limit) or error('Unable to fetch messages list for forum', __FILE__, __LINE__, $db->error());
$new_messages = false;
$messages_exist = false;

// If there are messages in this folder.
if ($db->num_rows($result))
{
	$messages_exist = true;
	while ($cur_mess = $db->fetch_assoc($result))
	{
		$icon_text = $lang_common['Normal icon'];
		$icon_type = 'icon';
		if ($cur_mess['showed'] == '0')
		{
			$icon_text .= ' '.$lang_common['New icon'];
			$icon_type = 'icon inew';
		}

		($new_messages == false && $cur_mess['showed'] == '0') ? $new_messages = true : null;
			
		$subject = '<a href="message_list.php?id='.$cur_mess['id'].'&amp;p='.$p.'&amp;box='.(int)$box.$param_user_id_2.'">'.pun_htmlspecialchars($cur_mess['subject']).'</a>';
		if (isset($_GET['id']))
			if($cur_mess['id'] == $_GET['id'])
				$subject = "<strong>$subject</strong>";

?>
	<tr>

		<td class="tcl">
			<div class="intd">
				<div class="<?php echo $icon_type ?>"><div class="nosize"><?php echo trim($icon_text) ?></div></div>
				<div class="tclcon">
					<?php echo $subject."\n" ?>
				</div>
			</div>
		</td>
		<td class="tc2"><a href="/users/<?php echo $cur_mess['sender_id'] ?>"><?php echo $cur_mess['sender'] ?></a></td>
<?php if(isset($_GET['action']) && $_GET['action'] == 'multidelete') { ?>
		<td class="tcra"><?php echo format_time($cur_mess['posted']) ?></td>
		<td class="tca"><input type="checkbox" name="delete_messages[]" value="<?php echo $cur_mess['id']; ?>"></td>
<?php } else { ?>
		<td class="tcr"><?php echo format_time($cur_mess['posted']) ?></td>
<?php } ?>
	</tr>
<?php

	}
}
else
{
	$cols = isset($_GET['action']) ? '4' : '3';
	echo "\t".'<tr><td class="puncon1" colspan="'.$cols.'">'.$lang_pms['No messages'].'</td></tr>'."\n";
}
?>
			</tbody>
			</table>
		</div>
	</div>
</div>

<div class="linksb">
	<div class="inbox">
		<p class="pagelink conl"><?php echo $lang_common['Pages'].': '.paginate($num_pages, $p, 'message_list.php?box='.$box.$param_user_id_2) ?></p>
<?php
if(isset($_GET['action']) && $_GET['action'] == 'multidelete')
{
?>
		<p class="postlink conr">
		    <input type="button" onclick="$('#punmessage_list form .tca input[type=checkbox]').prop('checked', true);" alt="<?php echo $lang_misc['Select all'] ?>" title="" value="<?php echo $lang_misc['Select all'] ?>" name="<?php echo $lang_misc['Select all'] ?>"/>&nbsp;&nbsp;
		    <input type="button" onclick="$('#punmessage_list form .tca input[type=checkbox]').prop('checked', false);" alt="<?php echo $lang_misc['Deselect all'] ?>" title="" value="<?php echo $lang_misc['Deselect all'] ?>" name="<?php echo $lang_misc['Deselect all'] ?>"/>&nbsp;&nbsp;
            <input type="hidden" name="box" value="<?php echo $box	; ?>"><input type="submit" value="<?php echo $lang_pms['Delete']; ?>">
        </p>
<?php
}
else
{
?>
		<p class="postlink conr"><a href="message_send.php"><?php echo $lang_pms['New message']; ?></a></p>
<?php
}
?>
		<ul><li><a href="<?php echo get_home_url() ?>"><?php echo pun_htmlspecialchars($pun_config['o_board_title']) ?></a>&nbsp;</li><li>&raquo;&nbsp;<?php echo $lang_pms['Private Messages'] ?>&nbsp;</li><li>&raquo;&nbsp;<?php echo $page_name ?></li></ul>
		<div class="clearer"></div>
	</div>
</div>
</form>
	<div class="clearer"></div>
</div>
<?php
if(isset($_GET['id'])){
	$forum_id = $id;
}
require PUN_ROOT.'footer.php';
