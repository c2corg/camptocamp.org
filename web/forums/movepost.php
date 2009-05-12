<?php
/***********************************************************************

  Frédéric Pouget (fpouget at georezo dot net)

  This file is part of the mod: Move Post 1.2 for PunBB.

************************************************************************/

/***********************************************************************

  Copyright (C) 2002-2005  Rickard Andersson (rickard@punbb.org)

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

if ($pun_user['g_id'] > PUN_MOD)
	message($lang_common['No permission']);

if (isset($_GET['id']) || isset($_GET['ids']))
{
	if (isset($_GET['ids']))
	{
		$post_ids = $_GET['ids'];
		if (@preg_match('/[^0-9,]/', $post_ids))
			message($lang_common['Bad request']);
		if (strpos($post_ids, ',') === false)
		{
			// there is only one post to move, so no special multi-moves
			$post_id = $post_ids;
			unset($post_ids);
		}
		else
		{
			// we presume all the posts we want to move are from the same
			// topic. So, for the information, we just take the first id
			$post_id = intval(substr($post_ids, 0, strpos($post_ids, ',')));
		}
	}
	else
		$post_id = intval($_GET['id']);
	
	//Find the information from the original post
	$result = $db->query('SELECT p.message, t.id, t.subject, t.forum_id, f.forum_name FROM '.$db->prefix.'posts as p INNER JOIN '.$db->prefix.'topics as t ON p.topic_id=t.id INNER JOIN '.$db->prefix.'forums as f ON t.forum_id=f.id WHERE p.id='.$post_id) or error('Unable to find information for the post', __FILE__, __LINE__, $db->error());
	if (!$db->num_rows($result))
		message($lang_common['Bad request']);
		
	list($message, $old_topic_id, $subject, $old_fid, $forum_name) = $db->fetch_row($result);
	
	
	//Same forum or new one ?
	if (isset($_GET['new_fid']))
	{
		$fid =$new_fid = intval($_GET['new_fid']);
		
		if ($new_fid != $old_fid)
			$new_forum = TRUE;
		else
			$fid = $old_fid;
	}
	else
		$fid = $old_fid;
	
	$forum_id = $fid;
    $footer_style = 'moderate';
    
	// Load the movepost.php language file
	require PUN_ROOT.'lang/'.$pun_user['language'].'/movepost.php';


	if (isset($_POST['form_sent']))
		$form_sent = intval($_POST['form_sent']);
		
	if ($form_sent > 1)
	{
		$topic_radio = $_POST['topic_to_move'];
		$topic_plain = pun_trim($_POST['topic_to_move_plain']);
		if(($topic_plain && $topic_radio) || ($topic_plain && !is_numeric($topic_plain)))
			message($lang_movepost['Bad topic']);
		
		$new_topic_id = ($topic_plain) ? $topic_plain : $topic_radio;
			
		if ($new_topic_id == '' && $form_sent != 2)
			message($lang_movepost['Bad topic']);

		//Same forum or new one ? Needed if is a plain topic
		if($topic_plain)
		{
			$result = $db->query('SELECT forum_id FROM '.$db->prefix.'topics WHERE id='.$new_topic_id) or error('Unable to find information for the post', __FILE__, __LINE__, $db->error());
			if (!$db->num_rows($result))
				message($lang_common['Bad request']);
			
			$fid = $new_fid = $db->result($result);
			if ($new_fid != $old_fid)
				$new_forum = TRUE;
			else
				$fid = $old_fid;
		}

		// If we move several posts
        if (isset($post_ids))
        {
            $all_post_id = $post_ids;
        }
        
		// If we need to move all the posts
		else if (isset($_POST['move_all_post']))
		{
			$all_id=TRUE;

			// Get the posts ID
			$result = $db->query('SELECT id, message FROM '.$db->prefix.'posts WHERE topic_id='.$old_topic_id.' ORDER BY posted') or error('Unable to get all the posts', __FILE__, __LINE__, $db->error());
			if (!$db->num_rows($result))
				message($lang_common['Bad request']);
			else
			{
				//get the first post to check if it is $post_id
				list($first_post_id, $first_message) = $db->fetch_row($result); 
				if ($first_post_id != $post_id)
					{
						$post_id = $first_post_id;
						$message = $first_message;
					}
				
				// get all the others post_id
				$all_post_id = $post_id;
				while ($row = $db->fetch_row($result))
					$all_post_id .= ','.$row[0];
			}
		}


		// If it's a creation of a new topic
		if ($form_sent == 2)
		{
			$new_subject = pun_trim($_POST['create_topic']);
			
			if ($new_subject == '')
				message($form_sent.$lang_movepost['Bad new topic']);
			
			// Create the topic
			$db->query('INSERT INTO '.$db->prefix.'topics (subject, forum_id) VALUES(\''.$db->escape($new_subject).'\', '.$fid.')') or error('Unable to create topic', __FILE__, __LINE__, $db->error());
			$new_topic_id = $db->insert_id();
            $target_subject = $new_subject;
		}
		else
		{
			// Determine if the post will be in first position in the reception topic ($new_topic_id)
			$result = $db->query('SELECT p.id, p.message, t.subject FROM '.$db->prefix.'posts as p INNER JOIN '.$db->prefix.'topics as t ON p.topic_id=t.id WHERE p.topic_id='.$new_topic_id.' ORDER BY p.posted LIMIT 1') or error('Unable to get first post info', __FILE__, __LINE__, $db->error());
			$reception_num_post = $db->num_rows($result);
			if ($reception_num_post < 1)
				message($lang_common['Bad request']);
			
			list($reception_topic_first_post_id, $reception_first_message, $reception_subject) = $db->fetch_row($result);
			
			$is_reception_post_new = ($reception_topic_first_post_id > $post_id) ? true : false;
            $target_subject = $reception_subject;
		}


		// Determine whether this post is the first post of the old topic or not AND if there is a second post or not
		$result = $db->query('SELECT id, message FROM '.$db->prefix.'posts WHERE topic_id='.$old_topic_id.' ORDER BY posted LIMIT 2') or error('Unable to fetch first and second posts info', __FILE__, __LINE__, $db->error());
		$num_post = $db->num_rows($result);
		if ($num_post < 1)
			message($lang_common['Bad request']);
		
		list($first_post_id,) = $db->fetch_row($result);
		list($second_post_id, $second_message) = $db->fetch_row($result);
		
		if ($num_post == 1 && !isset($post_ids))
			$is_topic_post=TRUE;
			
		if ($num_post > 1  && $first_post_id == $post_id && !$all_id && !isset($post_ids))
			$is_second_post=TRUE;


		//Move post(s) in the new topic
		if ($all_id || isset($post_ids))
			$db->query('UPDATE '.$db->prefix.'posts SET topic_id='.$new_topic_id.' WHERE id IN('.$all_post_id.')') or error('Unable to move all the posts', __FILE__, __LINE__, $db->error());
		else
			$db->query('UPDATE '.$db->prefix.'posts SET topic_id='.$new_topic_id.' WHERE id='.$post_id) or error('Unable to move the post', __FILE__, __LINE__, $db->error());
		

		//Update tables search
		if($all_id || $is_topic_post || $is_second_post) //if the moved post was in first position in the original topic
		{
			require_once PUN_ROOT.'include/search_idx.php';

			strip_search_index($post_id); // remove the entries in the tables
			if ($new_subject)
				update_search_index('movepost', $post_id, $message,$new_subject); // update message and subject
			else
				update_search_index('movepost', $post_id, $message); // update only message
				
			if ($is_second_post) //if the next post is now in first position in the old topic
			{
				strip_search_index($second_post_id); 
				update_search_index('movepost', $second_post_id, $second_message,$subject);
			}
		}
		
		if ($is_reception_post_new) // if the moved post is in first position on the reception topic
		{
			require_once PUN_ROOT.'include/search_idx.php';
			
			// update the post which was first one on the reception topic (and second now)
			strip_search_index($reception_topic_first_post_id); 
			update_search_index('movepost', $reception_topic_first_post_id, $reception_first_message);
			
			// update the post which is now the first one on the reception topic
			strip_search_index($post_id); 
			update_search_index('movepost', $post_id, $message, $reception_subject);
		}

		
		//Update topics and forum if required
		update_topic($new_topic_id);
		
		if($is_topic_post || $all_id)
		{
			delete_topic($old_topic_id);
			update_forum($old_fid); // Update the forum FROM which the topic was moved
			
			if($new_forum)
				update_forum($new_fid);	// Update the forum FROM which the topic was moved
		}
		else
		{
			update_topic($old_topic_id);
			
			if($new_forum)
			{
				update_forum($old_fid);	// Update the forum FROM which the topic was moved
				update_forum($new_fid);	// Update the forum TO which the topic was moved
			}
		}
        
        
        // clear symfony cache for the corresponding docs view, diff, history.. - in order to have number of comments properly displayed
        if($old_fid == 1)
        {
            $doc_param = get_doc_param($subject);
            c2cTools::clearCommentCache($doc_param[0], $doc_param[1]);
        }
        if($fid == 1)
        {
            $doc_param = get_doc_param($target_subject);
            c2cTools::clearCommentCache($doc_param[0], $doc_param[1]);
        }


		redirect('viewtopic.php?pid='.$post_id.'#p'.$post_id, $lang_movepost['Mark move redirect']);
	}
	else // Display the form
	{
		// If multiple most must be move, you can't move all the posts at the same time
		if (!isset($post_ids))
		{
			// Count the number of posts in the topic
			$result = $db->query('SELECT count(id) FROM '.$db->prefix.'posts WHERE topic_id='.$old_topic_id) or error('Unable to count posts', __FILE__, __LINE__, $db->error());
			$num_post = $db->result($result);
		}
		
        //Count the topics to diplayed
        $result = $db->query('SELECT count(id) FROM '.$db->prefix.'topics WHERE forum_id ='.$fid.' AND moved_to IS NULL') or error('Unable to count topics', __FILE__, __LINE__, $db->error());
        $num_topics = $db->result($result);
    
        //Not add the original topic
        if ($fid == $old_fid)
            $num_topics = $num_topics-1;
		
		
		// Determine the topic offset (based on $_GET['p'])
		$num_pages = ceil($num_topics / $pun_user['disp_topics']);
		$p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : $_GET['p'];
		$start_from = $pun_user['disp_topics'] * ($p - 1);
		
		
		//Sort query (based on $_GET['new_fid'], $_GET['sort'] and $_GET['desc'])
		$var_query_img = $var_query = '';
		if (isset($_GET['new_fid']))
			$var_query_img .= $var_query .='&new_fid='.$fid;
	
		if (isset($_GET['sort']))
		{	
			$sort_list= $_GET['sort'];
			$var_query .='&sort='.$sort_list;
			
			if (isset($_GET['desc']))
			{
				$sort_list .=' DESC';
				$var_query .='&desc=1';
			}
		}
		else
			$sort_list= 'last_post DESC';
		
		
		// Generate paging links
		if (!isset($post_ids))
        {
            $post_id_param = 'id='.$post_id;
        }
        else
        {
            $post_id_param = 'ids='.$post_ids;
        }
        $paging_links = $lang_common['Pages'].': '.paginate($num_pages, $p, 'movepost.php?'.$post_id_param.$var_query);
		
		
		//The topic query
		$result_topic = $db->query('SELECT id, subject, poster, last_post, num_replies FROM '.$db->prefix.'topics WHERE moved_to IS NULL AND forum_id='.$fid.' AND id <> '.$old_topic_id.' ORDER BY '.$sort_list.' LIMIT '.$start_from.','.$pun_user['disp_topics']) or error('Unable to find topics', __FILE__, __LINE__, $db->error());
		
		
		require PUN_ROOT.'header.php';
		
		?>
<div class="blockform">
	<h2><span><?php echo $lang_movepost['Mod move post'] ?></span></h2>
	<div class="box">
		<form id="qjump" method="get" action="movepost.php?<?php echo $post_id_param ?>">
			<p><strong><?php echo '<a href="viewtopic.php?pid='.$post_id.'#p'.$post_id.'">'.$lang_common['Go back'].'</a>'; ?></strong></p><br />
			<fieldset>
				<legend><?php echo $lang_movepost['Introduction'] ?></legend>
				<div class="infldset">
					<p><?php echo $lang_movepost['Intro'] ?></p><br />
					<p><?php echo $lang_movepost['Original topic'].' <strong><a href="viewtopic.php?id='.$old_topic_id.'">'.pun_htmlspecialchars($subject).'</a></strong>'; ?></p>
					<p><?php echo $lang_movepost['Original forum'].' <strong><a href="viewforum.php?id='.$old_fid.'">'.pun_htmlspecialchars($forum_name).'</a></strong>'; ?></p>
					<p><?php echo $lang_movepost['Select forum'] ?>:</p>
					<div>
					<select name="id" onchange="window.location=('movepost.php?<?php echo $post_id_param ?>&new_fid='+this.options[this.selectedIndex].value)">
<?php 
	$result = $db->query('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name FROM '.$db->prefix.'categories AS c INNER JOIN '.$db->prefix.'forums AS f ON c.id=f.cat_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.redirect_url IS NULL ORDER BY c.disp_position, c.id, f.disp_position', true) or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());
		$cur_category = 0;
		while ($cur_forum = $db->fetch_assoc($result))
		{
			if ($cur_forum['cid'] != $cur_category)	// A new category since last iteration?
			{
				if ($cur_category)
					echo "\t\t\t\t\t\t".'</optgroup>'."\n";
				
				echo "\t\t\t\t\t\t".'<optgroup label="'.pun_htmlspecialchars($cur_forum['cat_name']).'">'."\n";
				$cur_category = $cur_forum['cid'];
			}
			
			echo "\t\t\t\t\t\t\t<option value=".$cur_forum['fid'];
				if ($cur_forum['fid'] == $fid) echo " selected='selected'";
			echo " >".pun_htmlspecialchars($cur_forum['forum_name'])."</option>\n";
		}
	?>
						</optgroup>
						</select>
					</div>
				</div>
			</fieldset>
			<input type="hidden" name="form_sent" value="1" />
		</form>
		<form id="movepost_create" method="post" action="movepost.php?<?php echo $post_id_param ?>&new_fid=<?php echo $fid ?>">
			<fieldset>
				<legend><?php echo $lang_movepost['Create topic'] ?></legend>
				<div class="infldset">
					<p><?php echo $lang_movepost['Explain create topic'] ?></p><br />
						<input name="create_topic" size="70" maxlength="100" tabindex="1" type="text" />
						<input name="save" value="<?php echo $lang_common['Submit'] ?>" type="submit" />
					<?php if (!isset($post_ids))
					{
					?>
					<br /><br />
					<p><?php echo $lang_movepost['Move all posts'] ?> (<strong><?php echo $num_post ?></strong>)</p>
					<div class="rbox">
						<label><input name="move_all_post" value="1" type="checkbox" /><?php echo $lang_movepost['Explain move all posts'] ?> <br /></label>
					</div>
					<?php } ?>
				</div>
			</fieldset>
			<input type="hidden" name="form_sent" value="2" />
		</form>
		<form id="movepost_move" method="post" action="movepost.php?<?php echo $post_id_param; if ($new_fid) echo '&new_fid='.$new_fid;?>">
			<fieldset>
				<legend><?php echo $lang_movepost['Move post'] ?></legend>
				<div class="infldset">
					<p><?php echo $lang_movepost['Move all posts'] ?> (<strong><?php echo $num_post ?></strong>)</p>
					<?php if (!isset($post_ids))
					{
					?>
					<div class="rbox">
						<label><input name="move_all_post" value="1" type="checkbox" /><?php echo $lang_movepost['Explain move all posts'] ?> <br /></label>
					</div><br />
					<hr />
					<?php } ?>
					<p><strong><?php echo $lang_movepost['Explain move post plain'] ?></strong></p><br />
						<input name="topic_to_move_plain" size="10" maxlength="10" tabindex="1" type="text" />
						<input name="save" value="<?php echo $lang_common['Submit'] ?>" type="submit" />
					<br /><br />
					<hr />
					<p><strong><?php echo $lang_movepost['Explain move post radio'] ?></strong></p><br />
					<p style="TEXT-ALIGN:center;"><input name="save" value="<?php echo $lang_common['Submit'] ?>" type="submit" /></p>
					<p class="pagelink conl"><?php echo $paging_links ?></p><p>&nbsp;</p>
					<table class="aligntop" cellspacing="0">
						<tr>
							<th class="tc2" scope="col">
								<input name="topic_to_move" value="" type="radio" />
							</th>
							<th scope="col">
								<strong><?php echo $lang_common['Topic'] ?></strong><br />
								<a href="movepost.php?<?php echo $post_id_param.$var_query_img ?>&sort=subject"><img alt="up" SRC="<?php echo PUN_STATIC_URL; ?>/forums/img/movepost/arrow_up.png"></a>
								<a href="movepost.php?<?php echo $post_id_param.$var_query_img ?>&sort=subject&desc=1"><img alt="down" SRC="<?php echo PUN_STATIC_URL; ?>/forums/img/movepost/arrow_down.png"></a>
							</th>
							<th scope="col">
								<strong><?php echo $lang_movepost['Poster'] ?></strong><br />
								<a href="movepost.php?<?php echo $post_id_param.$var_query_img ?>&sort=poster"><img alt="up" SRC="<?php echo PUN_STATIC_URL; ?>/forums/img/movepost/arrow_up.png"></a>
								<a href="movepost.php?<?php echo $post_id_param.$var_query_img ?>&sort=poster&desc=1"><img alt="down" SRC="<?php echo PUN_STATIC_URL; ?>/forums/img/movepost/arrow_down.png"></a>
							</th>
							<th scope="col">
								<strong><?php echo $lang_movepost['Last'] ?></strong><br />
								<a href="movepost.php?<?php echo $post_id_param.$var_query_img ?>&sort=last_post"><img alt="up" SRC="<?php echo PUN_STATIC_URL; ?>/forums/img/movepost/arrow_up.png"></a>
								<a href="movepost.php?<?php echo $post_id_param.$var_query_img ?>&sort=last_post&desc=1"><img alt="down" SRC="<?php echo PUN_STATIC_URL; ?>/forums/img/movepost/arrow_down.png"></a>
							</th>
							<th class="tc2" scope="col">
								<strong><?php echo $lang_common['Replies'] ?></strong><br />
								<a href="movepost.php?<?php echo $post_id_param.$var_query_img ?>&sort=num_replies"><img alt="up" SRC="<?php echo PUN_STATIC_URL; ?>/forums/img/movepost/arrow_up.png"></a>
								<a href="movepost.php?<?php echo $post_id_param.$var_query_img ?>&sort=num_replies&desc=1"><img alt="down" SRC="<?php echo PUN_STATIC_URL; ?>/forums/img/movepost/arrow_down.png"></a>
							</th>
						</tr>
<?php
		// If there are topics in this forum.
		if ($db->num_rows($result_topic))
		{
			while ($cur_topic = $db->fetch_assoc($result_topic))
			{
				?>
						<tr>
							<td class="tc2"><input name="topic_to_move" value="<?php echo $cur_topic['id'] ?>" type="radio" /></td>
							<td><?php echo '<a href="viewtopic.php?id='.$cur_topic['id'].'">'.pun_htmlspecialchars($cur_topic['subject']).'</a>'; ?></td>
							<td><?php echo pun_htmlspecialchars($cur_topic['poster']) ?></td>
							<td><?php echo format_time($cur_topic['last_post']) ?></td>
							<td class="tc2"><?php echo $cur_topic['num_replies'] ?></td>
						</tr>
<?php
			}
		} 
?>
						<tr>
							<th class="tc2" scope="col">
								<input name="topic_to_move" value="" checked="checked" type="radio" />
							</th>
							<th scope="col">
								<a href="movepost.php?<?php echo $post_id_param.$var_query_img ?>&sort=subject"><img alt="up" SRC="<?php echo PUN_STATIC_URL; ?>/forums/img/movepost/arrow_up.png"></a>
								<a href="movepost.php?<?php echo $post_id_param.$var_query_img ?>&sort=subject&desc=1"><img alt="down" SRC="<?php echo PUN_STATIC_URL; ?>/forums/img/movepost/arrow_down.png"></a><br />
								<strong><?php echo $lang_common['Topic'] ?></strong>
							</th>
							<th scope="col">
								<a href="movepost.php?<?php echo $post_id_param.$var_query_img ?>&sort=poster"><img alt="up" SRC="<?php echo PUN_STATIC_URL; ?>/forums/img/movepost/arrow_up.png"></a>
								<a href="movepost.php?<?php echo $post_id_param.$var_query_img ?>&sort=poster&desc=1"><img alt="down" SRC="<?php echo PUN_STATIC_URL; ?>/forums/img/movepost/arrow_down.png"></a><br />
								<strong><?php echo $lang_movepost['Poster'] ?></strong>
							</th>
							<th scope="col">
								<a href="movepost.php?<?php echo $post_id_param.$var_query_img ?>&sort=last_post"><img alt="up" SRC="<?php echo PUN_STATIC_URL; ?>/forums/img/movepost/arrow_up.png"></a>
								<a href="movepost.php?<?php echo $post_id_param.$var_query_img ?>&sort=last_post&desc=1"><img alt="down" SRC="<?php echo PUN_STATIC_URL; ?>/forums/img/movepost/arrow_down.png"></a><br />
								<strong><?php echo $lang_movepost['Last'] ?></strong>
							</th>
							<th class="tc2" scope="col">
								<a href="movepost.php?<?php echo $post_id_param.$var_query_img ?>&sort=num_replies"><img alt="up" SRC="<?php echo PUN_STATIC_URL; ?>/forums/img/movepost/arrow_up.png"></a>
								<a href="movepost.php?<?php echo $post_id_param.$var_query_img ?>&sort=num_replies&desc=1"><img alt="down" SRC="<?php echo PUN_STATIC_URL; ?>/forums/img/movepost/arrow_down.png"></a><br />
								<strong><?php echo $lang_common['Replies'] ?></strong>
							</th>
						</tr>
					</table>
				<p class="pagelink conl"><?php echo $paging_links ?></p><p>&nbsp;</p>
				<p style= "TEXT-ALIGN: center;"><input name="save" value="<?php echo $lang_common['Submit'] ?>" type="submit" /></p>
				</div>
			</fieldset>
			<input type="hidden" name="form_sent" value="3" />
			<br /><p><strong><?php echo "<a href=\"viewtopic.php?pid=".$post_id."#p".$post_id."\" />" ?><?php echo $lang_common['Go back'] ?></a></strong></p>
		</form>
	</div>
</div>
<?php

	require PUN_ROOT.'footer.php';
	}
}
else
	message($lang_common['Bad request']);
