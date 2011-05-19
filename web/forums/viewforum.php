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


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 1)
	message($lang_common['Bad request']);

define('FORUM_FEED', $id);

// Load the viewforum.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/forum.php';

require PUN_ROOT.'lang/'.$pun_user['language'].'/index.php';

// Load poll language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/polls.php';

$show_link_to_forum = isset($_GET['forum']) ? '&amp;forum' : '' ;
$is_comment_forum = get_is_comment($id);
$mobile_version = c2cTools::mobileVersion();

// Fetch some info about the forum
$result = $db->query('SELECT f.forum_name, f.forum_desc, pf.forum_name AS parent_forum, f.redirect_url, f.moderators, f.num_topics, f.sort_by, f.parent_forum_id, fp.post_topics, fp.post_polls FROM '.$db->prefix.'forums AS f LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') LEFT JOIN '.$db->prefix.'forums AS pf ON f.parent_forum_id=pf.id WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$id) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());

if (!$db->num_rows($result))
	message($lang_common['Bad request']);

$cur_forum = $db->fetch_assoc($result);

// Is this a redirect forum? In that case, redirect!
if ($cur_forum['redirect_url'] != '')
{
	header('Location: '.$cur_forum['redirect_url']);
	exit;
}

// Sort out who the moderators are and if we are currently a moderator (or an admin)
list($is_admmod, $is_c2c_board) = get_is_admmod($id, $cur_forum['moderators'], $pun_user);

// c2c board topic
if (!$is_c2c_board)
	message($lang_common['No permission']);

// If it is a pub forum, we don't want thatsearch engine follow links
$pub_forums = explode(', ', PUB_FORUMS);
if (in_array($id, $pub_forums))
{
    $rel = ' rel="nofollow"';
}
else
{
    $rel = '';
}

// Can we or can we not post new topics?
if ((($cur_forum['post_topics'] == '' && $pun_user['g_post_topics'] == '1') || $cur_forum['post_topics'] == '1') && !$is_comment_forum || $is_admmod)
	$post_link = '<a href="post.php?fid='.$id.'" rel="nofollow">'.$lang_forum['Post topic'].'</a>';
else
	$post_link = '&nbsp;';

if (($cur_forum['post_polls'] == '' && $pun_user['g_post_polls'] == '1') || $cur_forum['post_polls'] == '1' || $is_admmod)
    $post_link .= ' | <a href="post.php?fid='.$id.'&amp;type=poll">'.$lang_polls['New poll'].'</a>';


// Determine the topic offset (based on $_GET['p'])
$num_pages = ceil($cur_forum['num_topics'] / $pun_user['disp_topics']);

$p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : $_GET['p'];
$start_from = $pun_user['disp_topics'] * ($p - 1);

// Generate paging links
$paging_links = $lang_common['Pages'].': '.paginate($num_pages, $p, 'viewforum.php?id='.$id.$show_link_to_forum, $rel, true);

// Link to show comment forum with forum link instead doc link
if ($is_comment_forum)
{
    $forum_mode_link = '&nbsp;<a href="viewforum.php?id='.$id;
    if (isset($_GET['p']))
    {
        $forum_mode_link .= '&amp;p='.$_GET['p'];
    }
    if (!isset($_GET['forum']))
    {
        $forum_mode_link .= '&amp;forum">['.$lang_common['Forum'].']</a>';
    }
    else
    {
        $forum_mode_link .= '">[Doc]</a>';
    }
}
else
{
    $forum_mode_link = '';
}

$forum_name = pun_htmlspecialchars($cur_forum['forum_name']);
$page_title = pun_htmlspecialchars($cur_forum['forum_name'].' :: '.$lang_common['forum'].' - '.$pun_config['o_board_title']);

$page_description = $cur_forum['forum_desc'];
$page_description = preg_replace('#<em>(.*?)</em>#is', '', $page_description);
$pattern = array("\n", "\t", '	', '  ', '<br />');
$replace = array(' ', ' ', ' ', ' ', ' ');
$page_description = str_replace($pattern, $replace, $page_description);
$page_description = pun_htmlspecialchars($page_description);

if ($pun_user['g_id'] < PUN_GUEST)
{
    $mods_array = unserialize($cur_forum['moderators']);
    if (!empty($mods_array))
    {
        $moderator_list = array();
        while (list($mod_username, $mod_id) = @each($mods_array))
        {
            $moderator_list[] = '<a href="/users/'.$mod_id.'">'.pun_htmlspecialchars($mod_username).'</a>';
        }
        $moderator_list = '<div class="forum_desc"><div class="inbox">'
                        . $lang_common['Moderated by'] . ' : '
                        . implode(', ', $moderator_list)
                        . '</div></div>';
    }
    else
    {
        $moderator_list = '';
    }
}
else
{
    $moderator_list = '';
}

$footer_style = 'viewforum';
$forum_id = $id;
define('PUN_ALLOW_INDEX', 1);
require PUN_ROOT.'header.php';

#$new_topics = get_all_new_topics();
$new_topics = array();

# Option Note: if you do not want the subforums displaying at the top
# when you go into the main forum topic 
# then in the following $sub_forum_result query change  
# - ORDER BY disp_position')        -  to
# - ORDER BY disp_position', true)  -  (without the dashes)
#
$subforum_result = $db->query('SELECT forum_desc, forum_name, id, last_post, last_post_id, last_poster, moderators, num_posts, num_topics, redirect_url FROM '.$db->prefix.'forums WHERE parent_forum_id='.$id.' ORDER BY disp_position') or error('Unable to fetch sub forum info',__FILE__,__LINE__,$db->error());
if($db->num_rows($subforum_result))
{
?>
<div class="linkst">
    <div class="inbox">
        <ul><li><a href="<?php echo get_home_url() ?>"><?php echo $lang_common['Index'] ?></a>&nbsp;</li><li>&raquo;&nbsp;<?php echo pun_htmlspecialchars($cur_forum['forum_name']) ?></li></ul>
        <div class="clearer"></div>
    </div>
</div>

<div id="vf" class="blocktable">
    <h2><span>Sub forums</span></h2>
    <div class="box">
        <div class="inbox">
            <table>
            <thead>
                <tr>
                    <th class="tcl" scope="col"><?php echo $lang_common['Forum'] ?></th>
                    <th class="tc2" scope="col"><?php echo $lang_index['Topics'] ?></th>
                    <th class="tc3" scope="col"><?php echo $lang_common['Posts'] ?></th>
                    <th class="tcr" scope="col"><?php echo $lang_common['Last post'] ?></th>
                </tr>
            </thead>
            <tbody>
<?php

while($cur_subforum = $db->fetch_assoc($subforum_result))
{
    $item_status = '';
    $icon_text = $lang_common['Normal icon'];
    $icon_type = 'icon';

    // Are there new posts?
	if (!$pun_user['is_guest'] && forum_is_new($cur_subforum['id'], $cur_subforum['last_post']))
    {
        $item_status = 'inew';
        $icon_text = $lang_common['New icon'];
        $icon_type = 'icon inew';
    }

    // Is this a redirect forum?
    if ($cur_forum['redirect_url'] != '')
    {
        $forum_field = '<h3><a href="'.pun_htmlspecialchars($cur_subforum['redirect_url']).'" title="'.$lang_index['Link to'].' '.pun_htmlspecialchars($cur_subforum['redirect_url']).'">'.pun_htmlspecialchars($cur_subforum['forum_name']).'</a></h3>';
        $num_topics = $num_posts = '&nbsp;';
        $item_status = 'iredirect';
        $icon_text = $lang_common['Redirect icon'];
        $icon_type = 'icon';
    }
    else
    {
        $forum_field = '<h3><a href="viewforum.php?id='.$cur_subforum['id'].'">'.pun_htmlspecialchars($cur_subforum['forum_name']).'</a></h3>';
        $num_topics = $cur_subforum['num_topics'];
        $num_posts = $cur_subforum['num_posts'];
    }

    if ($cur_subforum['forum_desc'] != '')
        $forum_field .= "\n\t\t\t\t\t\t\t\t".$cur_subforum['forum_desc'];

    // If there is a last_post/last_poster.
    if ($cur_subforum['last_post'] != '')
        $last_post = '<a href="viewtopic.php?pid='.$cur_subforum['last_post_id'].'#p'.$cur_subforum['last_post_id'].'">'.format_time($cur_subforum['last_post']).'</a> <span class="byuser">'.$lang_common['by'].' '.pun_htmlspecialchars($cur_subforum['last_poster']).'</span>';
    else
        $last_post = '&nbsp;';

    if ($cur_subforum['moderators'] != '')
    {
        $mods_array = unserialize($cur_subforum['moderators']);
        $moderators = array();

        while (list($mod_username, $mod_id) = @each($mods_array))
            $moderators[] = '<a href="profile.php?id='.$mod_id.'">'.pun_htmlspecialchars($mod_username).'</a>';

        $moderators = "\t\t\t\t\t\t\t\t".'<p><em>('.$lang_common['Moderated by'].'</em> '.implode(', ', $moderators).')</p>'."\n";
    }
?>
                <tr<?php if ($item_status != '') echo ' class="'.$item_status.'"'; ?>>
                    <td class="tcl">
                        <div class="intd">
                            <div class="<?php echo $icon_type ?>"><div class="nosize"><?php echo $icon_text ?></div></div>
                            <div class="tclcon">
                                <?php echo $forum_field;
                                if ($cur_subforum['moderators'] != '') {
                                    echo "\n".$moderators;
                                }
                                ?>
                            </div>
                        </div>
                    </td>
                    <td class="tc2"><?php echo $num_topics ?></td>
                    <td class="tc3"><?php echo $num_posts ?></td>
                    <td class="tcr"><?php echo $last_post ?></td>
                </tr>
<?php
    }
?>
            </tbody>
            </table>
        </div>
    </div>
</div>
<?php
}

?>
<h1>
    <span class="article_title_img action_comment"></span><span class="article_title"><?php echo $forum_name ?></span>
</h1>
<?php
if($cur_forum['forum_desc'] != ''):
?>
<div class="forum_desc">
	<div class="inbox"><?php echo $cur_forum['forum_desc'] ?></div>
</div>
<?php
endif;
?>
<div class="linkst">
	<div class="inbox">
		<?php
if($cur_forum['parent_forum'])
{
    $forum_links = "\t\t".'<ul><li><a href="' . get_home_url() . '">'.$lang_common['Index'].'</a>&nbsp;</li><li>&raquo;&nbsp;<a href="viewforum.php?id='.$cur_forum['parent_forum_id'].'">'.pun_htmlspecialchars($cur_forum['parent_forum']).'</a>&nbsp;</li><li>&raquo;&nbsp;'.$forum_name.$forum_mode_link.'</li></ul>';
}
else
{
    $forum_links = "\t\t".'<ul><li><a href="' . get_home_url() . '">'.$lang_common['Index'].'</a>&nbsp;</li><li>&raquo;&nbsp;'.$forum_name.$forum_mode_link.'</li></ul>';
}
?>
		<p class="pagelink conl"><?php echo $paging_links ?></p>
		<p class="postlink conr"><?php echo $post_link ?></p>
		<div class="clearer"></div>
	</div>
</div>
<?php 
if ($db->num_rows($subforum_result) < 1){
?>
<div id="vf" class="blocktable">
	<div class="box">
		<div class="inbox">
			<table>
			<thead>
				<tr>
					<th class="tcl" scope="col"><?php echo $lang_common['Topic'] ?></th>
					<?php if (!$mobile_version): ?>
					<th class="tc2" scope="col"><?php echo $lang_common['Replies'] ?></th>
					<?php endif ?>
					<th class="tcr" scope="col"><?php echo $lang_common['Last post'] ?></th>
				</tr>
			</thead>
			<tbody>
<?php

// Fetch list of topics to display on this page
if ($pun_user['is_guest'] || $pun_config['o_show_dot'] == '0')
{
	// Without "the dot"
	//$sql = 'SELECT id, poster, subject, posted, last_post, last_post_id, last_poster, num_replies, closed, sticky, moved_to, question FROM '.$db->prefix.'topics WHERE forum_id='.$id.' ORDER BY sticky DESC, '.(($cur_forum['sort_by'] == '1') ? 'posted' : 'last_post').' DESC LIMIT '.$start_from.', '.$pun_user['disp_topics'];

    // postgresql prefers "OFFSET"
	$sql = 'SELECT id, poster, subject, posted, last_post, last_post_id, last_poster, num_replies, closed, sticky, moved_to, question FROM '.$db->prefix.'topics WHERE forum_id='.$id.' ORDER BY sticky DESC, '.(($cur_forum['sort_by'] == '1') ? 'posted' : 'last_post').' DESC LIMIT '.$pun_user['disp_topics'].' OFFSET '.$start_from;
}
else
{
	// With "the dot"
	switch ($db_type)
	{
		case 'mysql':
		case 'mysqli':
			$sql = 'SELECT p.poster_id AS has_posted, t.id, t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, t.question FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'posts AS p ON t.id=p.topic_id AND p.poster_id='.$pun_user['id'].' WHERE t.forum_id='.$id.' GROUP BY t.id ORDER BY sticky DESC, '.(($cur_forum['sort_by'] == '1') ? 'posted' : 'last_post').' DESC LIMIT '.$start_from.', '.$pun_user['disp_topics'];
			break;

		case 'sqlite':
			$sql = 'SELECT p.poster_id AS has_posted, t.id, t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, t.question FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'posts AS p ON t.id=p.topic_id AND p.poster_id='.$pun_user['id'].' WHERE t.id IN(SELECT id FROM '.$db->prefix.'topics WHERE forum_id='.$id.' ORDER BY sticky DESC, '.(($cur_forum['sort_by'] == '1') ? 'posted' : 'last_post').' DESC LIMIT '.$start_from.', '.$pun_user['disp_topics'].') GROUP BY t.id ORDER BY t.sticky DESC, t.last_post DESC';
			break;

		default:
			$sql = 'SELECT p.poster_id AS has_posted, t.id, t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.sticky, t.moved_to, t.question FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'posts AS p ON t.id=p.topic_id AND p.poster_id='.$pun_user['id'].' WHERE t.forum_id='.$id.' GROUP BY t.id, t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.sticky, t.moved_to, t.question, p.poster_id ORDER BY sticky DESC, '.(($cur_forum['sort_by'] == '1') ? 'posted' : 'last_post').' DESC LIMIT '.$start_from.', '.$pun_user['disp_topics'];
			break;

	}
}

$result = $db->query($sql) or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());

// If there are topics in this forum.
if ($db->num_rows($result))
{
	while ($cur_topic = $db->fetch_assoc($result))
	{
		$icon_text = $lang_common['Normal icon'];
		$item_status = '';
		$icon_type = 'icon';
        
        // Does this topic have new posts ?
        $has_new_post = !$pun_user['is_guest'] && topic_is_new($cur_topic['id'], $id,  $cur_topic['last_post']) && $cur_topic['moved_to'] == null;
        
        // Forum 'comments'
        if ($is_comment_forum && !isset($_GET['forum']))
        {
            $doc_param = get_doc_param($cur_topic['subject']);
            $topic_url = $doc_param[2];
            $last_post_url = $topic_url;
            $doc = '&amp;doc='.$doc_param[4];
        }
        else
        {
            $topic_url = 'viewtopic.php?id='.$cur_topic['id'].$show_link_to_forum;
            $last_post_url = 'viewtopic.php?pid='.$cur_topic['last_post_id'].$show_link_to_forum;
            $doc = '';
        }

		if ($cur_topic['moved_to'] == null)
			$last_post = '<a href="'.$last_post_url.'#p'.$cur_topic['last_post_id'].'" rel="nofollow">'.format_time($cur_topic['last_post']).'</a> <span class="byuser">'.$lang_common['by'].'&nbsp;'.pun_htmlspecialchars($cur_topic['last_poster']).'</span>';
		else
			$last_post = '&nbsp;';

		if ($pun_config['o_censoring'] == '1')
			$cur_topic['subject'] = censor_words($cur_topic['subject']);

/*      	if ($cur_topic['question'] != '') 
		{
			if ($pun_config['o_censoring'] == '1')
				$cur_topic['question'] = censor_words($cur_topic['question']);

			if ($cur_topic['moved_to'] != 0)
            {
				$subject = $lang_forum['Moved'].': ' . $lang_polls['Poll'].': <a href="viewtopic.php?id='.$cur_topic['moved_to'].'">'.pun_htmlspecialchars($cur_topic['subject']).'</a>';
                $by_user = ' <span class="byuser">'.$lang_common['by'].'&nbsp;'.pun_htmlspecialchars($cur_topic['poster']).'</span><br />[ '.pun_htmlspecialchars($cur_topic['question']).' ]';
            }
			else if ($cur_topic['closed'] == '0')
            {
				$subject = $lang_polls['Poll'].': <a href="'.$topic_url.'">'.pun_htmlspecialchars($cur_topic['subject']).'</a>';
                $by_user = ' <span class="byuser">'.$lang_common['by'].'&nbsp;'.pun_htmlspecialchars($cur_topic['poster']).'</span><br />[ '.pun_htmlspecialchars($cur_topic['question']).' ]';
            }
			else
			{
				$subject = $lang_polls['Poll'] . ': <a href="'.$topic_url.'">'.pun_htmlspecialchars($cur_topic['subject']).'</a>';
                $by_user = ' <span class="byuser">'.$lang_common['by'].'&nbsp;'.pun_htmlspecialchars($cur_topic['poster']).'</span><br />[ '.pun_htmlspecialchars($cur_topic['question']).' ]';
				$icon_text = $lang_common['Closed icon'];
				$item_status = 'iclosed';
			}

			if ($has_new_post)
			{
				$icon_text .= ' '.$lang_common['New icon'];
				$item_status .= ' inew';
				$icon_type = 'icon inew';	
				$subject = '<strong>'.$subject.'</strong>';
				$subject_new_posts = '<span class="newtext">[&nbsp;<a href="viewtopic.php?id='.$cur_topic['id'].'&amp;action=new" title="'.$lang_common['New posts info'].'">'.$lang_common['New posts'].'</a>&nbsp;]</span>';
			}
			else
            {
				$subject_new_posts = null;
            }

            $subject = $subject.$by_user;

			// Should we display the dot or not? :)
			if (!$pun_user['is_guest'] && $pun_config['o_show_dot'] == '1')
			{
				if ($cur_topic['has_posted'] == $pun_user['id'])
					$subject = '<strong>&middot;</strong>&nbsp;'.$subject;
				else
					$subject = '&nbsp;&nbsp;'.$subject;
			}
		} 
		//else 
		//{
*/
		if ($cur_topic['moved_to'] != 0)
        {
			$subject = $lang_forum['Moved'].': <a href="viewtopic.php?id='.$cur_topic['moved_to'].'">'.pun_htmlspecialchars($cur_topic['subject']).'</a>';
            $by_user = ' <span class="byuser">'.$lang_common['by'].'&nbsp;'.pun_htmlspecialchars($cur_topic['poster']).'</span>';
        }
        else if ($cur_topic['closed'] == '0')
        {
			$subject = '<a href="'.$topic_url.'"'.$rel.'>'.pun_htmlspecialchars($cur_topic['subject']).'</a>';
            $by_user = ' <span class="byuser">'.$lang_common['by'].'&nbsp;'.pun_htmlspecialchars($cur_topic['poster']).'</span>';
        }
        else
		{
			$subject = '<a href="'.$topic_url.'"'.$rel.'>'.pun_htmlspecialchars($cur_topic['subject']).'</a>';
            $by_user = ' <span class="byuser">'.$lang_common['by'].'&nbsp;'.pun_htmlspecialchars($cur_topic['poster']).'</span>';
			$icon_text = $lang_common['Closed icon'];
			$item_status = 'iclosed';
		}

		if ($has_new_post)
		{
			$icon_text .= ' '.$lang_common['New icon'];
			$item_status .= ' inew';
			$icon_type = 'icon inew';
			$subject = '<strong>'.$subject.'</strong>';
			$subject_new_posts = '<span class="newtext">[&nbsp;<a href="viewtopic.php?id='.$cur_topic['id'].'&amp;action=new'.$doc.$show_link_to_forum.'" title="'.$lang_common['New posts info'].'">'.$lang_common['New posts'].'</a>&nbsp;]</span>';
		}
        else
        {
			$subject_new_posts = null;
        }
        
        $subject = $subject.$by_user;

		// Should we display the dot or not? :)
		if (!$pun_user['is_guest'] && $pun_config['o_show_dot'] == '1')
		{
			if ($cur_topic['has_posted'] == $pun_user['id'])
				$subject = '<strong>&middot;</strong>&nbsp;'.$subject;
			else
				$subject = '&nbsp;&nbsp;'.$subject;
		}

		if ($cur_topic['sticky'] == '1')
		{
			$subject = '<span class="stickytext">'.$lang_forum['Sticky'].': </span>'.$subject;
			$item_status .= ' isticky';
			$icon_text .= ' '.$lang_forum['Sticky'];
		}

        if ($is_comment_forum && !isset($_GET['forum']))
        {
            $num_pages_topic = 1;
        }
        else
        {
            $num_pages_topic = ceil(($cur_topic['num_replies'] + 1) / $pun_user['disp_posts']);
        }

		if ($num_pages_topic > 1)
			$subject_multipage = '[&nbsp;'.paginate($num_pages_topic, -1, 'viewtopic.php?id='.$cur_topic['id'].$show_link_to_forum, $rel).'&nbsp;]';
		else
			$subject_multipage = null;

		// Should we show the "New posts" and/or the multipage links?
		if (!empty($subject_new_posts) || !empty($subject_multipage))
		{
			$subject .= '&nbsp; '.(!empty($subject_new_posts) ? $subject_new_posts : '');
			$subject .= !empty($subject_multipage) ? ' '.$subject_multipage : '';
		}

?>
				<tr<?php if ($item_status != '') echo ' class="'.trim($item_status).'"'; ?>>
					<td class="tcl">
						<div class="intd">
							<div class="<?php echo $icon_type ?>"><div class="nosize"><?php echo trim($icon_text) ?></div></div>
							<div class="tclcon">
								<?php echo $subject."\n" ?>
							</div>
						</div>
					</td>
					<?php if (!$mobile_version): ?>
					<td class="tc2"><?php echo ($cur_topic['moved_to'] == null) ? $cur_topic['num_replies'] : '&nbsp;' ?></td>
					<?php endif ?>
					<td class="tcr"><?php echo $last_post ?></td>
				</tr>
<?php

	}
}
else
{

?>
				<tr>
					<td class="tcl" colspan="4"><?php echo $lang_forum['Empty forum'] ?></td>
				</tr>
<?php

}

?>
			</tbody>
			</table>
		</div>
	</div>
</div>
<div class="linksb">
	<div class="inbox">
		<p class="pagelink conl"><?php echo $paging_links ?></p>
		<p class="postlink conr"><?php echo $post_link ?></p>
		<?php echo $forum_links;
        echo $moderator_list ?>
		<div class="clearer"></div>
	</div>
</div>
<?php
}
?>
<?php

$forum_id = $id;
require PUN_ROOT.'footer.php';

