<?php
/***********************************************************************

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


// Tell header.php to use the admin template
define('PUN_ADMIN_CONSOLE', 1);

define('PUN_ROOT', './');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'include/common_admin.php';


if ($pun_user['g_id'] > PUN_MOD)
	message($lang_common['No permission']);


if (isset($_GET['action']) || isset($_POST['prune']))
{
	$prune_days_start = $_POST['req_prune_days_start'];
    if ($prune_days_start == '')
    {
        $prune_days_start = '0';
    }
	if (!@preg_match('#^\d+$#', $prune_days_start))
		message('Days must be a positive integer.');

	$prune_date_start = time() - ($prune_days_start*86400);

	$prune_days_end = $_POST['req_prune_days_end'];
    if ($prune_days_end == '')
    {
        $prune_days_end = '0';
    }
	if (!@preg_match('#^\d+$#', $prune_days_end))
		message('Days must be a positive integer.');

	$prune_date_end = time() - ($prune_days_end*86400);

	$prune_from = $_POST['prune_from'];

	// Concatenate together the query for counting number or topics to prune
	$sql_topics = 'SELECT COUNT(id) FROM '.$db->prefix.'topics WHERE last_post>'.$prune_date_end.' AND last_post<'.$prune_date_start.' AND moved_to IS NULL';
	
    $sql_posts = 'SELECT COUNT(id) FROM '.$db->prefix.'posts WHERE posted>'.$prune_date_start.' AND posted<'.$prune_date_end;

	if (!in_array('all', $prune_from))
	{
		$sql_topics .= ' AND forum_id IN ('.implode(',', $prune_from).')';
		
        $sql_posts = 'SELECT COUNT(p.id) FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id WHERE t.forum_id IN ('.implode(',', $prune_from).') AND p.posted>'.$prune_date_end.' AND p.posted<'.$prune_date_start;

		// Fetch the forum name (just for cosmetic reasons)
		$result = $db->query('SELECT forum_name FROM '.$db->prefix.'forums WHERE id IN ('.implode(',', $prune_from).')') or error('Unable to fetch forum name', __FILE__, __LINE__, $db->error());
        $forum_name_list = '';
        while ($forum = $db->fetch_assoc($result))
        {
            $forum_name_list .= ' - ' . pun_htmlspecialchars($forum['forum_name']) . '<br />';
        }
	}
	else
		$forum_name_list = "\t" . 'all forums<br />';

	$result = $db->query($sql_topics) or error('Unable to fetch topic prune count', __FILE__, __LINE__, $db->error());
	$num_topics = $db->result($result);

	if (!$num_topics)
		message('There are no topics that are '.$prune_days_start.' to '.$prune_days_end.' days old. Please decrease the values of "Days old" and try again.');

	$result = $db->query($sql_posts) or error('Unable to fetch topic prune count', __FILE__, __LINE__, $db->error());
	$num_posts = $db->result($result);

	if (!$num_posts)
		message('There are no posts that are '.$prune_days_start.' to '.$prune_days_end.' days old. Please decrease the values of "Days old" and try again.');


	$page_title = pun_htmlspecialchars($pun_config['o_board_title']).' / Admin / Statistics';
	require PUN_ROOT.'header.php';

	generate_admin_menu('stats');

?>
	<div class="blockform">
		<h2><span>Forum statistics</span></h2>
		<div class="box">
			<form method="post" action="admin_stats.php?action=foo">
				<div class="inform">
					<fieldset>
						<legend>Number of posts</legend>
						<div class="infldset">
							<p>Number of posts older than <?php echo $prune_days_start ?> days and earlier than <?php echo $prune_days_end ?> days from :<br />
                            <?php echo $forum_name_list;
                            echo $num_topics . ' topics<br />';
                            echo $num_posts . ' posts';?>
                            </p>
						</div>
					</fieldset>
				</div>
				<p><a href="javascript:history.go(-1)">Go back</a></p>
			</form>
		</div>
	</div>
	<div class="clearer"></div>
</div>
<?php

	require PUN_ROOT.'footer.php';
}


else
{
	$page_title = pun_htmlspecialchars($pun_config['o_board_title']).' / Admin / Statistics';
	$required_fields = array('req_prune_days_end' => 'Days old end');
	$focus_element = array('stats', 'req_prune_days_start');
	require PUN_ROOT.'header.php';

	generate_admin_menu('stats');

?>
	<div class="blockform">
		<h2><span>Forum statistics</span></h2>
		<div class="box">
			<form id="stats" method="post" action="admin_stats.php?action=foo" onsubmit="return process_form(this)">
				<div class="inform">
				<input type="hidden" name="form_sent" value="1" />
					<fieldset>
						<legend>Num of topics and posts</legend>
						<div class="infldset">
							<table class="aligntop">
								<tr>
									<th scope="row">Days old start</th>
									<td>
										<input type="text" name="req_prune_days_start" size="4" maxlength="4" tabindex="1" />
										<span>The number of minimum days "old".</span>
									</td>
								</tr>
								<tr>
									<th scope="row">Days old end</th>
									<td>
										<input type="text" name="req_prune_days_end" size="4" maxlength="4" tabindex="2" />
										<span>The number of maximum days "old".</span>
									</td>
								</tr>
								<tr>
									<th scope="row">Stats from forum</th>
									<td>
										<select name="prune_from[]" multiple="multiple" size="10" tabindex="3">
											<option value="all">All forums</option>
<?php

	$result = $db->query('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name FROM '.$db->prefix.'categories AS c INNER JOIN '.$db->prefix.'forums AS f ON c.id=f.cat_id WHERE f.redirect_url IS NULL ORDER BY c.disp_position, c.id, f.disp_position') or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

	$cur_category = 0;
	while ($forum = $db->fetch_assoc($result))
	{
		if ($forum['cid'] != $cur_category)	// Are we still in the same category?
		{
			if ($cur_category)
				echo "\t\t\t\t\t\t\t\t\t\t\t".'</optgroup>'."\n";

			echo "\t\t\t\t\t\t\t\t\t\t\t".'<optgroup label="'.pun_htmlspecialchars($forum['cat_name']).'">'."\n";
			$cur_category = $forum['cid'];
		}

		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$forum['fid'].'">'.pun_htmlspecialchars($forum['forum_name']).'</option>'."\n";
	}

?>
											</optgroup>
										</select>
										<span>The forum from which you want to extract statistics.</span>
									</td>
								</tr>
							</table>
							<div class="fsetsubmit"><input type="submit" name="prune" value="OK" tabindex="4" /></div>
						</div>
					</fieldset>
				</div>
			</form>
		</div>
	</div>
	<div class="clearer"></div>
</div>
<?php

	require PUN_ROOT.'footer.php';
}
