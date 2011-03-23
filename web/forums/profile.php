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

require_once SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP .
             DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

$sf_id = sfContext::getInstance()->getUser()->getId();

define('PUN_ROOT', './');
require PUN_ROOT.'include/common.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;
$section = isset($_GET['section']) ? $_GET['section'] : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : $sf_id; // instead of 0 (punbb default), get $sf_user Id;
if ($id < 2)
	message($lang_common['Bad request']);

if(($pun_user['is_guest'] 
    ||  ($pun_user['id'] != $id && $pun_user['g_id'] != PUN_ADMIN ) 
    || $section == null)
    && ($action != 'upload_avatar' && $action != 'upload_avatar2' && $action != 'delete_avatar'))
    redirect('/users/'.$id, $lang_profile['Profile redirect']);  

if ($pun_user['g_read_board'] == '0' && ($action != 'change_pass' || !isset($_GET['key'])))
	message($lang_common['No view']);

// Load the profile.php/register.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/prof_reg.php';

// Load the profile.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/profile.php';


if ($action == 'upload_avatar' || $action == 'upload_avatar2')
{
	if ($pun_config['o_avatars'] == '0')
		message($lang_profile['Avatars disabled']);

	if ($pun_user['id'] != $id && $pun_user['g_id'] > PUN_MOD)
		message($lang_common['No permission']);

	if (isset($_POST['form_sent']))
	{
		if (!isset($_FILES['req_file']))
			message($lang_profile['No file']);
			
		$uploaded_file = $_FILES['req_file'];

		// Make sure the upload went smooth
		if (isset($uploaded_file['error']))
		{
			switch ($uploaded_file['error'])
			{
				case 1:	// UPLOAD_ERR_INI_SIZE
				case 2:	// UPLOAD_ERR_FORM_SIZE
					message($lang_profile['Too large ini']);
					break;

				case 3:	// UPLOAD_ERR_PARTIAL
					message($lang_profile['Partial upload']);
					break;

				case 4:	// UPLOAD_ERR_NO_FILE
					message($lang_profile['No file']);
					break;

				case 6:	// UPLOAD_ERR_NO_TMP_DIR
					message($lang_profile['No tmp directory']);
					break;

				default:
					// No error occured, but was something actually uploaded?
					if ($uploaded_file['size'] == 0)
						message($lang_profile['No file']);
					break;
			}
		}

		if (is_uploaded_file($uploaded_file['tmp_name']))
		{
			$allowed_types = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
			if (!in_array($uploaded_file['type'], $allowed_types))
				message($lang_profile['Bad type']);

			// Make sure the file isn't too big
			if ($uploaded_file['size'] > $pun_config['o_avatars_size'])
				message($lang_profile['Too large'].' '.$pun_config['o_avatars_size'].' '.$lang_profile['bytes'].'.');

			// Determine type
			$extensions = null;
			if ($uploaded_file['type'] == 'image/gif')
				$extensions = array('.gif', '.jpg', '.png');
			else if ($uploaded_file['type'] == 'image/jpeg' || $uploaded_file['type'] == 'image/pjpeg')
				$extensions = array('.jpg', '.gif', '.png');
			else
				$extensions = array('.png', '.gif', '.jpg');

			// Move the file to the avatar directory. We do this before checking the width/height to circumvent open_basedir restrictions.
			if (!@move_uploaded_file($uploaded_file['tmp_name'], $pun_config['o_avatars_dir'].'/'.$id.'.tmp'))
				message($lang_profile['Move failed'].' <a href="mailto:'.$pun_config['o_admin_email'].'">'.$pun_config['o_admin_email'].'</a>.');

			// Now check the width/height
			list($width, $height, $type,) = getimagesize($pun_config['o_avatars_dir'].'/'.$id.'.tmp');
			if (empty($width) || empty($height) || $width > $pun_config['o_avatars_width'] || $height > $pun_config['o_avatars_height'])
			{
				@unlink($pun_config['o_avatars_dir'].'/'.$id.'.tmp');
				message($lang_profile['Too wide or high'].' '.$pun_config['o_avatars_width'].'x'.$pun_config['o_avatars_height'].' '.$lang_profile['pixels'].'.');
			}
			else if ($type == 1 && $uploaded_file['type'] != 'image/gif')	// Prevent dodgy uploads
			{
				@unlink($pun_config['o_avatars_dir'].'/'.$id.'.tmp');
				message($lang_profile['Bad type']);
			}			

			// Delete any old avatars and put the new one in place
			@unlink($pun_config['o_avatars_dir'].'/'.$id.$extensions[0]);
			@unlink($pun_config['o_avatars_dir'].'/'.$id.$extensions[1]);
			@unlink($pun_config['o_avatars_dir'].'/'.$id.$extensions[2]);
			@rename($pun_config['o_avatars_dir'].'/'.$id.'.tmp', $pun_config['o_avatars_dir'].'/'.$id.$extensions[0]);
			@chmod($pun_config['o_avatars_dir'].'/'.$id.$extensions[0], 0644);
		}
		else
			message($lang_profile['Unknown failure']);

		// Enable use_avatar (seems sane since the user just uploaded an avatar)
		$db->query('UPDATE '.$db->prefix.'users SET use_avatar=1 WHERE id='.$id) or error('Unable to update avatar state', __FILE__, __LINE__, $db->error());

		redirect('profile.php?section=personality&amp;id='.$id, $lang_profile['Avatar upload redirect']);
	}

	$page_title = pun_htmlspecialchars($pun_config['o_board_title']).' / '.$lang_common['Profile'];
	$required_fields = array('req_file' => $lang_profile['File']);
	$focus_element = array('upload_avatar', 'req_file');
	require PUN_ROOT.'header.php';

?>
<div class="blockform">
	<h2><span><?php echo $lang_profile['Upload avatar'] ?></span></h2>
	<div class="box">
		<form id="upload_avatar" method="post" enctype="multipart/form-data" action="profile.php?action=upload_avatar2&amp;id=<?php echo $id ?>" onsubmit="return process_form(this)">
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_profile['Upload avatar legend'] ?></legend>
					<div class="infldset">
						<input type="hidden" name="form_sent" value="1" />
						<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $pun_config['o_avatars_size'] ?>" />
						<label><strong><?php echo $lang_profile['File'] ?></strong><br /><input name="req_file" type="file" size="40" /><br /></label>
						<p><?php echo $lang_profile['Avatar desc'].' '.$pun_config['o_avatars_width'].' x '.$pun_config['o_avatars_height'].' '.$lang_profile['pixels'].' '.$lang_common['and'].' '.$pun_config['o_avatars_size'].' '.$lang_profile['bytes'].' ('.ceil($pun_config['o_avatars_size'] / 1024) ?> KB).</p>
					</div>
				</fieldset>
			</div>
			<p><input type="submit" name="upload" value="<?php echo $lang_profile['Upload'] ?>" /><a href="javascript:history.go(-1)"><?php echo $lang_common['Go back'] ?></a></p>
		</form>
	</div>
</div>
<?php

	require PUN_ROOT.'footer.php';
}


else if ($action == 'delete_avatar')
{
	if ($pun_user['id'] != $id && $pun_user['g_id'] > PUN_MOD)
		message($lang_common['No permission']);

	confirm_referrer('profile.php');

	@unlink($pun_config['o_avatars_dir'].'/'.$id.'.jpg');
	@unlink($pun_config['o_avatars_dir'].'/'.$id.'.png');
	@unlink($pun_config['o_avatars_dir'].'/'.$id.'.gif');

	// Disable use_avatar
	$db->query('UPDATE '.$db->prefix.'users SET use_avatar=0 WHERE id='.$id) or error('Unable to update avatar state', __FILE__, __LINE__, $db->error());

	redirect('profile.php?section=personality&amp;id='.$id, $lang_profile['Avatar deleted redirect']);
}


else if (isset($_POST['update_group_membership']))
{
	if ($pun_user['g_id'] > PUN_ADMIN)
		message($lang_common['No permission']);

	confirm_referrer('profile.php');

	$new_group_id = intval($_POST['group_id']);

	$db->query('UPDATE '.$db->prefix.'users SET group_id='.$new_group_id.' WHERE id='.$id) or error('Unable to change user group', __FILE__, __LINE__, $db->error());

	// If the user was a moderator or an administrator, we remove him/her from the moderator list in all forums as well
	if ($new_group_id > PUN_MOD)
	{
		$result = $db->query('SELECT id, moderators FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());

		while ($cur_forum = $db->fetch_assoc($result))
		{
			$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

			if (in_array($id, $cur_moderators))
			{
				$username = array_search($id, $cur_moderators);
				unset($cur_moderators[$username]);
				$cur_moderators = (!empty($cur_moderators)) ? '\''.$db->escape(serialize($cur_moderators)).'\'' : 'NULL';

				$db->query('UPDATE '.$db->prefix.'forums SET moderators='.$cur_moderators.' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
			}
		}
	}

	redirect('profile.php?section=admin&amp;id='.$id, $lang_profile['Group membership redirect']);
}


else if (isset($_POST['update_forums']))
{
	if ($pun_user['g_id'] > PUN_ADMIN)
		message($lang_common['No permission']);

	confirm_referrer('profile.php');

	// Get the username of the user we are processing
	$result = $db->query('SELECT username FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	$username = $db->result($result);

	$moderator_in = (isset($_POST['moderator_in'])) ? array_keys($_POST['moderator_in']) : array();

	// Loop through all forums
	$result = $db->query('SELECT id, moderators FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());

	while ($cur_forum = $db->fetch_assoc($result))
	{
		$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();
		// If the user should have moderator access (and he/she doesn't already have it)
		if (in_array($cur_forum['id'], $moderator_in) && !in_array($id, $cur_moderators))
		{
			$cur_moderators[$username] = $id;
			ksort($cur_moderators);

			$db->query('UPDATE '.$db->prefix.'forums SET moderators=\''.$db->escape(serialize($cur_moderators)).'\' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
		}
		// If the user shouldn't have moderator access (and he/she already has it)
		else if (!in_array($cur_forum['id'], $moderator_in) && in_array($id, $cur_moderators))
		{
            $username_old = array_search($id, $cur_moderators);
			unset($cur_moderators[$username_old]);
			$cur_moderators = (!empty($cur_moderators)) ? '\''.$db->escape(serialize($cur_moderators)).'\'' : 'NULL';

			$db->query('UPDATE '.$db->prefix.'forums SET moderators='.$cur_moderators.' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
		}
	}

	redirect('profile.php?section=admin&amp;id='.$id, $lang_profile['Update forums redirect']);
}


else if (isset($_POST['ban']))
{
	if ($pun_user['g_id'] > PUN_MOD || ($pun_user['g_id'] == PUN_MOD && $pun_config['p_mod_ban_users'] == '0'))
		message($lang_common['No permission']);

	redirect('admin_bans.php?add_ban='.$id, $lang_profile['Ban redirect']);
}
else if (isset($_POST['form_sent']))
{
	// Fetch the user group of the user we are editing
	$result = $db->query('SELECT group_id FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	if (!$db->num_rows($result))
		message($lang_common['Bad request']);

	$group_id = $db->result($result);

	if ($pun_user['id'] != $id &&
		($pun_user['g_id'] > PUN_MOD ||
		($pun_user['g_id'] == PUN_MOD && $pun_config['p_mod_edit_users'] == '0') ||
		($pun_user['g_id'] == PUN_MOD && $group_id < PUN_GUEST)))
		message($lang_common['No permission']);

	if ($pun_user['g_id'] < PUN_GUEST)
		confirm_referrer('profile.php');

	// Extract allowed elements from $_POST['form']
	function extract_elements($allowed_elements)
	{
		$form = array();

		while (list($key, $value) = @each($_POST['form']))
		{
		    if (in_array($key, $allowed_elements))
		        $form[$key] = $value;
		}

		return $form;
	}

	$username_updated = false;

	// Validate input depending on section
	switch ($section)
	{

		case 'display':
		{
			$form = extract_elements(array('disp_topics', 'disp_posts', 'show_smilies', 'show_img', 'show_img_sig', 'show_avatars', 'show_sig', 'style'));

			if ($form['disp_topics'] != '' && intval($form['disp_topics']) < 3) $form['disp_topics'] = 3;
			if ($form['disp_topics'] != '' && intval($form['disp_topics']) > 75) $form['disp_topics'] = 75;
			if ($form['disp_posts'] != '' && intval($form['disp_posts']) < 3) $form['disp_posts'] = 3;
			if ($form['disp_posts'] != '' && intval($form['disp_posts']) > 75) $form['disp_posts'] = 75;

			if (!isset($form['show_smilies']) || $form['show_smilies'] != '1') $form['show_smilies'] = '0';
			if (!isset($form['show_img']) || $form['show_img'] != '1') $form['show_img'] = '0';
			if (!isset($form['show_img_sig']) || $form['show_img_sig'] != '1') $form['show_img_sig'] = '0';
			if (!isset($form['show_avatars']) || $form['show_avatars'] != '1') $form['show_avatars'] = '0';
			if (!isset($form['show_sig']) || $form['show_sig'] != '1') $form['show_sig'] = '0';

			break;
		}
		case 'personality':
		{
			$form = extract_elements(array('use_avatar'));

			// Clean up signature from POST
			$form['signature'] = pun_linebreaks(trim($_POST['signature']));

			// Validate signature
			if (pun_strlen($form['signature']) > $pun_config['p_sig_length'])
				message($lang_prof_reg['Sig too long'].' '.$pun_config['p_sig_length'].' '.$lang_prof_reg['characters'].'.');
			else if (substr_count($form['signature'], "\n") > ($pun_config['p_sig_lines']-1))
				message($lang_prof_reg['Sig too many lines'].' '.$pun_config['p_sig_lines'].' '.$lang_prof_reg['lines'].'.');
			else if ($form['signature'] && $pun_config['p_sig_all_caps'] == '0' && strtoupper($form['signature']) == $form['signature'] && $pun_user['g_id'] > PUN_MOD)
				$form['signature'] = ucfirst(strtolower($form['signature']));

			// Validate BBCode syntax
            require PUN_ROOT.'include/parser.php';
			if ($pun_config['p_sig_bbcode'] == '1' && strpos($form['signature'], '[') !== false && strpos($form['signature'], ']') !== false)
			{
				$form['signature'] = preparse_bbcode($form['signature'], $foo, true);
			}
            $form['signature'] = preparse_url($form['signature']);

			if (!isset($form['use_avatar']) || $form['use_avatar'] != '1') $form['use_avatar'] = '0';

			break;
		}
		default:
			message($lang_common['Bad request']);
	}


	// Singlequotes around non-empty values and NULL for empty values
	$temp = array();
	while (list($key, $input) = @each($form))
	{
		$value = ($input !== '') ? '\''.$db->escape($input).'\'' : 'NULL';

		$temp[] = $key.'='.$value;
	}

	if (empty($temp))
		message($lang_common['Bad request']);


	$db->query('UPDATE '.$db->prefix.'users SET '.implode(',', $temp).' WHERE id='.$id) or error('Unable to update profile', __FILE__, __LINE__, $db->error());

	// If we changed the username we have to update some stuff
	if ($username_updated)
	{
		$db->query('UPDATE '.$db->prefix.'posts SET poster=\''.$db->escape($form['username']).'\' WHERE poster_id='.$id) or error('Unable to update posts', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'topics SET poster=\''.$db->escape($form['username']).'\' WHERE poster=\''.$db->escape($old_username).'\'') or error('Unable to update topics', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'topics SET last_poster=\''.$db->escape($form['username']).'\' WHERE last_poster=\''.$db->escape($old_username).'\'') or error('Unable to update topics', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'forums SET last_poster=\''.$db->escape($form['username']).'\' WHERE last_poster=\''.$db->escape($old_username).'\'') or error('Unable to update forums', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'online SET ident=\''.$db->escape($form['username']).'\' WHERE ident=\''.$db->escape($old_username).'\'') or error('Unable to update online list', __FILE__, __LINE__, $db->error());

		// If the user is a moderator or an administrator we have to update the moderator lists
		$result = $db->query('SELECT group_id FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
		$group_id = $db->result($result);

		if ($group_id < PUN_GUEST)
		{
			$result = $db->query('SELECT id, moderators FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());

			while ($cur_forum = $db->fetch_assoc($result))
			{
				$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

				if (in_array($id, $cur_moderators))
				{
					unset($cur_moderators[$old_username]);
					$cur_moderators[$form['username']] = $id;
					ksort($cur_moderators);

					$db->query('UPDATE '.$db->prefix.'forums SET moderators=\''.$db->escape(serialize($cur_moderators)).'\' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
				}
			}
		}
	}

	redirect('profile.php?section='.$section.'&amp;id='.$id, $lang_profile['Profile redirect']);
}


$result = $db->query('SELECT u.username, u.email, u.title, u.realname, u.url, u.jabber, u.icq, u.msn, u.aim, u.yahoo, u.location, u.use_avatar, u.signature, u.disp_topics, u.disp_posts, u.email_setting, u.save_pass, u.notify_with_post, u.show_smilies, u.show_img, u.show_img_sig, u.show_avatars, u.show_sig, u.timezone, u.language, u.style, u.num_posts, u.last_post, u.registered, u.registration_ip, u.admin_note, g.g_id, g.g_user_title FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result))
	message($lang_common['Bad request']);

$user = $db->fetch_assoc($result);

$last_post = format_time($user['last_post']);

if ($user['signature'] != '')
{
	require PUN_ROOT.'include/parser.php';
	$parsed_signature = parse_signature($user['signature']);
}


// View or edit?
if ($pun_user['id'] != $id &&
	($pun_user['g_id'] > PUN_MOD ||
	($pun_user['g_id'] == PUN_MOD && $pun_config['p_mod_edit_users'] == '0') ||
	($pun_user['g_id'] == PUN_MOD && $user['g_id'] < PUN_GUEST)))
{
	if ($user['email_setting'] == '0' && !$pun_user['is_guest'])
		$email_field = '<a href="mailto:'.$user['email'].'">'.$user['email'].'</a>';
	else if ($user['email_setting'] == '1' && !$pun_user['is_guest'])
		$email_field = '<a href="misc.php?email='.$id.'">'.$lang_common['Send e-mail'].'</a>';
	else
		$email_field = $lang_profile['Private'];

	$user_title_field = get_title($user);

	if ($user['url'] != '')
	{
		$user['url'] = pun_htmlspecialchars($user['url']);

		if ($pun_config['o_censoring'] == '1')
			$user['url'] = censor_words($user['url']);

		$url = '<a href="'.$user['url'].'">'.$user['url'].'</a>';
	}
	else
		$url = $lang_profile['Unknown'];

	if ($pun_config['o_avatars'] == '1')
	{
		if ($user['use_avatar'] == '1')
		{
			if ($img_size = @getimagesize($pun_config['o_avatars_dir'].'/'.$id.'.gif'))
				$avatar_field = '<img src="'.PUN_STATIC_URL.'/forums/'.$pun_config['o_avatars_dir'].'/'.$id.'.gif" '.$img_size[3].' alt="" />';
			else if ($img_size = @getimagesize($pun_config['o_avatars_dir'].'/'.$id.'.jpg'))
				$avatar_field = '<img src="'.PUN_STATIC_URL.'/forums/'.$pun_config['o_avatars_dir'].'/'.$id.'.jpg" '.$img_size[3].' alt="" />';
			else if ($img_size = @getimagesize($pun_config['o_avatars_dir'].'/'.$id.'.png'))
				$avatar_field = '<img src="'.PUN_STATIC_URL.'/forums/'.$pun_config['o_avatars_dir'].'/'.$id.'.png" '.$img_size[3].' alt="" />';
			else
				$avatar_field = $lang_profile['No avatar'];
		}
		else
			$avatar_field = $lang_profile['No avatar'];
	}

	$posts_field = '';
	if ($pun_config['o_show_post_count'] == '1' || $pun_user['g_id'] < PUN_GUEST)
		$posts_field = $user['num_posts'];
	if ($pun_user['g_search'] == '1')
		$posts_field .= (($posts_field != '') ? ' - ' : '').'<a href="search.php?action=show_user&amp;user_id='.$id.'">'.$lang_profile['Show posts'].'</a>';

	$page_title = pun_htmlspecialchars($pun_config['o_board_title']).' / '.$lang_common['Profile'];
	define('PUN_ALLOW_INDEX', 1);
	require PUN_ROOT.'header.php';

?>
<div id="viewprofile" class="block">
	<h2><span><?php echo $lang_common['Profile'] ?></span></h2>
	<div class="box">
		<div class="fakeform">
			<div class="inform">
				<fieldset>
				<legend><?php echo $lang_profile['Section personality'] ?></legend>
					<div class="infldset">
						<dl>
<?php if ($pun_config['o_avatars'] == '1'): ?>							<dt><?php echo $lang_profile['Avatar'] ?>: </dt>
							<dd><?php echo $avatar_field ?></dd>
<?php endif; ?>							<dt><?php echo $lang_profile['Signature'] ?>: </dt>
							<dd><div><?php echo isset($parsed_signature) ? $parsed_signature : $lang_profile['No sig']; ?></div></dd>
						</dl>
						<div class="clearer"></div>
					</div>
				</fieldset>
			</div>
		</div>
	</div>
</div>

<?php

	require PUN_ROOT.'footer.php';
}
else
{
	if ($section == 'display')
	{
		$page_title = pun_htmlspecialchars($pun_config['o_board_title']).' / '.$lang_common['Profile'];
		require PUN_ROOT.'header.php';

		generate_profile_menu('display');

?>
	<div class="blockform">
		<h2><span><?php echo pun_htmlspecialchars($user['username']).' - '.$lang_profile['Section display'] ?></span></h2>
		<div class="box">
			<form id="profile5" method="post" action="profile.php?section=display&amp;id=<?php echo $id ?>">
				<div><input type="hidden" name="form_sent" value="1" /></div>
<?php

		$styles = array();
		$d = dir(PUN_ROOT.'style');
		while (($entry = $d->read()) !== false)
		{
			if (substr($entry, strlen($entry)-4) == '.css')
				$styles[] = substr($entry, 0, strlen($entry)-4);
		}
		$d->close();

?>
				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_profile['Post display legend'] ?></legend>
						<div class="infldset">
							<p><?php echo $lang_profile['Post display info'] ?></p>
							<div class="rbox">
								<label><input type="checkbox" name="form[show_smilies]" value="1"<?php if ($user['show_smilies'] == '1') echo ' checked="checked"' ?> /><?php echo $lang_profile['Show smilies'] ?><br /></label>
								<label><input type="checkbox" name="form[show_sig]" value="1"<?php if ($user['show_sig'] == '1') echo ' checked="checked"' ?> /><?php echo $lang_profile['Show sigs'] ?><br /></label>
<?php if ($pun_config['o_avatars'] == '1'): ?>							<label><input type="checkbox" name="form[show_avatars]" value="1"<?php if ($user['show_avatars'] == '1') echo ' checked="checked"' ?> /><?php echo $lang_profile['Show avatars'] ?><br /></label>
<?php endif; ?>								<label><input type="checkbox" name="form[show_img]" value="1"<?php if ($user['show_img'] == '1') echo ' checked="checked"' ?> /><?php echo $lang_profile['Show images'] ?><br /></label>
								<label><input type="checkbox" name="form[show_img_sig]" value="1"<?php if ($user['show_img_sig'] == '1') echo ' checked="checked"' ?> /><?php echo $lang_profile['Show images sigs'] ?><br /></label>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_profile['Pagination legend'] ?></legend>
						<div class="infldset">
							<label class="conl"><?php echo $lang_profile['Topics per page'] ?><br /><input type="text" name="form[disp_topics]" value="<?php echo $user['disp_topics'] ?>" size="6" maxlength="3" /><br /></label>
							<label class="conl"><?php echo $lang_profile['Posts per page'] ?><br /><input type="text" name="form[disp_posts]" value="<?php echo $user['disp_posts'] ?>" size="6" maxlength="3" /><br /></label>
							<p class="clearb"><?php echo $lang_profile['Paginate info'] ?> <?php echo $lang_profile['Leave blank'] ?></p>
						</div>
					</fieldset>
				</div>
				<p><input type="submit" name="update" value="<?php echo $lang_common['Submit'] ?>" />  <?php echo $lang_profile['Instructions'] ?></p>
			</form>
		</div>
	</div>
<?php

	}else if (!$section || $section == 'personality')
	{
		$avatar_field = '<a href="profile.php?action=upload_avatar&amp;id='.$id.'">'.$lang_profile['Change avatar'].'</a>';
		if ($img_size = @getimagesize($pun_config['o_avatars_dir'].'/'.$id.'.gif'))
			$avatar_format = 'gif';
		else if ($img_size = @getimagesize($pun_config['o_avatars_dir'].'/'.$id.'.jpg'))
			$avatar_format = 'jpg';
		else if ($img_size = @getimagesize($pun_config['o_avatars_dir'].'/'.$id.'.png'))
			$avatar_format = 'png';
		else
			$avatar_field = '<a href="profile.php?action=upload_avatar&amp;id='.$id.'">'.$lang_profile['Upload avatar'].'</a>';

		// Display the delete avatar link?
		if ($img_size)
			$avatar_field .= '&nbsp;&nbsp;&nbsp;<a href="profile.php?action=delete_avatar&amp;id='.$id.'">'.$lang_profile['Delete avatar'].'</a>';

		if ($user['signature'] != '')
			$signature_preview = '<p>'.$lang_profile['Sig preview'].'</p>'."\n\t\t\t\t\t".'<div class="postsignature">'."\n\t\t\t\t\t\t".'<hr />'."\n\t\t\t\t\t\t".$parsed_signature."\n\t\t\t\t\t".'</div>'."\n";
		else
			$signature_preview = '<p>'.$lang_profile['No sig'].'</p>'."\n";

		$page_title = pun_htmlspecialchars($pun_config['o_board_title']).' / '.$lang_common['Profile'];
		require PUN_ROOT.'header.php';

		generate_profile_menu('personality');


?>
	<div class="blockform">
		<h2><span><?php echo pun_htmlspecialchars($user['username']).' - '.$lang_profile['Section personality'] ?></span></h2>
		<div class="box">
			<form id="profile4" method="post" action="profile.php?section=personality&amp;id=<?php echo $id ?>">
				<div><input type="hidden" name="form_sent" value="1" /></div>
<?php if ($pun_config['o_avatars'] == '1'): ?>				<div class="inform">
					<fieldset id="profileavatar">
						<legend><?php echo $lang_profile['Avatar legend'] ?></legend>
						<div class="infldset">
<?php if (isset($avatar_format)): ?>					<img src="<?php echo PUN_STATIC_URL.'/forums/'.$pun_config['o_avatars_dir'].'/'.$id.'.'.$avatar_format ?>" <?php echo $img_size[3] ?> alt="" />
<?php endif; ?>					<p><?php echo $lang_profile['Avatar info'] ?></p>
							<div class="rbox">
								<label><input type="checkbox" name="form[use_avatar]" value="1"<?php if ($user['use_avatar'] == '1') echo ' checked="checked"' ?> /><?php echo $lang_profile['Use avatar'] ?><br /></label>
							</div>
							<p class="clearb"><?php echo $avatar_field ?></p>
						</div>
					</fieldset>
				</div>
<?php endif; ?>				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_profile['Signature legend'] ?></legend>
						<div class="infldset">
							<p><?php echo $lang_profile['Signature info'] ?></p>
							<div class="txtarea">
								<label><?php echo $lang_profile['Sig max length'] ?>: <?php echo $pun_config['p_sig_length'] ?> / <?php echo $lang_profile['Sig max lines'] ?>: <?php echo $pun_config['p_sig_lines'] ?><br />
								<textarea name="signature" rows="4" cols="65"><?php echo pun_htmlspecialchars($user['signature']) ?></textarea><br /></label>
							</div>
							<ul class="bblinks">
								<li><a href="help.php#bbcode" onclick="window.open(this.href); return false;"><?php echo $lang_common['BBCode'] ?></a>: <?php echo ($pun_config['p_sig_bbcode'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
								<li><a href="help.php#img" onclick="window.open(this.href); return false;"><?php echo $lang_common['img tag'] ?></a>: <?php echo ($pun_config['p_sig_img_tag'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
								<li><a href="help.php#smilies" onclick="window.open(this.href); return false;"><?php echo $lang_common['Smilies'] ?></a>: <?php echo ($pun_config['o_smilies_sig'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
							</ul>
							<?php echo $signature_preview ?>
						</div>
					</fieldset>
				</div>
				<p><input type="submit" name="update" value="<?php echo $lang_common['Submit'] ?>" /><?php echo $lang_profile['Instructions'] ?></p>
			</form>
		</div>
	</div>
<?php

	}
	else if ($section == 'admin')
	{
		if ($pun_user['g_id'] > PUN_MOD || ($pun_user['g_id'] == PUN_MOD && $pun_config['p_mod_ban_users'] == '0'))
			message($lang_common['Bad request']);

		$page_title = pun_htmlspecialchars($pun_config['o_board_title']).' / '.$lang_common['Profile'];
		require PUN_ROOT.'header.php';

		generate_profile_menu('admin');

?>
	<div class="blockform">
		<h2><span><?php echo pun_htmlspecialchars($user['username']).' - '.$lang_profile['Section admin'] ?></span></h2>
		<div class="box">
			<form id="profile7" method="post" action="profile.php?section=admin&amp;id=<?php echo $id ?>&amp;action=foo">
				<div class="inform">
				<input type="hidden" name="form_sent" value="1" />
					<fieldset>
<?php

		if ($pun_user['g_id'] == PUN_MOD)
		{

?>
						<legend><?php echo $lang_profile['Delete ban legend'] ?></legend>
						<div class="infldset">
							<p><input type="submit" name="ban" value="<?php echo $lang_profile['Ban user'] ?>" /></p>
						</div>
					</fieldset>
				</div>
<?php

		}
		else
		{
			if ($pun_user['id'] != $id)
			{

?>
						<legend><?php echo $lang_profile['Group membership legend'] ?></legend>
						<div class="infldset">
							<select id="group_id" name="group_id">
<?php

				$result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups WHERE g_id!='.PUN_GUEST.' ORDER BY g_title') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

				while ($cur_group = $db->fetch_assoc($result))
				{
					if ($cur_group['g_id'] == $user['g_id'] || ($cur_group['g_id'] == $pun_config['o_default_user_group'] && $user['g_id'] == ''))
						echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected="selected">'.pun_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
					else
						echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.pun_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
				}

?>
							</select>
							<input type="submit" name="update_group_membership" value="<?php echo $lang_profile['Save'] ?>" />
						</div>
					</fieldset>
				</div>
				<div class="inform">
					<fieldset>
<?php

			}

?>
						<legend><?php echo $lang_profile['Delete ban legend'] ?></legend>
						<div class="infldset">
							<input type="submit" name="delete_user" value="<?php echo $lang_profile['Delete user'] ?>" />&nbsp;&nbsp;<input type="submit" name="ban" value="<?php echo $lang_profile['Ban user'] ?>" />
						</div>
					</fieldset>
				</div>
<?php

			if ($user['g_id'] == PUN_MOD || $user['g_id'] == PUN_ADMIN)
			{

?>
				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_profile['Set mods legend'] ?></legend>
						<div class="infldset">
							<p><?php echo $lang_profile['Moderator in info'] ?></p>
<?php

				$result = $db->query('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.moderators FROM '.$db->prefix.'categories AS c INNER JOIN '.$db->prefix.'forums AS f ON c.id=f.cat_id WHERE f.redirect_url IS NULL ORDER BY c.disp_position, c.id, f.disp_position') or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

				$cur_category = 0;
				while ($cur_forum = $db->fetch_assoc($result))
				{
					if ($cur_forum['cid'] != $cur_category)	// A new category since last iteration?
					{
						if ($cur_category)
							echo "\n\t\t\t\t\t\t\t\t".'</div>';

						if ($cur_category != 0)
							echo "\n\t\t\t\t\t\t\t".'</div>'."\n";

						echo "\t\t\t\t\t\t\t".'<div class="conl">'."\n\t\t\t\t\t\t\t\t".'<p><strong>'.$cur_forum['cat_name'].'</strong></p>'."\n\t\t\t\t\t\t\t\t".'<div class="rbox">';
						$cur_category = $cur_forum['cid'];
					}

					$moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

					echo "\n\t\t\t\t\t\t\t\t\t".'<label><input type="checkbox" name="moderator_in['.$cur_forum['fid'].']" value="1"'.((in_array($id, $moderators)) ? ' checked="checked"' : '').' />'.pun_htmlspecialchars($cur_forum['forum_name']).'<br /></label>'."\n";
				}

?>
								</div>
							</div>
							<br class="clearb" /><input type="submit" name="update_forums" value="<?php echo $lang_profile['Update forums'] ?>" />
						</div>
					</fieldset>
				</div>
<?php

			}
		}

?>
			</form>
		</div>
	</div>
<?php

	}

?>
	<div class="clearer"></div>
</div>
<?php

	require PUN_ROOT.'footer.php';
}
