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

define('FORUM_FEED', 'all');

if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view']);

if (isset($_GET['lang']))
{
    $where_cat_culture = "cat_culture='" . $_GET['lang'] . "' AND";
}
else
{
    $where_cat_culture =  '';
}

// Load the index.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/index.php';

$page_title = pun_htmlspecialchars($lang_common['Forum'].' - '.$pun_config['o_board_title']);
$mobile_version = c2cTools::mobileVersion();
$footer_style = 'index';
define('PUN_ALLOW_INDEX', 1);
require PUN_ROOT.'header.php';

################################################################################
########################### Sub Forum MOD (start) ##############################
################################################################################
$sfcount=0;
$sfdb = array($_parent_id_, $_topics_, $_posts_, $_last_post_id_, $_last_poster_, $_last_post_);
$forums_info = $db->query('SELECT num_topics, num_posts, parent_forum_id, last_post_id, last_poster, last_post, id, forum_name FROM '.$db->prefix.'forums ORDER BY disp_position') or error(implode($db->error(),''),__FILE__,__LINE__,$db->error());
while($current = $db->fetch_assoc($forums_info)) {
 if ($current['parent_forum_id'] != 0)
  {
   $sfdb[$sfcount][0] = $current['parent_forum_id'];
   $sfdb[$sfcount][1] = $current['num_topics'];
   $sfdb[$sfcount][2] = $current['num_posts'];
   $sfdb[$sfcount][3] = $current['last_post_id'];
   $sfdb[$sfcount][4] = $current['last_poster'];
   $sfdb[$sfcount][5] = $current['last_post'];
   $sfdb[$sfcount][6] = $current['id'];
   $sfdb[$sfcount][7] = $current['forum_name'];
   $sfcount++;
  }
}
################################################################################
########################### Sub Forum MOD ( end ) ##############################
################################################################################

$new_topics = get_all_new_topics();
// Print the categories and forums
    // MOD : show last topic subject  ### réactivé ###
$result = $db->query('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.forum_desc, f.redirect_url, f.moderators, f.num_topics, f.num_posts, f.last_post, f.last_post_id, f.last_poster, f.parent_forum_id, t.subject, t.id AS last_topic_id FROM '.$db->prefix.'categories AS c INNER JOIN '.$db->prefix.'forums AS f ON c.id=f.cat_id LEFT JOIN '.$db->prefix.'topics AS t ON (t.last_post_id=f.last_post_id) LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE ' . $where_cat_culture . ' (fp.read_forum IS NULL OR fp.read_forum=1) AND (f.parent_forum_id IS NULL OR f.parent_forum_id=0) ORDER BY c.disp_position, c.id, f.disp_position', true) or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

// $result = $db->query('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.forum_desc, f.redirect_url, f.moderators, f.num_topics, f.num_posts, f.last_post, f.last_post_id, f.last_poster, f.parent_forum_id FROM '.$db->prefix.'categories AS c INNER JOIN '.$db->prefix.'forums AS f ON c.id=f.cat_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE ' . $where_cat_culture . ' (fp.read_forum IS NULL OR fp.read_forum=1) AND (f.parent_forum_id IS NULL OR f.parent_forum_id=0) ORDER BY c.disp_position, c.id, f.disp_position', true) or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());


$pub_forums = explode(', ', PUB_FORUMS);
$cur_category = 0;
$cat_count = 0;
while ($cur_forum = $db->fetch_assoc($result))
{
// At the beginning of the modification allowing to avoid the conflicts of compatibility between the sub-forums and the mod Mark Topic As read
	if (!empty($sfdb))
	{
		foreach ($sfdb as $sub_forums)
		{
			if ($cur_forum['fid'] == $sub_forums[0])
			{
				if($new_topics[$sub_forums[0]] != "")
				{
					foreach($new_topics[$sub_forums[6]] as $topic_id => $last_post)
					{
						$new_topics[$cur_forum['fid']][$topic_id] = $last_post;
					}
				}
			}
		}
	}
// At the end of the modification allowing to avoid the conflicts of compatibility between the sub-forums and the mod Mark Topic As read

    list($is_admmod, $is_c2c_board) = get_is_admmod($cur_forum['fid'], $cur_forum['moderators'], $pun_user);
    if (!$is_c2c_board)
    {
        continue;
    }
    
	$moderators = '';

	if ($cur_forum['cid'] != $cur_category)	// A new category since last iteration?
	{
		if ($cur_category != 0)
			echo "\t\t\t".'</tbody>'."\n\t\t\t".'</table>'."\n\t\t".'</div>'."\n\t".'</div>'."\n".'</div>'."\n\n";

		++$cat_count;

?>
<div id="idx<?php echo $cat_count ?>" class="blocktable">
	<h2><span><?php echo pun_htmlspecialchars($cur_forum['cat_name']) ?></span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
			<thead>
				<tr>
					<th class="tcl" scope="col"><?php echo $lang_common['Forum'] ?></th>
					<?php if (!$mobile_version): ?>
					<th class="tc2" scope="col"><?php echo $lang_index['Topics'] ?></th>
					<th class="tc3" scope="col"><?php echo $lang_common['Posts'] ?></th>
					<?php endif ?>
					<th class="tcr" scope="col"><?php echo $lang_common['Last post'] ?></th>
				</tr>
			</thead>
			<tbody>
<?php

		$cur_category = $cur_forum['cid'];
	}

	$item_status = '';
	$icon_text = $lang_common['Normal icon'];
	$icon_type = 'icon';

	// Are there new posts?
	if (!$pun_user['is_guest'] && forum_is_new($cur_forum['fid'], $cur_forum['last_post']))
	{
		$item_status = 'inew';
		$icon_text = $lang_common['New icon'];
		$icon_type = 'icon inew';
	}
    
    // Is this a pub forum ?
    if (in_array($cur_forum['fid'], $pub_forums))
    {
        $rel = ' rel="nofollow"';
    }
    else
    {
        $rel = '';
    }

	// Is this a redirect forum?
	if ($cur_forum['redirect_url'] != '')
	{
		$forum_field = '<h3><a href="'.pun_htmlspecialchars($cur_forum['redirect_url']).'" title="'.$lang_index['Link to'].' '.pun_htmlspecialchars($cur_forum['redirect_url']).'">'.pun_htmlspecialchars($cur_forum['forum_name']).'</a></h3>';
		$num_topics = $num_posts = '&nbsp;';
		$item_status = 'iredirect';
		$icon_text = $lang_common['Redirect icon'];
		$icon_type = 'icon';
        $l_post = '';
	}
	else
	{
        $forum_field = '<h3><a href="viewforum.php?id='.$cur_forum['fid'].'"'.$rel.'>'.pun_htmlspecialchars($cur_forum['forum_name']).'</a></h3>';
                ################################################################################
                ########################### Sub Forum MOD (start) ##############################
                ################################################################################
                  $n_t = 0;
                  $n_p = 0;
                  $l_tid = $cur_forum['last_topic_id'];
                  $l_pid = $cur_forum['last_post_id'];
                  $l_pr = $cur_forum['last_poster'];
                  $l_post = $cur_forum['last_post'];
                  for ($i = 0; $i < $sfcount; $i++)
                  {
                   if ($sfdb[$i][0] == $cur_forum['fid'])
                    {
                     $n_t = $n_t + $sfdb[$i][1];
                     $n_p = $n_p + $sfdb[$i][2];
                     if ($l_pid < $sfdb[$i][3])
                      {
                       $l_pid = $sfdb[$i][3];
                       $l_pr = $sfdb[$i][4];
                       $l_post = $sfdb[$i][5];
                      }
                    }
                  }
                  $num_topics = $n_t + $cur_forum['num_topics'];
                  $num_posts = $n_p + $cur_forum['num_posts'];
                ################################################################################
                ########################### Sub Forum MOD ( end ) ##############################
                ################################################################################
	}

    if ($cur_forum['forum_desc'] != '' && !$mobile_version)
    {
        $forum_field .= "\n\t\t\t\t\t\t\t\t".$cur_forum['forum_desc'];
    }

    // If there is a last_post/last_poster.
    if ($l_post != '')
    {
    // MOD : show last topic subject  ### résactivé ###
        $subject_latin = utf8_decode($cur_forum['subject']);
        if (strlen($subject_latin) > 40)
        {
            $cur_forum['subject'] = utf8_encode(substr($subject_latin, 0, 36)).'...';
        }
        $last_post = '<a href="viewtopic.php?id='.$l_tid.'&amp;action=new"'.$rel.'>'.$cur_forum['subject'].'</a><br /><a href="viewtopic.php?pid='.$l_pid.'#p'.$l_pid.'"'.$rel.'>'.format_time($l_post).'</a> <span class="byuser">'.$lang_common['by'].' '.pun_htmlspecialchars($l_pr).'</span>';
        
    //    $last_post = '<a href="viewtopic.php?pid='.$l_pid.'#p'.$l_pid.'">'.format_time($l_post).'</a> <span class="byuser">'.$lang_common['by'].' '.pun_htmlspecialchars($l_pr).'</span>';
    }
    else
    {
        $last_post = '&nbsp;';
    }

        if (!empty($sfdb))
        {
            foreach ($sfdb as $sub_forums)
            {
                if ($cur_forum['fid'] == $sub_forums[0] && !$pun_user['is_guest'] && $sub_forums[5] > $pun_user['last_visit'])
                {
                    $item_status = 'inew';
                    $icon_text = $lang_common['New icon'];
                    $icon_type = 'icon inew';
                }
            }
        }

?>
 				<tr<?php if ($item_status != '') echo ' class="'.$item_status.'"'; ?>>
					<td class="tcl">
						<div class="intd">
							<div class="<?php echo $icon_type ?>"><div class="nosize"><?php echo $icon_text ?></div></div>
							<div class="tclcon">
								<?php echo $forum_field."\n".$moderators ?>
        <?php
        $sub_forums_list = array();
        if(!empty($sfdb)) {
            foreach ($sfdb as $sub_forums)
            {
                if($cur_forum['fid'] == $sub_forums[0])
                            {
                $sub_forums_list[] = '<a href="viewforum.php?id='.$sub_forums[6].'">'.pun_htmlspecialchars($sub_forums[7]).'</a>';
                }
            }
            // EDIT THIS FOR THE DISPLAY STYLE OF THE SUBFORUMS ON MAIN PAGE
            if(!empty($sub_forums_list))
            {
                // Leave one $sub_forums_list commented out to use the other (between the ###..)
                ################################
                // This is Single Line Wrap Style
                $sub_forums_list = "\t\t\t\t\t\t\t\t".'<em>Sub Forums:</em> '.implode(', ', $sub_forums_list)."\n";
                // This is List Style
                //$sub_forums_list = "\n".'<b><em>Sub Forums:</em></b><br />&nbsp; -- &nbsp;'.implode('<br />&nbsp; -- &nbsp;', $sub_forums_list)."\n";
                ################################
                if ($cur_forum['forum_desc'] != NULL)
                {
                    echo "<br />";
                }
                // TO TURN OFF DISPLAY OF SUBFORUMS ON INDEX PAGE, COMMENT OUT THE FOLLOWING LINE
                echo "$sub_forums_list";
            }
        }
        ?>
							</div>
						</div>
					</td>
					<?php if (!$mobile_version): ?>
					<td class="tc2"><?php echo $num_topics ?></td>
					<td class="tc3"><?php echo $num_posts ?></td>
					<?php endif ?>
					<td class="tcr"><?php echo $last_post ?></td>
				</tr>
<?php

}

// Did we output any categories and forums?
if ($cur_category > 0)
	echo "\t\t\t".'</tbody>'."\n\t\t\t".'</table>'."\n\t\t".'</div>'."\n\t".'</div>'."\n".'</div>'."\n\n";
else
	echo '<div id="idx0" class="block"><div class="box"><div class="inbox"><p>'.$lang_index['Empty board'].'</p></div></div></div>';
require PUN_ROOT.'footer.php';
