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


define('PUN_ROOT', './');
require PUN_ROOT.'include/common.php';


if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view']);


$action = isset($_GET['action']) ? $_GET['action'] : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
$post_id = $pid;
if ($id < 1 && $pid < 1)
	message($lang_common['Bad request']);

// Load the viewtopic.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/topic.php';

// If it is a comment of a document
if (isset($_GET['doc']) && !isset($_GET['forum']))
{
    $doc_param = get_doc_param($_GET['doc']);
    $redirect_url = $doc_param[2];
    $doc = '&amp;doc='.$_GET['doc'];
}
else
{
    $doc = '';
}
$show_link_to_forum = isset($_GET['forum']) ? '&forum' : '' ;

// If a post ID is specified we determine topic ID and page number so we can redirect to the correct message
if ($pid)
{
	$result = $db->query('SELECT topic_id FROM '.$db->prefix.'posts WHERE id='.$pid) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
	if (!$db->num_rows($result))
		message($lang_common['Bad request']);

	$id = $db->result($result);

	// Determine on what page the post is located (depending on $pun_user['disp_posts'])
	$result = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE topic_id='.$id.' ORDER BY posted') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
	$num_posts = $db->num_rows($result);

	for ($i = 0; $i < $num_posts; ++$i)
	{
		$cur_id = $db->result($result, $i);
		if ($cur_id == $pid)
			break;
	}
	++$i;	// we started at 0

	$_GET['p'] = ceil($i / $pun_user['disp_posts']);
}

// If action=new, we redirect to the first new post (if any)
else if (!$pun_user['is_guest'] && (($action == 'new') || (($action == null) && !isset($_GET['p']))))
{
    $last_read = get_topic_last_read($id);
	$result = $db->query('SELECT MIN(id) FROM '.$db->prefix.'posts WHERE topic_id='.$id.' AND posted>'.$last_read) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
	$first_new_post_id = $db->result($result);

	if ($first_new_post_id)
    {
		if (!isset($redirect_url))
        {
            $redirect_url = 'viewtopic.php?pid='.$first_new_post_id.$show_link_to_forum;
        }
        else
        {
            $redirect_url .= '?new';
        }
        header('Location: '.$redirect_url.'#p'.$first_new_post_id);
	}
    else if ($action == 'new')	// If there is no new post, we go to the last post
	{
        $redirect_url = 'viewtopic.php?id='.$id.'&action=last'.$doc.$show_link_to_forum;
        header('Location: '.$redirect_url);
    }

	if (isset($redirect_url))
    {
        exit;
    }
}

// If action=last, we redirect to the last post
else if ($action == 'last')
{
	$result = $db->query('SELECT MAX(id) FROM '.$db->prefix.'posts WHERE topic_id='.$id) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
	$last_post_id = $db->result($result);

	if ($last_post_id)
	{
		if (!isset($redirect_url))
        {
            $redirect_url = 'viewtopic.php?pid='.$last_post_id;
        }
        header('Location: '.$redirect_url.'#p'.$last_post_id);
		exit;
	}
}
else if (isset($redirect_url))
{
    header('Location: '.$redirect_url);
    exit;
}

// Fetch some info about the topic
if (!$pun_user['is_guest'])
	$result = $db->query('SELECT pf.forum_name AS parent_forum, f.parent_forum_id, t.subject, t.closed, t.num_replies, t.sticky, t.last_post, t.question, t.yes, t.no, f.id AS forum_id, f.forum_name, f.moderators, fp.post_replies, s.user_id AS is_subscribed FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'subscriptions AS s ON (t.id=s.topic_id AND s.user_id='.$pun_user['id'].') LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') LEFT JOIN '.$db->prefix.'forums AS pf ON f.parent_forum_id=pf.id WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$id.' AND t.moved_to IS NULL') or error('Impossible de retrouver les informations de la discussion', __FILE__, __LINE__, $db->error());
else
	$result = $db->query('SELECT pf.forum_name AS parent_forum, f.parent_forum_id, t.subject, t.closed, t.num_replies, t.sticky, t.question, t.yes, t.no, f.id AS forum_id, f.forum_name, f.moderators, fp.post_replies FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].')  LEFT JOIN '.$db->prefix.'forums AS pf ON f.parent_forum_id=pf.id WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$id.' AND t.moved_to IS NULL') or error('Impossible de retrouver les informations de la discussion', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result))
	message($lang_common['Bad request']);

$cur_topic = $db->fetch_assoc($result);

if (!$pun_user['is_guest'])
{
    $last_read = get_topic_last_read($id);
    mark_topic_read($id, $cur_topic['forum_id'], $cur_topic['last_post']);
}
else
{
    $last_read = 0;
}

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_topic['moderators'] != '') ? unserialize($cur_topic['moderators']) : array();
$is_admmod = ($pun_user['g_id'] == PUN_ADMIN || ($pun_user['g_id'] == PUN_MOD && array_key_exists($pun_user['username'], $mods_array))) ? true : false;

// If it is a comment topic, we redirect to the document
if (get_is_comment($cur_topic['forum_id']) && !isset($_GET['forum']))
{
    $doc_param = get_doc_param($cur_topic['subject']);
    header('Location: '.$doc_param[2].$doc_param[3]);
    exit;
}

// Can we or can we not post replies?
if ($cur_topic['closed'] == '0')
{
	if (($cur_topic['post_replies'] == '' && $pun_user['g_post_replies'] == '1') || $cur_topic['post_replies'] == '1' || $is_admmod)
		$post_link = '<a href="post.php?tid='.$id.'">'.$lang_topic['Post reply'].'</a>';
	else
		$post_link = '&nbsp;';
}
else
{
	$post_link = $lang_topic['Topic closed'];

	if ($is_admmod)
		$post_link .= '<br /><a href="post.php?tid='.$id.'">'.$lang_topic['Post reply'].'</a>';
}

// Determine the post offset (based on $_GET['p'])
$num_pages = ceil(($cur_topic['num_replies'] + 1) / $pun_user['disp_posts']);

$p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : $_GET['p'];
$start_from = $pun_user['disp_posts'] * ($p - 1);

// Generate paging links
$paging_links = $lang_common['Pages'].': '.paginate($num_pages, $p, 'viewtopic.php?id='.$id);


if ($pun_config['o_censoring'] == '1')
	$cur_topic['subject'] = censor_words($cur_topic['subject']);


$quickpost = false;
if ($pun_config['o_quickpost'] == '1' &&
	!$pun_user['is_guest'] &&
	($cur_topic['post_replies'] == '1' || ($cur_topic['post_replies'] == '' && $pun_user['g_post_replies'] == '1')) &&
	($cur_topic['closed'] == '0' || $is_admmod))
{
	$required_fields = array('req_message' => $lang_common['Message']);
	$quickpost = true;
}

if (!$pun_user['is_guest'] && $pun_config['o_subscriptions'] == '1')
{
	if ($cur_topic['is_subscribed'])
		// I apologize for the variable naming here. It's a mix of subscription and action I guess :-)
		$subscraction = '<p class="subscribelink clearb">'.$lang_topic['Is subscribed'].' - <a href="misc.php?unsubscribe='.$id.'">'.$lang_topic['Unsubscribe'].'</a></p>'."\n";
	else
		$subscraction = '<p class="subscribelink clearb"><a href="misc.php?subscribe='.$id.'">'.$lang_topic['Subscribe'].'</a></p>'."\n";
}
else
	$subscraction = '<div class="clearer"></div>'."\n";

if ($cur_topic['question'])
	$cur_topic_question = $cur_topic['question'].' - ';
else
	$cur_topic_question = '';


$subject = pun_htmlspecialchars($cur_topic['subject']);
$page_title = pun_htmlspecialchars($cur_topic_question . $cur_topic['subject'].' / '.$pun_config['o_board_title']);
$footer_style = 'viewtopic';
$forum_id = $cur_topic['forum_id'];
define('PUN_ALLOW_INDEX', 1);
require PUN_ROOT.'header.php';
?>
<div class="clearing">
    <span class="article_title_img action_comment"/></span><span class="article_title"><?php echo $subject ?></span>
</div>
<div class="postlinkst">
	<div class="inbox">
		<p class="pagelink conl"><?php echo $paging_links ?></p>
		<p class="postlink conr"><?php echo $post_link ?></p>
<?php
if($cur_topic['parent_forum'])
{
    echo "\t\t".'<ul><li><a href="' . get_home_url() . '">'.$lang_common['Index'].'</a>&nbsp;</li><li>&raquo;&nbsp;<a href="viewforum.php?id='.$cur_topic['parent_forum_id'].'">'.pun_htmlspecialchars($cur_topic['parent_forum']).'</a>&nbsp;</li><li>&raquo;&nbsp;<a href="viewforum.php?id='.$cur_topic['forum_id'].'">'.pun_htmlspecialchars($cur_topic['forum_name']).'</a>&nbsp;</li><li>&raquo;&nbsp;'.$subject.'</li></ul>';
}
else
{
    echo "\t\t".'<ul><li><a href="' . get_home_url() . '">'.$lang_common['Index'].'</a>&nbsp;</li><li>&raquo;&nbsp;<a href="viewforum.php?id='.$cur_topic['forum_id'].'">'.pun_htmlspecialchars($cur_topic['forum_name']).'</a>&nbsp;</li><li>&raquo;&nbsp;<a href="viewtopic.php?id='.$id.'" rel="bookmark">'.$subject.'</a></li></ul>';
}
?>
		<div class="clearer"></div>
	</div>
</div>

<?php


require PUN_ROOT.'include/parser.php';

$bg_switch = true;	// Used for switching background color in posts
$post_count = 0;	// Keep track of post numbers

// Mod poll begin
if ($cur_topic['question'])
{
	require PUN_ROOT . 'lang/' . $pun_user['language'] . '/polls.php'; 
    // get the poll data
    $result = $db->query('SELECT ptype,options,voters,votes FROM ' . $db->prefix . 'polls WHERE pollid=' . $id . '') or error('Unable to fetch poll info', __FILE__, __LINE__, $db->error());

    if (!$db->num_rows($result))
        message($lang_common['Bad request']);

    $cur_poll = $db->fetch_assoc($result);

    $options = unserialize($cur_poll['options']);
    if (!empty($cur_poll['voters']))
        $voters = unserialize($cur_poll['voters']);
    else
        $voters = array();

    $ptype = $cur_poll['ptype']; 
    // yay memory!
    // $cur_poll = null;
    $firstcheck = false;
    ?>
<div class="blockform">
	<h2><span><?php echo $lang_polls['Poll'] ?></span></h2>
	<div class="box">
    	<?php
    if ((!$pun_user['is_guest']) && (!in_array($pun_user['id'], $voters)) && ($cur_topic['closed'] == '0') && (($cur_topic['post_replies'] == '1' || ($cur_topic['post_replies'] == '' && $pun_user['g_post_replies'] == '1')) || $is_admmod)) 
	{
		$showsubmit = true;
		?>
		<form id="poll" method="post" action="vote.php">
			<div class="inform">
				<div class="rbox">
				<fieldset>
					<legend><?php echo pun_htmlspecialchars($cur_topic['question']) ?></legend>
					<div class="infldset txtarea">
						<input type="hidden" name="poll_id" value="<?php echo $id; ?>" />
						<input type="hidden" name="form_sent" value="1" />
						<input type="hidden" name="form_user" value="<?php echo (!$pun_user['is_guest']) ? pun_htmlspecialchars($pun_user['username']) : 'Guest'; ?>" />
	
						<?php
				        if ($ptype == 1) 
						{
							while (list($key, $value) = each($options)) 
							{
							?>
								<label><input name="vote" <?php if (!$firstcheck) { echo 'checked="checked"'; $firstcheck = true; }; ?> type="radio" value="<?php echo $key ?>" /> <span><?php echo pun_htmlspecialchars($value); ?></span></label>
							<?php
				            } 
				        } 
						elseif ($ptype == 2) 
						{
						    while (list($key, $value) = each($options)) 
							{         
							?>
								<label><input name="options[<?php echo $key ?>]" type="checkbox" value="1" /> <span><?php echo pun_htmlspecialchars($value); ?></span></label>
							<?php
				            } 
				        } 
						elseif ($ptype == 3) 
						{
							
							while (list($key, $value) = each($options)) 
							{
								echo pun_htmlspecialchars($value); ?>
								<label><input name="options[<?php echo $key ?>]" checked="checked" type="radio" value="yes" /> <?php echo $cur_topic['yes']; ?></label>
								<label><input name="options[<?php echo $key ?>]" type="radio" value="no" /> <?php echo $cur_topic['no']; ?></label>
								<br />
							<?php
				            } 
						} 
						else
						{
							message($lang_common['Bad request']);
						}
			?></div></fieldset><?php
    } 
	else 
	{
		$showsubmit = false;
		?>
		<div class="inform">
		<div class="rbox">
			
			<p class="poll_info"><strong><?php echo pun_htmlspecialchars($cur_topic['question']) ?></strong></p>			
			<?php
    		if (!empty($cur_poll['votes']))
    	    		$votes = unserialize($cur_poll['votes']);
    		else
          		$votes = array();
		
			if ($ptype == 1 || $ptype == 2) 
			{
				$total = 0;
				$percent = 0;
				$percent_int = 0;
				while (list($key, $val) = each($options)) 
				{
					if (isset($votes[$key]))
						$total += $votes[$key];
				}
				reset($options);
			}
			
		  	while (list($key, $value) = each($options)) {    

				if ($ptype == 1 || $ptype == 2)
				{ 
					if (isset($votes[$key]))
					{
						$percent =  $votes[$key] * 100 / $total;
						$percent_int = floor($percent);
					}
					?>
						<div class="poll_question"><?php echo pun_htmlspecialchars($value); ?></div>
						<div class="poll_result">
							<img src="<?php echo PUN_STATIC_URL; ?>/forums/img/transparent.gif" class="poll_bar" style="width:<?php if (isset($votes[$key])) echo $percent_int/2; else echo '0'; ?>%;" alt="" />
							<span><?php if (isset($votes[$key])) echo $percent_int . '% - ' . $votes[$key]; else echo '0% - 0'; ?></span>
						</div>
				<?php
				}
				else if ($ptype == 3) 
				{ 
					$total = 0;
					$yes_percent = 0;
					$no_percent = 0;
					$vote_yes = 0;
					$vote_no = 0;
					if (isset($votes[$key]['yes']))
					{
						$vote_yes = $votes[$key]['yes'];
					}

					if (isset($votes[$key]['no'])) {
						$vote_no += $votes[$key]['no'];
					}

					$total = $vote_yes + $vote_no;
					if (isset($votes[$key]))
					{
						$yes_percent =   floor($vote_yes * 100 / $total);
						$no_percent = floor($vote_no * 100 / $total);
					}
					?>
						<div class="poll_question"><?php echo pun_htmlspecialchars($value); ?></div>
						
						<div class="poll_result_yesno">
							<strong><?php echo $cur_topic['yes']; ?></strong>
								<img src="<?php echo PUN_STATIC_URL; ?>/forums/img/transparent.gif" class="poll_bar" style="width:<?php if (isset($votes[$key]['yes'])) { echo $yes_percent/2; } else { echo '0';  } ?>%;" alt="" />
								<span><?php if (isset($votes[$key]['yes'])) { echo $yes_percent . "% - " . $votes[$key]['yes']; } else { echo "0% - " . 0; } ?></span>
						</div>
						<div class="poll_result_yesno">						
							<strong><?php echo $cur_topic['no']; ?></strong>
								<img src="<?php echo PUN_STATIC_URL; ?>/forums/img/transparent.gif" class="poll_bar" style="width:<?php if (isset($votes[$key]['no'])) { echo $no_percent/2; } else { echo '0';  } ?>%;" alt="" />
								<span><?php if (isset($votes[$key]['no'])) { echo $no_percent . "% - " . $votes[$key]['no']; } else { echo "0% - " . 0; } ?></span>
						</div>
					<?php 
				}
				else
				message($lang_common['Bad request']);
            } 	
			?>
				<p class="poll_info">Total : <?php echo $total; ?></p>
			<?php
		} 
		?>
			</div>
				
			</div>

			<?php if ($showsubmit == true) 
			{ 
				echo '<p><input type="submit" name="submit" tabindex="2" value="' . $lang_common['Submit'] . '" accesskey="s" /> <input type="submit" name="null" tabindex="2" value="' . $lang_polls['Null vote']. '" accesskey="n" /></p>
				</form>';
			} 
			?>
	</div>
</div>
<?php
}
// Mod poll end

$result = $db->query('SELECT p.id, p.poster AS username, p.poster_id, p.poster_ip, p.poster_email, p.message, p.hide_smilies, p.posted, p.edited, p.edited_by FROM '.$db->prefix.'posts AS p WHERE p.topic_id='.$id.' ORDER BY p.id LIMIT '.$start_from.','.$pun_user['disp_posts'], true) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());

$posts_list = $posters_ids = $posters_data = array();
while ($cur_post = $db->fetch_assoc($result))
{
    $posts_list[] = $cur_post;
    $posters_ids[] = $cur_post['poster_id'];
}

if (count($posters_ids) > 0)
{
    $posters_ids = array_unique($posters_ids);
    $result = $db->query('SELECT u.id, u.email, u.title, u.use_avatar, u.signature, u.email_setting, u.num_posts, g.g_id, g.g_user_title FROM '.$db->prefix.'users AS u INNER JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id IN (' . implode(',', $posters_ids) . ')', true) or error('Unable to fetch posters info', __FILE__, __LINE__, $db->error());
    
    while ($cur_poster = $db->fetch_assoc($result))
    {
        $posters_data[$cur_poster['id']] = $cur_poster;
    }
}

foreach ($posts_list as $cur_post)
{
	$post_count++;
	$user_avatar = '';
	$user_info = array();
	$user_contacts = array();
	$post_actions = array();
	$signature = '';

    $poster_id = $cur_post['poster_id'];
    $poster_data =& $posters_data[$poster_id];

    if (isset($poster_data['user_title']))
    {
        $user_title = $poster_data['user_title'];
    }
    else
    {
        $poster_data['username'] = $cur_post['username'];
        $user_title = get_title($poster_data);
        $poster_data['user_title'] = $user_title;
    }

	// If the poster is a registered user.
	if ($poster_id > 1)
	{
		$username = '<a href="/users/'.$poster_id.'">'.pun_htmlspecialchars($cur_post['username']).'</a>';
		
		if ($pun_config['o_censoring'] == '1')
			$user_title = censor_words($user_title);

		if ($pun_config['o_avatars'] == '1' && $poster_data['use_avatar'] == '1' && $pun_user['show_avatars'] != '0')
		{
			if ($img_size = @getimagesize($pun_config['o_avatars_dir'].'/'.$cur_post['poster_id'].'.gif'))
				$user_avatar = '<img src="'.PUN_STATIC_URL.'/forums/'.$pun_config['o_avatars_dir'].'/'.$cur_post['poster_id'].'.gif" '.$img_size[3].' alt="" />';
			else if ($img_size = @getimagesize($pun_config['o_avatars_dir'].'/'.$cur_post['poster_id'].'.jpg'))
				$user_avatar = '<img src="'.PUN_STATIC_URL.'/forums/'.$pun_config['o_avatars_dir'].'/'.$cur_post['poster_id'].'.jpg" '.$img_size[3].' alt="" />';
			else if ($img_size = @getimagesize($pun_config['o_avatars_dir'].'/'.$cur_post['poster_id'].'.png'))
				$user_avatar = '<img src="'.PUN_STATIC_URL.'/forums/'.$pun_config['o_avatars_dir'].'/'.$cur_post['poster_id'].'.png" '.$img_size[3].' alt="" />';
		}
		else
			$user_avatar = '';

		if ($pun_config['o_show_user_info'] == '1')
		{
			// Don't display location
           /* if ($cur_post['location'] != '')
			{
				if ($pun_config['o_censoring'] == '1')
					$cur_post['location'] = censor_words($cur_post['location']);

				$user_info[] = '<dd>'.$lang_topic['From'].': '.pun_htmlspecialchars($cur_post['location']);
			} */
			//Don't display the register date for all users
			//$user_info[]= '<dd>'.$lang_common['Registered'].': '.date($pun_config['o_date_format'], $cur_post['registered']);

			if ($pun_config['o_show_post_count'] == '1' || $pun_user['g_id'] < PUN_GUEST)
				$user_info[] = '<dd>'.$lang_common['Posts'].': '.$poster_data['num_posts'];

			// Now let's deal with the contact links (E-mail and URL)
			if (!$pun_user['is_guest'])
				$user_contacts[] = '<a href="javascript:paste_nick(\''.pun_jsspecialchars($cur_post['username']).'\');">'.$lang_topic['Nick copy'].'</a>';
			if (($poster_data['email_setting'] == '0' && !$pun_user['is_guest']) || $pun_user['g_id'] < PUN_GUEST)
				$user_contacts[] = '<a href="mailto:'.$poster_data['email'].'">'.$lang_common['E-mail'].'</a>';
			else if ($poster_data['email_setting'] == '1' || $pun_user['is_guest'])
				$user_contacts[] = '<a href="misc.php?email='.$cur_post['poster_id'].'">'.$lang_common['E-mail'].'</a>';
            require(PUN_ROOT.'include/pms/viewtopic_PM-link.php');

			// Don't display url
            /*if ($cur_post['url'] != '')
				$user_contacts[] = '<a href="'.pun_htmlspecialchars($cur_post['url']).'">'.$lang_topic['Website'].'</a>';
                                */
		}

		if ($pun_user['g_id'] < PUN_GUEST)
		{
			$user_info[] = '<dd>IP: <a href="moderate.php?get_host='.$cur_post['id'].'">'.$cur_post['poster_ip'].'</a>';

        // Don't display admin_note : a quoi ca sert ce champ ???
		/*	if ($cur_post['admin_note'] != '')
				$user_info[] = '<dd>'.$lang_topic['Note'].': <strong>'.pun_htmlspecialchars($cur_post['admin_note']).'</strong>';
                      */
		}
	}
	// If the poster is a guest (or a user that has been deleted)
	else
	{
		$username = pun_htmlspecialchars($cur_post['username']);

		if ($pun_user['g_id'] < PUN_GUEST)
			$user_info[] = '<dd>IP: <a href="moderate.php?get_host='.$cur_post['id'].'">'.$cur_post['poster_ip'].'</a>';

		if (!$pun_user['is_guest'])
			$user_contacts[] = '<a href="javascript:paste_nick(\''.pun_jsspecialchars($cur_post['username']).'\');">'.$lang_topic['Nick copy'].'</a>';
		
		if ($pun_config['o_show_user_info'] == '1' && $cur_post['poster_email'] != '')
        {
            if (!$pun_user['is_guest'])
            {
                $user_contacts[] = '<a href="mailto:'.$cur_post['poster_email'].'">'.$lang_common['E-mail'].'</a>';
            }
            else
            {
                $user_contacts[] = '<span class="inactive" title="'.$lang_topic['E-mail tooltip'].'">'.$lang_common['E-mail'].'</span>';
            }
        }
	}

	// Generation post action array (quote, edit, delete etc.)
	if (!$is_admmod)
	{
		if (!$pun_user['is_guest'])
			$post_actions[] = '<li class="postreport"><a href="misc.php?report='.$cur_post['id'].'">'.$lang_topic['Report'].'</a>';

		if ($cur_topic['closed'] == '0')
		{
			if ($cur_post['poster_id'] == $pun_user['id'])
			{
				if ((($start_from + $post_count) == 1 && $pun_user['g_delete_topics'] == '1') || (($start_from + $post_count) > 1 && $pun_user['g_delete_posts'] == '1'))
					$post_actions[] = '<li class="postdelete"><a href="delete.php?id='.$cur_post['id'].'">'.$lang_topic['Delete'].'</a>';
				if ($pun_user['g_edit_posts'] == '1')
					$post_actions[] = '<li class="postedit"><a href="edit.php?id='.$cur_post['id'].'">'.$lang_topic['Edit'].'</a>';
			}

			if (($cur_topic['post_replies'] == '' && $pun_user['g_post_replies'] == '1') || $cur_topic['post_replies'] == '1') 
			{
				$post_actions[] = '<li class="postquote"><a href="post.php?tid='.$id.'&amp;qid='.$cur_post['id'].'">'.$lang_topic['Quoted reply'].'</a>';
				if (!$pun_user['is_guest'])
					$post_actions[] = '<li class="postquote"><a onmouseover="get_quote_text();" href="javascript:paste_quote(\''.pun_jsspecialchars($cur_post['username']).'\');">'.$lang_topic['Quote'].'</a>';
			}
		}
	}
	else
		$post_actions[] = '<li class="postreport"><a href="misc.php?report='.$cur_post['id'].'">'.$lang_topic['Report'].'</a>'.$lang_topic['Link separator'].'</li><li class="postdelete"><a href="delete.php?id='.$cur_post['id'].'">'.$lang_topic['Delete'].'</a>'.$lang_topic['Link separator'].'</li><li class="movepost"><a href="movepost.php?id='.$cur_post['id'].'">'.$lang_topic['Move'].'</a>'.$lang_topic['Link separator'].'</li><li class="postedit"><a href="edit.php?id='.$cur_post['id'].'">'.$lang_topic['Edit'].'</a>'.$lang_topic['Link separator'].'</li><li class="postquote"><a href="post.php?tid='.$id.'&amp;qid='.$cur_post['id'].'">'.$lang_topic['Quoted reply'].'</a>'.$lang_topic['Link separator'].'<li class="postquote"><a onmouseover="get_quote_text();" href="javascript:paste_quote(\''.pun_jsspecialchars($cur_post['username']).'\');">'.$lang_topic['Quote'].'</a>'; //Move Post Mod 1.2 row - Quick Quote



	// Switch the background color for every message.
	$bg_switch = ($bg_switch) ? $bg_switch = false : $bg_switch = true;
	$vtbg = ($bg_switch) ? ' roweven' : ' rowodd';


	// Perform the main parsing of the message (BBCode, smilies, censor words etc)
	$cur_post['message'] = parse_message($cur_post['message'], $cur_post['hide_smilies']);

	// Do signature parsing/caching
	if ($poster_data['signature'] != '' && $pun_user['show_sig'] != '0')
	{
		if (isset($signature_cache[$cur_post['poster_id']]))
			$signature = $signature_cache[$cur_post['poster_id']];
		else
		{
			$signature = parse_signature($poster_data['signature']);
			$signature_cache[$cur_post['poster_id']] = $signature;
		}
	}

?>
<div id="p<?php echo $cur_post['id'] ?>" class="blockpost<?php
    echo $vtbg;
    if (!$pun_user['is_guest'] && ($cur_post['posted'] > $last_read) && ($cur_post['poster_id'] != $pun_user['id'])) echo ' new';
    if (($post_count + $start_from) == 1) echo ' firstpost'; ?>">
	<h2><span><span class="conr">#<?php echo ($start_from + $post_count) ?>&nbsp;</span><a href="viewtopic.php?pid=<?php echo $cur_post['id'].'#p'.$cur_post['id'] ?>"><?php echo format_time($cur_post['posted']) ?></a></h2>
	<div class="box">
		<div class="inbox">
			<div class="postleft">
				<dl>
					<dt><strong><?php echo $username ?></strong></dt>
					<dd class="usertitle"><strong><?php echo $user_title ?></strong></dd>
					<dd class="postavatar"><?php echo $user_avatar ?></dd>
<?php if (count($user_info)) echo "\t\t\t\t\t".implode('</dd>'."\n\t\t\t\t\t", $user_info).'</dd>'."\n"; ?>
<?php if (count($user_contacts)) echo "\t\t\t\t\t".'<dd class="usercontacts">'.implode('&nbsp; ', $user_contacts).'</dd>'."\n"; ?>
				</dl>
			</div>
			<div class="postright">
				<h3><?php if (($post_count + $start_from) > 1) echo ' Re: '; ?><?php echo $subject ?></h3>
				<div class="postmsg">
					<?php echo $cur_post['message']."\n" ?>
<?php if ($cur_post['edited'] != '') echo "\t\t\t\t\t".'<p class="postedit"><em>'.$lang_topic['Last edit'].' '.pun_htmlspecialchars($cur_post['edited_by']).' ('.format_time($cur_post['edited']).')</em></p>'."\n"; ?>
				</div>
<?php if ($signature != '') echo "\t\t\t\t".'<div class="postsignature"><hr />'.$signature.'</div>'."\n"; ?>
			</div>
			<div class="clearer"></div>
			<div class="postfootright"><?php echo (count($post_actions)) ? '<ul>'.implode($lang_topic['Link separator'].'</li>', $post_actions).'</li></ul></div>'."\n" : '<div>&nbsp;</div></div>'."\n" ?>
		</div>
	</div>
</div>

<?php

}

?>
<div class="postlinksb">
	<div class="inbox">
		<p class="pagelink conl"><?php echo $paging_links ?></p>
		<p class="postlink conr"><?php echo $post_link ?></p>
<?php
if($cur_topic['parent_forum'])
{
    echo "\t\t".'<ul><li><a href="' . get_home_url() . '">'.$lang_common['Index'].'</a>&nbsp;</li><li>&raquo;&nbsp;<a href="viewforum.php?id='.$cur_topic['parent_forum_id'].'">'.pun_htmlspecialchars($cur_topic['parent_forum']).'</a>&nbsp;</li><li>&raquo;&nbsp;<a href="viewforum.php?id='.$cur_topic['forum_id'].'">'.pun_htmlspecialchars($cur_topic['forum_name']).'</a>&nbsp;</li><li>&raquo;&nbsp;'.$subject.'</li></ul>';
}
else
{
    echo "\t\t".'<ul><li><a href="' . get_home_url() . '">'.$lang_common['Index'].'</a>&nbsp;</li><li>&raquo;&nbsp;<a href="viewforum.php?id='.$cur_topic['forum_id'].'">'.pun_htmlspecialchars($cur_topic['forum_name']).'</a>&nbsp;</li><li>&raquo;&nbsp;<a href="viewtopic.php?id='.$id.'" rel="bookmark">'.$subject.'</a></li></ul>';
}
echo "\t\t".$subscraction ?>
	</div>
</div>

<?php

// Display quick post if enabled
if ($quickpost)
{
?>
<div class="blockform">
	<h2><span><?php echo $lang_topic['Quick post'] ?></span></h2>
	<div class="box">
		<form id="post" method="post" action="post.php?tid=<?php echo $id ?>#postpreview" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_common['Write message legend'] ?></legend>
					<div class="infldset txtarea">
						<input type="hidden" name="form_sent" value="1" />
						<input type="hidden" name="form_user" value="<?php echo (!$pun_user['is_guest']) ? pun_htmlspecialchars($pun_user['username']) : 'Guest'; ?>" /><?php
						require PUN_ROOT.'mod_easy_bbcode.php'; ?>
						<label><textarea name="req_message" rows="20" cols="75" tabindex="1"></textarea></label>
						<ul class="bblinks">
							<li><a href="help.php#bbcode" onclick="window.open(this.href); return false;"><?php echo $lang_common['BBCode'] ?></a>: <?php echo ($pun_config['p_message_bbcode'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
							<li><a href="help.php#img" onclick="window.open(this.href); return false;"><?php echo $lang_common['img tag'] ?></a>: <?php echo ($pun_config['p_message_img_tag'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
							<li><a href="help.php#smilies" onclick="window.open(this.href); return false;"><?php echo $lang_common['Smilies'] ?></a>: <?php echo ($pun_config['o_smilies'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
						</ul>
					</div>
				</fieldset>
			</div>
			<p>
                <input type="submit" name="preview" value="<?php echo $lang_common['Preview'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="p" />
                <input type="submit" name="submit" value="<?php echo $lang_common['Submit and topic'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="s" />
                <input type="submit" name="submit_forum" value="<?php echo $lang_common['Submit and forum'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="f" />
                <a href="javascript:history.go(-1)"><?php echo $lang_common['Go back'] ?></a>
            </p>
		</form>
	</div>
</div>
<?php

}

// Increment "num_views" for topic
$low_prio = ($db_type == 'mysql') ? 'LOW_PRIORITY ' : '';
$db->query('UPDATE '.$low_prio.$db->prefix.'topics SET num_views=num_views+1 WHERE id='.$id) or error('Unable to update topic', __FILE__, __LINE__, $db->error());

require PUN_ROOT.'footer.php';
