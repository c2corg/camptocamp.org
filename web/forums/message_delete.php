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

if ($pun_user['is_guest'] || $pun_user['g_pm'] == 0)
	message($lang_common['No permission']);
	
if (empty($_GET['id']))
	message($lang_common['Bad request']);
$id = intval($_GET['id']);

// Load the delete.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/pms.php';
require PUN_ROOT.'lang/'.$pun_user['language'].'/delete.php';

// Fetch some info from the message we are deleting
$result = $db->query('SELECT * FROM '.$db->prefix.'messages WHERE id='.$id) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result))
	message($lang_common['Bad request']);

$cur_post = $db->fetch_assoc($result);

// Check permissions
if ($cur_post['owner'] != $pun_user['id'])
	message($lang_common['No permission']);

if (isset($_POST['delete']))
{
	// Check id
	if (empty($_GET['id']))
		message($lang_common['Bad request']);
	$id = intval($_GET['id']);
	
	confirm_referrer('message_delete.php');

	// Delete message
	$db->query('DELETE FROM '.$db->prefix.'messages WHERE id='.$id) or error('Unable to delete messages', __FILE__, __LINE__, $db->error());
	
	// Redirect
	redirect('message_list.php?box='.$_POST['box'].'&p='.$_POST['p'], $lang_pms['Del redirect']);
}
else
{
	$page_title = $lang_pms['Delete message'].' / '.pun_htmlspecialchars($pun_config['o_board_title']);
	
	require PUN_ROOT.'header.php';
	require PUN_ROOT.'include/parser.php';
	
	$cur_post['message'] = parse_message($cur_post['message'], (int)(!$cur_post['smileys']));
?>
<div class="blockform">
	<h2><span><?php echo $lang_pms['Delete message'] ?></span></h2>
	<div class="box">
		<form method="post" action="message_delete.php?id=<?php echo $id ?>">
		<input type="hidden" name="box" value="<?php echo (int)$_GET['box'] ?>">
		<input type="hidden" name="p" value="<?php echo (int)$_GET['p'] ?>">
			<div class="inform">
				<fieldset>
					<div class="infldset">
						<div class="postmsg">
							<p><?php echo $lang_pms['Sender'] ?>: <strong><?php echo pun_htmlspecialchars($cur_post['sender']) ?></strong></p>
							<?php echo $cur_post['message'] ?>
						</div>
					</div>
				</fieldset>
			</div>
			<p><input type="submit" name="delete" value="<?php echo $lang_delete['Delete'] ?>" /><a href="javascript:history.go(-1)"><?php echo $lang_common['Go back'] ?></a></p>
		</form>
	</div>
</div>
<?php

	require PUN_ROOT.'footer.php';
}
