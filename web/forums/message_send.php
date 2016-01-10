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
require PUN_ROOT.'include/common.php';

if(!$pun_config['o_pms_enabled'] || $pun_user['is_guest'] || $pun_user['g_pm'] == 0)
	message($lang_common['No permission']);

// Load the post.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/pms.php';
require PUN_ROOT.'lang/'.$pun_user['language'].'/post.php';

if (isset($_POST['form_sent']))
{
	// Flood protection
	if($pun_user['g_id'] > PUN_GUEST){
		$result = $db->query('SELECT posted FROM '.$db->prefix.'messages ORDER BY id DESC LIMIT 1') or error('Unable to fetch message time for flood protection', __FILE__, __LINE__, $db->error());
		if(list($last) = $db->fetch_row($result)){
			if((time() - $last) < $pun_user['g_post_flood'])
				message($lang_pms['Flood start'].' '.$pun_user['g_post_flood'].' '.$lang_pms['Flood end']);
		}
	}
	// Smileys
	$hide_smilies = isset($_POST['hide_smilies']) ? 1 : 0;

	// Check subject
	$subject = pun_trim($_POST['req_subject']);
	if ($subject == '')
		message($lang_post['No subject']);
	else if (pun_strlen($subject) > 100)
		message($lang_post['Too long subject']);
	else if ($pun_config['p_subject_all_caps'] == '0' && strtoupper($subject) == $subject && $pun_user['g_id'] > PUN_GUEST)
		$subject = ucfirst(strtolower($subject));
    if (isset($_POST['preview']))
    {
    	$subject = str_replace('\'', '&#39;', $subject);
    }
    
	// Clean up message from POST
	$message = pun_linebreaks(pun_trim($_POST['req_message']));

	// Check message
	if ($message == '')
		message($lang_post['No message']);
	else if (strlen($message) > 65535)
		message($lang_post['Too long message']);
	else if ($pun_config['p_message_all_caps'] == '0' && strtoupper($message) == $message && $pun_user['g_id'] > PUN_GUEST)
		$message = ucfirst(strtolower($message));

	// Validate BBCode syntax
    require PUN_ROOT.'include/parser.php';
	if ($pun_config['p_message_bbcode'] == '1' && strpos($message, '[') !== false && strpos($message, ']') !== false)
	{
		$message = preparse_bbcode($message, $errors);
	}
    $message = preparse_url($message);
	
	if (!isset($errors) && !isset($_POST['preview']))
	{
	$multiuser = explode(", ", $_POST['req_username']);
    if (count($multiuser) > 30)
	{
		$errors[] = $lang_pms['Trop Users'];
	}
	else
	{
    for($ju=0; $ju<count($multiuser); $ju++)
    {
        $_POST['req_username'] = $multiuser[$ju];
	
	// Get userid
	$result = $db->query('SELECT u.id, u.username, u.group_id, g.g_pm_limit FROM '.$db->prefix.'users AS u INNER JOIN '.$db->prefix.'groups AS g ON u.group_id=g.g_id WHERE u.id != 1 AND u.username = \''.$db->escape($_POST['req_username']).'\'') or error('Unable to get user id', __FILE__, __LINE__, $db->error());

	// Send message
	if(list($id, $receiver, $status, $receiver_pm_limit) = $db->fetch_row($result))
    {
		// Check inbox status
		if($pun_user['g_pm_limit'] != 0 && $pun_user['g_id'] > PUN_GUEST && $status > PUN_GUEST)
		{
			$result = $db->query('SELECT count(*) FROM '.$db->prefix.'messages WHERE owner='.$id) or error('Unable to get message count for the receiver', __FILE__, __LINE__, $db->error());
			list($count) = $db->fetch_row($result);
			if($count >= $receiver_pm_limit)
				message($lang_pms['Inbox full']);
				
			// Also check users own box
			if(isset($_POST['savemessage']) && intval($_POST['savemessage']) == 1)
			{
				$result = $db->query('SELECT count(*) FROM '.$db->prefix.'messages WHERE owner='.$pun_user['id']) or error('Unable to get message count the sender', __FILE__, __LINE__, $db->error());
				list($count) = $db->fetch_row($result);
				if($count >= $pun_user['g_pm_limit'])
					message($lang_pms['Sent full']);
			}
		}
		
		// "Send" message
		$db->query('INSERT INTO '.$db->prefix.'messages (owner, subject, message, sender, sender_id, sender_ip, smileys, showed, status, posted) VALUES(
			\''.$id.'\',
			\''.$db->escape($subject).'\',
			\''.$db->escape($message).'\',
			\''.$db->escape($pun_user['username']).'\',
			\''.$pun_user['id'].'\',
			\''.get_remote_address().'\',
			\''.$hide_smilies.'\',
			\'0\',
			\'0\',
			\''.time().'\'
		)') or error('Unable to send message', __FILE__, __LINE__, $db->error());

		// Save an own copy of the message
		if(isset($_POST['savemessage']))
        {
			$db->query('INSERT INTO '.$db->prefix.'messages (owner, subject, message, sender, sender_id, sender_ip, smileys, showed, status, posted) VALUES(
				\''.$pun_user['id'].'\',
				\''.$db->escape($subject).'\',
				\''.$db->escape($message).'\',
				\''.$db->escape($receiver).'\',
				\''.$id.'\',
				\''.get_remote_address().'\',
				\''.$hide_smilies.'\',
				\'1\',
				\'1\',
				\''.time().'\'
			)') or error('Unable to send message', __FILE__, __LINE__, $db->error());
		}
	}
	else
    {
		$errors[] = $_POST['req_username'].' : '.$lang_pms['No user'];
        
        $username_pending[] = $_POST['req_username'];
	}
    }
    }
	
	if (!isset($errors))
	{
		$topic_redirect = intval($_POST['topic_redirect']);
		$post_redirect = intval($_POST['post_redirect']);
        $from_profile = isset($_POST['from_profile']) ? intval($_POST['from_profile']) : '';
        
		if($from_profile != 0)
			redirect('/users/'.$from_profile, $lang_pms['Sent redirect']);
		else if($post_redirect != 0)
			redirect('viewtopic.php?pid='.$post_redirect.'#p'.$post_redirect, $lang_pms['Sent redirect']);
		else if($topic_redirect != 0)
			redirect('message_list.php?tid='.$topic_redirect, $lang_pms['Sent redirect']);
		else
			redirect('message_list.php', $lang_pms['Sent redirect']);
	}
	}
}

if (isset($_GET['id']))
	$id = intval($_GET['id']);
else
	$id = 0;

	if($id > 0){
		$result = $db->query('SELECT username FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch message info', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message($lang_common['Bad request']);
		list($username) = $db->fetch_row($result);
	}

	if(isset($_GET['reply']) || isset($_GET['quote'])){
		$r = isset($_GET['reply']) ? intval($_GET['reply']) : 0;
		$q = isset($_GET['quote']) ? intval($_GET['quote']) : 0;

		// Get message info
		empty($r) ? $id = $q : $id = $r;
		$result = $db->query('SELECT * FROM '.$db->prefix.'messages WHERE id='.$id.' AND owner='.$pun_user['id']) or error('Unable to fetch message info', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message($lang_common['Bad request']);
		$message_db = $db->fetch_assoc($result);

		// Quote the message
		if(isset($_GET['quote']))
			$message = '[quote='.$message_db['sender'].']'.$message_db['message'].'[/quote]';

		// Add subject
		if (!isset($_POST['preview']))
		{
			$subject = str_replace('\'', '&#39;', $message_db['subject']);
			$subject = ((strpos($subject, 'RE:') === 0) ? $subject : 'RE: '.$subject);
		}
	}
    else if (isset($username_pending))
    {
        $username = implode(', ', $username_pending);
    }
    else if (isset($_POST['req_username']))
    {
        $username = $_POST['req_username'];
    }

	$action = $lang_pms['Send a message'];
	$form = '<form method="post" id="post" action="message_send.php?action=send#postpreview" onsubmit="return process_form(this)">';

	$page_title = pun_htmlspecialchars($pun_config['o_board_title']).' / '.$action;
	$form_name = 'post';

	$cur_index = 1;
	if (!isset($username))
		$username = '';
	if (!isset($subject))
		$subject = '';
	if (!isset($message))
		$message = '';
	$footer_style = 'message_send';
	
	require PUN_ROOT.'header.php';

  // If there are errors, we display them
	if (!empty($errors))
	{
	?>
	<div id="posterror" class="block">
	<h2><span><?php echo $lang_post['Post errors'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<p><?php echo $lang_post['Post errors info'] ?></p>
			<ul>
	<?php

	while (list(, $cur_error) = each($errors))
		echo "\t\t\t\t".'<li><strong>'.$cur_error.'</strong></li>'."\n";
	?>
			</ul>
		</div>
	</div>
	</div>

	<?php
	}
	else if (isset($_POST['preview']))
	{
		require_once PUN_ROOT.'include/parser.php';
		$preview_message = parse_message($message, $hide_smilies);

	?>
	<div id="postpreview" class="blockpost">
	<h2><span><?php echo $lang_post['Post preview'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<div class="postright">
				<div class="postmsg">
					<?php echo $preview_message."\n" ?>
				</div>
			</div>
		</div>
	</div>
	</div>
	<?php
	}
?>
<div class="blockform">
	<h2><span><?php echo $action ?></span></h2>
	<div class="box">
	<?php echo $form."\n" ?>
		<div class="inform">
		<fieldset>
			<legend><?php echo $lang_common['Write message legend'] ?></legend>
			<div class="infldset txtarea">
				<input type="hidden" name="form_sent" value="1" />
				<input type="hidden" name="topic_redirect" value="<?php
				if (isset($_GET['tid']))
				{
					echo $_GET['tid'];
				}
				else if (isset($_POST['topic_redirect']))
				{
					echo $_POST['topic_redirect'];
				} ?>" />
				<input type="hidden" name="post_redirect" value="<?php
				if (isset($_GET['pid']))
				{
					echo $_GET['pid'];
				}
				else if (isset($_POST['post_redirect']))
				{
					echo $_POST['post_redirect'];
				} ?>" />
				<input type="hidden" name="from_profile" value="<?php
				if (isset($_GET['uid']))
				{
					echo $_GET['uid'];
				}
				else if (isset($_POST['from_profile']))
				{
					echo $_POST['from_profile'];
				} ?>" />
				<input type="hidden" name="form_user" value="<?php echo (!$pun_user['is_guest']) ? pun_htmlspecialchars($pun_user['username']) : 'Guest'; ?>" />
				<label class="conl"><strong><?php echo $lang_pms['Send to'] ?></strong><br /><?php echo '<input class="longinput" type="text" name="req_username" value="'.pun_htmlspecialchars($username).'" tabindex="'.($cur_index++).'" />'; ?><br /></label>
				<div class="clearer"></div>
				<label><strong><?php echo $lang_common['Subject'] ?></strong><br /><input class="longinput" type='text' name='req_subject' value='<?php echo $subject ?>' maxlength="100" tabindex='<?php echo $cur_index++ ?>' /><br /></label>
				<?php require PUN_ROOT.'mod_easy_bbcode.php'; ?>
				<label><strong><?php echo $lang_common['Message'] ?></strong><br />
				<textarea id="req_message" name="req_message" rows="15" tabindex="<?php echo $cur_index++ ?>"><?php echo $message ?></textarea><br /></label>
				<ul class="bblinks">
					<li><a href="help.php#bbcode" onclick="window.open(this.href); return false;"><?php echo $lang_common['BBCode'] ?></a>: <?php echo ($pun_config['p_message_bbcode'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
					<li><a href="help.php#img" onclick="window.open(this.href); return false;"><?php echo $lang_common['img tag'] ?></a>: <?php echo ($pun_config['p_message_img_tag'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
					<li><a href="help.php#smilies" onclick="window.open(this.href); return false;"><?php echo $lang_common['Smilies'] ?></a>: <?php echo ($pun_config['o_smilies'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
				</ul>
			</div>
		</fieldset>
<?php
	$checkboxes = array();

	if ($pun_config['o_smilies'] == '1')
		$checkboxes[] = '<label><input type="checkbox" name="hide_smilies" value="1" tabindex="'.($cur_index++).'"'.(isset($_POST['hide_smilies']) ? ' checked="checked"' : '').' />'.$lang_post['Hide smilies'];

    if (isset($_POST['form_sent']) && !isset($_POST['savemessage']))
    {
        $save_message_checked = '';
    }
    else
    {
        $save_message_checked = ' checked="checked"';
    }
	$checkboxes[] = '<label><input type="checkbox" name="savemessage" value="1" '.$save_message_checked.' tabindex="'.($cur_index++).'" />'.$lang_pms['Save message'];

	if (!empty($checkboxes))
	{
?>
			</div>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_common['Options'] ?></legend>
					<div class="infldset">
						<div class="rbox">
							<?php echo implode('<br /></label>'."\n\t\t\t\t", $checkboxes).'<br /></label>'."\n" ?>
						</div>
					</div>
				</fieldset>
<?php
	}
?>
			</div>
			<p>
            <input type="submit" name="preview" value="<?php echo $lang_post['Preview'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="p" />
			<input onclick="javascript:if(document.forms['post'].req_subject.value==''){alert('<?php echo($lang_post['No subject']); ?>');Event.stop(event);}else if(document.forms['post'].req_message.value==''){alert('<?php echo($lang_post['No message']); ?>');Event.stop(event);}" type="submit" name="submit" value="<?php echo $lang_pms['Send'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="s" /></p>
		</form>
	</div>
</div>
<?php
	require PUN_ROOT.'footer.php';

