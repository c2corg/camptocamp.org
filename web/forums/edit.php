<?php
/***********************************************************************

  Copyright (C) 2002-2005  Rickard Andersson (rickard@punbb.org)

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
define('SF_ROOT_DIR',    realpath(dirname(__file__).'/../../'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       false);

require_once SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

define('PUN_ROOT', './');
require PUN_ROOT.'include/common.php';


if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view']);


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 1)
	message($lang_common['Bad request']);

// Fetch some info about the post, the topic and the forum
$result = $db->query('SELECT f.id AS fid, f.forum_name, f.moderators, f.redirect_url, fp.post_replies, fp.post_topics, t.id AS tid, t.subject, t.posted, t.closed, p.poster, p.poster_id, p.poster_email, p.message, p.hide_smilies FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.post_replies=1 OR fp.post_topics=1) AND p.id='.$id) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result))
	message($lang_common['Bad request']);

$cur_post = $db->fetch_assoc($result);
$forum_id = $cur_post['fid'];

// Sort out who the moderators are and if we are currently a moderator (or an admin)
list($is_admmod, $is_c2c_board) = get_is_admmod($forum_id, $cur_post['moderators'], $pun_user);

// Determine whether this post is the "topic post" or not
$result = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE topic_id='.$cur_post['tid'].' ORDER BY posted LIMIT 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
$topic_post_id = $db->result($result);

$is_comment = get_is_comment($forum_id);
$can_edit_subject = ($id == $topic_post_id && (($pun_user['g_edit_subjects_interval'] == '0' || (time() - $cur_post['posted']) < $pun_user['g_edit_subjects_interval']) || $is_admmod)) ? true : false;
$hidden_subject = $is_comment && !$is_admmod;
$input_type = $hidden_subject ? 'hidden' : 'text';
if ($hidden_subject)
{
    $subject_title = '';
}
else
{
    $subject_title = '<strong>'.$lang_common['Subject'].'</strong><br />';
}


// Do we have permission to edit this post?
if (($pun_user['g_edit_posts'] == '0' ||
	$cur_post['poster_id'] != $pun_user['id'] ||
	$cur_post['closed'] == '1') &&
	!$is_admmod)
	message($lang_common['No permission']);

// Load the post.php/edit.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/post.php';

// Start with a clean slate
$errors = array();


if (isset($_POST['form_sent']))
{
	if ($is_admmod)
		confirm_referrer('edit.php');

	// If it is a topic it must contain a subject
	if ($can_edit_subject)
	{
		$subject = pun_trim($_POST['req_subject']);

		if ($subject == '')
			$errors[] = $lang_post['No subject'];
		else if (pun_strlen($subject) > 100)
			$errors[] = $lang_post['Too long subject'];
		else if ($pun_config['p_subject_all_caps'] == '0' && strtoupper($subject) == $subject && $pun_user['g_id'] > PUN_MOD)
			$subject = ucfirst(strtolower($subject));
	}

    // Moderator can edit username and email
    if ($is_admmod)
    {
		$username = trim($_POST['req_username']);

		// Load the register.php/profile.php language files
		require PUN_ROOT.'lang/'.$pun_user['language'].'/prof_reg.php';
		require PUN_ROOT.'lang/'.$pun_user['language'].'/register.php';

		// It's a guest, so we have to validate the username
		if (strlen($username) < 2)
			$errors[] = $lang_prof_reg['Username too short'];
		else if (!strcasecmp($username, 'Guest') || !strcasecmp($username, $lang_common['Guest']))
			$errors[] = $lang_prof_reg['Username guest'];
		else if (preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $username))
			$errors[] = $lang_prof_reg['Username IP'];

		if ((strpos($username, '[') !== false || strpos($username, ']') !== false) && strpos($username, '\'') !== false && strpos($username, '"') !== false)
			$errors[] = $lang_prof_reg['Username reserved chars'];
		if (preg_match('#\[b\]|\[/b\]|\[u\]|\[/u\]|\[i\]|\[/i\]|\[color|\[/color\]|\[quote\]|\[quote=|\[/quote\]|\[code\]|\[/code\]|\[img\]|\[/img\]|\[url|\[/url\]|\[email|\[/email\]#i', $username))
			$errors[] = $lang_prof_reg['Username BBCode'];

		// Check username for any censored words
		$temp = censor_words($username);
		if ($temp != $username)
			$errors[] = $lang_register['Username censor'];

		if ($cur_post['poster_id'] <= 1)
        {
            $email = strtolower(trim(($pun_config['p_force_guest_email'] == '1') ? $_POST['req_email'] : $_POST['email']));
    		if ($pun_config['p_force_guest_email'] == '1' || $email != '')
    		{
    			require PUN_ROOT.'include/email.php';
    			if (!is_valid_email($email))
    				$errors[] = $lang_common['Invalid e-mail'];
    		}
        }
    }
    
	// Clean up message from POST
	$message = pun_linebreaks(pun_trim($_POST['req_message']));

	if ($message == '')
		$errors[] = $lang_post['No message'];
	else if (strlen($message) > 65535)
		$errors[] = $lang_post['Too long message'];
	else if ($pun_config['p_message_all_caps'] == '0' && strtoupper($message) == $message && $pun_user['g_id'] > PUN_MOD)
		$message = ucfirst(strtolower($message));

	// Validate BBCode syntax
    require PUN_ROOT.'include/parser.php';
	if ($pun_config['p_message_bbcode'] == '1' && strpos($message, '[') !== false && strpos($message, ']') !== false)
	{
		$message = preparse_bbcode($message, $errors);
	}
    $message = preparse_url($message);

	$hide_smilies = isset($_POST['hide_smilies']) ? intval($_POST['hide_smilies']) : 0;
	if ($hide_smilies != '1') $hide_smilies = '0';

	// Did everything go according to plan?
	if (empty($errors) && !isset($_POST['preview']))
	{
		$edited_sql = (!isset($_POST['silent']) || !$is_admmod) ? $edited_sql = ', edited='.time().', edited_by=\''.$db->escape($pun_user['username']).'\'' : '';

		require PUN_ROOT.'include/search_idx.php';

		if ($can_edit_subject)
		{
			// Update the topic and any redirect topics
			$db->query('UPDATE '.$db->prefix.'topics SET subject=\''.$db->escape($subject).'\' WHERE id='.$cur_post['tid'].' OR moved_to='.$cur_post['tid']) or error('Unable to update topic', __FILE__, __LINE__, $db->error());

			// We changed the subject, so we need to take that into account when we update the search words
			update_search_index('edit', $id, $message, $subject);
		}
		else
			update_search_index('edit', $id, $message);

		// Update the post
        if (!$is_admmod)
        {
            $db->query('UPDATE '.$db->prefix.'posts SET message=\''.$db->escape($message).'\', hide_smilies=\''.$hide_smilies.'\''.$edited_sql.' WHERE id='.$id) or error('Unable to update post', __FILE__, __LINE__, $db->error());
        }
        else if ($cur_post['poster_id'] > 1)
        {
            $db->query('UPDATE '.$db->prefix.'posts SET poster=\''.$db->escape($username).'\', message=\''.$db->escape($message).'\', hide_smilies=\''.$hide_smilies.'\''.$edited_sql.' WHERE id='.$id) or error('Unable to update post', __FILE__, __LINE__, $db->error());
            // update the forums if this is the last post
            $db->query('UPDATE '.$db->prefix.'forums SET last_poster=\''.$db->escape($username).'\' WHERE last_post_id='.$id) or error('Unable to update post', __FILE__, __LINE__, $db->error());
            // update the topics if this is the last post
            $db->query('UPDATE '.$db->prefix.'topics SET last_poster=\''.$db->escape($username).'\' WHERE last_post_id='.$id) or error('Unable to update topic', __FILE__, __LINE__, $db->error());
            // update the topics if the modified username was the one used for the topic creation
            if ($id == $topic_post_id)
            {
                $db->query('UPDATE '.$db->prefix.'topics SET poster=\''.$db->escape($username).'\' WHERE id=\''.$cur_post['tid'].'\'') or error('Unable to update topic', __FILE__, __LINE__, $db->error());
            }
        }
        else
        {
            $email_sql = ($pun_config['p_force_guest_email'] == '1' || $email != '') ? '\''.$email.'\'' : 'NULL';
            $db->query('UPDATE '.$db->prefix.'posts SET poster=\''.$db->escape($username).'\', poster_email='.$email_sql.', message=\''.$db->escape($message).'\', hide_smilies=\''.$hide_smilies.'\''.$edited_sql.' WHERE id='.$id) or error('Unable to update post', __FILE__, __LINE__, $db->error());
            // update the forums if this is the last post
            $db->query('UPDATE '.$db->prefix.'forums SET last_poster=\''.$db->escape($username).'\' WHERE last_post_id='.$id) or error('Unable to update post', __FILE__, __LINE__, $db->error());
            // update the topics if this is the last post
            $db->query('UPDATE '.$db->prefix.'topics SET last_poster=\''.$db->escape($username).'\' WHERE last_post_id='.$id) or error('Unable to update topic', __FILE__, __LINE__, $db->error());
            // update the topics if the modified username was the one used for the topic creation
            if ($id == $topic_post_id)
            {
                $db->query('UPDATE '.$db->prefix.'topics SET poster=\''.$db->escape($username).'\' WHERE id=\''.$cur_post['tid'].'\'') or error('Unable to update topic', __FILE__, __LINE__, $db->error());
            }
        }

        if ($is_comment)
        {
            $doc_param = get_doc_param(($subject) ? $subject : $cur_post['subject']);
            redirect($doc_param[2].'#p'.$id, $lang_post['Edit redirect']);
        }
        else
        {
            redirect('viewtopic.php?pid='.$id.'#p'.$id, $lang_post['Edit redirect']);
        }
	}
}



$page_title = pun_htmlspecialchars($pun_config['o_board_title']).' / '.$lang_post['Edit post'];
$required_fields = array('req_subject' => $lang_common['Subject'], 'req_message' => $lang_common['Message']);
$focus_element = array('edit', 'req_message');
$footer_style = 'edit';
require PUN_ROOT.'header.php';

$cur_index = 1;

?>
<div class="linkst">
	<div class="inbox">
		<ul><li><a href="index.php"><?php echo $lang_common['Index'] ?></a></li><li>&nbsp;&raquo;&nbsp;<a href="viewforum.php?id=<?php echo $cur_post['fid'] ?>"><?php echo pun_htmlspecialchars($cur_post['forum_name']) ?></a></li><li>&nbsp;&raquo;&nbsp;<a href="viewtopic.php?pid=<?php echo $id.'#p'.$id.'">'.pun_htmlspecialchars($cur_post['subject']); ?></a></li></ul>
	</div>
</div>

<?php

// If there are errors, we display them
if (!empty($errors))
{

?>
<div id="posterror" class="block">
	<h2><span><?php echo $lang_post['Post errors'] ?></span></h2>
	<div class="box">
		<div class="inbox"
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
	<h2><?php echo $lang_post['Edit post'] ?></h2>
	<div class="box">
		<form id="edit" method="post" action="edit.php?id=<?php echo $id ?>&amp;action=edit#postpreview" onsubmit="return process_form(this)">
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_post['Edit post legend'] ?></legend>
					<input type="hidden" name="form_sent" value="1" />
<?php

if ($is_admmod)
{
    if (!isset($_POST['req_username']))
    {
        $username = $cur_post['poster'];
    }
    else
    {
        $username = $_POST['req_username'];
    }
?>						<label class="conl"><strong><?php echo $lang_post['Guest name'] ?></strong><br /><input type="text" name="req_username" value="<?php echo pun_htmlspecialchars($username); ?>" size="25" maxlength="25" tabindex="<?php echo $cur_index++ ?>" /><br /></label>
<?php

    if ($cur_post['poster_id'] <= 1)
    {
    	$email_label = ($pun_config['p_force_guest_email'] == '1') ? '<strong>'.$lang_common['E-mail'].'</strong>' : $lang_common['E-mail'];
    	$email_form_name = ($pun_config['p_force_guest_email'] == '1') ? 'req_email' : 'email';
        if (!isset($_POST[$email_form_name]))
        {
            $email = $cur_post['poster_email'];
        }
?>						<label class="conl"><?php echo $email_label ?><br /><input type="text" name="<?php echo $email_form_name ?>" value="<?php echo pun_htmlspecialchars($email); ?>" size="50" maxlength="50" tabindex="<?php echo $cur_index++ ?>" /><br /></label>
<?php
    }
?>						<div class="clearer"></div>
<?php
}

?>					<div class="infldset txtarea">
<?php if ($can_edit_subject): ?>                      <label><?php echo $subject_title; ?>
						<input class="longinput" type="<?php echo $input_type; ?>" name="req_subject" size="80" maxlength="100" tabindex="<?php echo $cur_index++ ?>" value="<?php echo pun_htmlspecialchars(isset($_POST['req_subject']) ? $_POST['req_subject'] : $cur_post['subject']) ?>" /><br /></label>
<?php endif; $bbcode_form = 'edit'; $bbcode_field = 'req_message'; require PUN_ROOT.'mod_easy_bbcode.php'; ?><label><strong><?php echo $lang_common['Message'] ?></strong><br />
						<textarea id="req_message" name="req_message" rows="15" cols="95" tabindex="<?php echo $cur_index++ ?>"><?php echo pun_htmlspecialchars(isset($_POST['req_message']) ? $message : $cur_post['message']) ?></textarea><br /></label>
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
{
	if (isset($_POST['hide_smilies']) || $cur_post['hide_smilies'] == '1')
		$checkboxes[] = '<label><input type="checkbox" name="hide_smilies" value="1" checked="checked" tabindex="'.($cur_index++).'" />&nbsp;'.$lang_post['Hide smilies'];
	else
		$checkboxes[] = '<label><input type="checkbox" name="hide_smilies" value="1" tabindex="'.($cur_index++).'" />&nbsp;'.$lang_post['Hide smilies'];
}

if ($is_admmod)
{
	if ((isset($_POST['form_sent']) && isset($_POST['silent'])) || !isset($_POST['form_sent']))
		$checkboxes[] = '<label><input type="checkbox" name="silent" value="1" tabindex="'.($cur_index++).'" checked="checked" />&nbsp;'.$lang_post['Silent edit'];
	else
		$checkboxes[] = '<label><input type="checkbox" name="silent" value="1" tabindex="'.($cur_index++).'" />&nbsp;'.$lang_post['Silent edit'];
}

if (!empty($checkboxes))
{

?>
			</div>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_common['Options'] ?></legend>
					<div class="infldset">
						<div class="rbox">
							<?php echo implode('</label>'."\n\t\t\t\t\t\t\t", $checkboxes).'</label>'."\n" ?>
						</div>
					</div>
				</fieldset>
<?php

	}

?>
			</div>
            <p><input type="submit" name="preview" value="<?php echo $lang_post['Preview'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="p" />
            <input type="submit" name="submit" value="<?php echo $lang_common['Submit'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="s" /></p>
		</form>
	</div>
</div>
<?php

require PUN_ROOT.'footer.php';
