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


// The contents of this file are very much inspired by the file search.php
// from the phpBB Group forum software phpBB2 (http://www.phpbb.com).


define('PUN_ROOT', './');
require PUN_ROOT.'include/common.php';


// Load the search.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/search.php';

// Load poll language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/polls.php';

if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view']);
else if ($pun_user['g_search'] == '0')
	message($lang_search['No search permission']);

$c2c_board_condition = '';

if ($pun_user['g_id'] == PUN_MOD)
{
    // Fetch some info about the forum
    $result = $db->query('SELECT f.moderators FROM '.$db->prefix.'forums AS f LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.C2C_BOARD_FORUM) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());

    if (!$db->num_rows($result))
        message($lang_common['Bad request']);

    $c2c_board_forum = $db->fetch_assoc($result);

    // Sort out who the moderators are and if we are currently a moderator (or an admin)
    list($is_admmod, $is_c2c_board) = get_is_admmod(C2C_BOARD_FORUM, $c2c_board_forum['moderators'], $pun_user);
    $is_admmod = true;
    if (!$is_c2c_board)
    {
        $c2c_board_condition = ' AND (f.id != '.C2C_BOARD_FORUM.')';
    }
}
elseif ($pun_user['g_id'] == PUN_ADMIN)
{
    $is_admmod = true;
    $is_c2c_board = true;
}
elseif ($pun_user['g_id'] > PUN_GUEST)
{
    $is_admmod = false;
    $is_c2c_board = true;
}
else
{
    $is_admmod = false;
    $is_c2c_board = false;
}


// Detect two byte character sets
$multibyte = (isset($lang_common['lang_multibyte']) && $lang_common['lang_multibyte']) ? true : false;

// Default title of search results
$search_title = $lang_search['Search'];

// Figure out what to do :-)
if (isset($_GET['action']) || isset($_GET['search_id']))
{
    if (isset($_GET['action']))
    {
        if ($_GET['action'] == 'show_new' && $pun_user['is_guest'])
        {
            $_GET['action'] = 'show_24h';
        }
        $action = $_GET['action'];
    }
    else
    {
        $action = null;
    }
    
    $forum = array();
	$forum_tmp = (isset($_GET['forum'])) ? $_GET['forum'] : array('-1');
    foreach ($forum_tmp as $tmp_id)
    {
        if (preg_match('/^(-1|\d+)$/', $tmp_id))
        {
            $forum[] = $tmp_id;
        }
    }
    
    if (in_array(strval(C2C_BOARD_FORUM), $forum) && !$is_c2c_board)
        message($lang_search['No search permission']);
   
	$sort_dir = (isset($_GET['sort_dir'])) ? (($_GET['sort_dir'] == 'DESC') ? 'DESC' : 'ASC') : 'DESC';
	if (isset($search_id)) unset($search_id);
    
    // Set title
    if (isset($_GET['title']))
    {
        $search_action = $_GET['title'];
    }
    else
    {
        $search_action = $action;
    }
    
    if ($search_action == 'show_new')
    {
        $search_title = $lang_search['New posts'];
    }
    else if ($search_action == 'show_24h')
    {
        $search_title = $lang_search['Recent posts'];
    }
    else if ($search_action == 'show_user')
    {
        $search_title = $lang_search['Posts from user'];
    }
    else if ($search_action == 'show_user_topics')
    {
        $search_title = $lang_search['Topics from user'];
    }
    else if ($search_action == 'show_subscriptions')
    {
        $search_title = $lang_search['Subscriptions topics'];
    }
    else if ($search_action == 'show_unanswered')
    {
        $search_title = $lang_search['Unanswered topics'];
    }
    else if ($search_action == 'show_news')
    {
        $search_title =  $lang_search['News'];
    }
    else
    {
        $search_title = $lang_search['Search results'];
    }
    
    // If a language was supplied
    if (isset($_GET['lang']) && !empty($_GET['lang']))
    {
        $languages = trim(preg_replace('#\W+#', ' ', $_GET['lang']));
        $languages = explode(' ', $languages);
        $search_title .= ' [' . implode(', ', $languages) . ']';
        $where_languages = implode('\',\'', $languages);
        $where_culture = "f.culture IN ('" . $where_languages . "') AND ";
    }
    else
    {
        $where_culture =  '';
    }
    
    $context_title = $search_title;
    
	// If a search_id was supplied
	if (isset($_GET['search_id']))
	{
		$search_id = intval($_GET['search_id']);
		if ($search_id < 1)
			message($lang_common['Bad request']);
	}
	// If it's a regular search (keywords and/or author)
	else if ($action == 'search')
	{
		$keywords = (isset($_GET['keywords'])) ? strtolower(trim($_GET['keywords'])) : null;
		$author = (isset($_GET['author'])) ? strtolower(trim($_GET['author'])) : null;
		$author_type = (isset($_GET['author_type'])) ? intval(trim($_GET['author_type'])) : 0;
		$guest = (isset($_GET['guest'])) ? intval(trim($_GET['guest'])) : 0;
        $author_ids = array();
        if (isset($_GET['author_id']))
        {
            $author_id = $_GET['author_id'];
            $author_id = trim(preg_replace('#\D+#', ' ', $author_id));
            $author_id = explode(' ', $author_id);
            foreach ($author_id as $a_id)
            {
                if ($a_id >= 2)
                {
                    $author_ids[] = $a_id;
                }
            }
            if (!empty($author_ids) && $author_type == 0 && empty($author) && $guest == 0)
            {
                $author_type = 2;
            }
        }
        if (empty($author_ids) && $author_type >= 2)
        {
            $author_type = 1;
        }

		if (preg_match('#^[\*%]+$#', $keywords) || strlen(str_replace(array('*', '%'), '', $keywords)) < 3)
			$keywords = '';

		if (preg_match('#^[\*%]+$#', $author) || strlen(str_replace(array('*', '%'), '', $author)) < 2)
			$author = '';
        
        $ip = '';
        $ip_condition = '';
        if ($is_admmod && isset($_GET['ip']))
        {
            $ip = trim($_GET['ip']);

            if (!empty($ip))
            {
                if (!@preg_match('/^[0-9.*]+$/', $ip))
                    message('The supplied IP address is not correctly formatted.');

                if (@preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $ip))
                {
                    $ip_condition = ' AND p.poster_ip=\''.$db->escape($ip).'\'';
                }
                elseif (@preg_match('/^[0-9]{1,3}\.[0-9.]*\*$/', $ip))
                {
                    $ip_condition = ' AND p.poster_ip LIKE \''.$db->escape(str_replace('*', '', $ip)).'%\'';
                }
                else
                {
                    message('The supplied IP address is not correctly formatted.');
                }
            }
        }

		if ($author)
			$author = str_replace('*', '%', $author);

		$show_as = (isset($_GET['show_as'])) ? $_GET['show_as'] : 'posts';
		$sort_by = (isset($_GET['sort_by'])) ? intval($_GET['sort_by']) : null;
		$search_in = (!isset($_GET['search_in']) || $_GET['search_in'] == 'all') ? 0 : (($_GET['search_in'] == 'message') ? 1 : -1);
	}
	// If it's a user search (by id)
	else if ($action == 'show_user' || $action == 'show_user_topics')
	{
		if (isset($_GET['user_id']))
        {
            $user_id = intval($_GET['user_id']);
            if ($user_id < 2)
            {
                message($lang_common['Bad request']);
            }
        }
        elseif (!$pun_user['is_guest'])
        {
            $user_id = $pun_user['id'];
        }
        else
        {
            message($lang_common['Bad request']);
        }
	}
	else
	{
		if ($action != 'show_new' && $action != 'show_24h' && $action != 'show_unanswered' && $action != 'show_subscriptions' && $action != 'show_news')
			message($lang_common['Bad request']);
	}

	// If a valid search_id was supplied we attempt to fetch the search results from the db
	if (isset($search_id))
	{
		$ident = ($pun_user['is_guest']) ? get_remote_address() : $pun_user['username'];

		$result = $db->query('SELECT search_data FROM '.$db->prefix.'search_cache WHERE id='.$search_id.' AND ident=\''.$db->escape($ident).'\'') or error('Unable to fetch search results', __FILE__, __LINE__, $db->error());
		if ($row = $db->fetch_assoc($result))
		{
			$temp = unserialize($row['search_data']);

			$search_results = $temp['search_results'];
			$num_hits = $temp['num_hits'];
			$sort_by = $temp['sort_by'];
			$sort_dir = $temp['sort_dir'];
			$show_as = $temp['show_as'];

			unset($temp);
		}
		else
			message($lang_search['No hits']);
	}
	else
	{
		$keyword_results = $author_results = array();

		// Search a specific forum?
		$forum_sql = (!in_array('-1', $forum) || (in_array('-1', $forum) && $pun_config['o_search_all_forums'] == '0')) ? ' AND t.forum_id IN ('.implode(',', $forum).')' : '';

		if ($action == 'search')
		{
			// If it's a search for keywords
			if ($keywords)
			{
				$stopwords = (array)@file(PUN_ROOT.'lang/'.$pun_user['language'].'/stopwords.txt');
				$stopwords = array_map('trim', $stopwords);

				// Are we searching for multibyte charset text?
				if ($multibyte)
				{
					// Strip out excessive whitespace
					$keywords = trim(preg_replace('#\s+#', ' ', $keywords));

					$keywords_array = explode(' ', $keywords);
				}
				else
				{
					// Filter out non-alphabetical chars
					$noise_match = array('^', '$', '&', '(', ')', '<', '>', '`', '\'', '"', '|', ',', '@', '_', '?', '%', '~', '[', ']', '{', '}', ':', '\\', '/', '=', '#', '\'', ';', '!', '¤');
					$noise_replace = array(' ', ' ', ' ', ' ', ' ', ' ', ' ', '',  '',   ' ', ' ', ' ', ' ', '',  ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', '' ,  ' ', ' ', ' ', ' ',  ' ', ' ', ' ');
					$keywords = str_replace($noise_match, $noise_replace, $keywords);

					// Strip out excessive whitespace
					$keywords = trim(preg_replace('#\s+#', ' ', $keywords));

					// Fill an array with all the words
					$keywords_array = explode(' ', $keywords);

					if (empty($keywords_array))
						message($lang_search['No hits']);

					while (list($i, $word) = @each($keywords_array))
					{
						$num_chars = pun_strlen($word);

						if ($num_chars < 3 || $num_chars > 20 || in_array($word, $stopwords))
							unset($keywords_array[$i]);
					}

					// Should we search in message body or topic subject specifically?
					$search_in_cond = ($search_in) ? (($search_in > 0) ? ' AND m.subject_match = 0' : ' AND m.subject_match = 1') : '';
				}

				$word_count = 0;
				$match_type = 'and';
				$result_list = array();
				@reset($keywords_array);
				while (list(, $cur_word) = @each($keywords_array))
				{
					switch ($cur_word)
					{
						case 'and':
						case 'or':
						case 'not':
							$match_type = $cur_word;
							break;

						default:
						{
							// Are we searching for multibyte charset text?
							if ($multibyte)
							{
								$cur_word = $db->escape('%'.str_replace('*', '', $cur_word).'%');
								$cur_word_like = ($db_type == 'pgsql') ? 'ILIKE \''.$cur_word.'\'' : 'LIKE \''.$cur_word.'\'';

								if ($search_in > 0)
									$sql = 'SELECT id FROM '.$db->prefix.'posts WHERE message '.$cur_word_like;
								else if ($search_in < 0)
									$sql = 'SELECT p.id FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id WHERE t.subject '.$cur_word_like.' GROUP BY p.id, t.id';
								else
									$sql = 'SELECT p.id FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id WHERE p.message '.$cur_word_like.' OR t.subject '.$cur_word_like.' GROUP BY p.id, t.id';
							}
							else
							{
								$cur_word = str_replace('*', '%', $cur_word);
								$sql = 'SELECT m.post_id FROM '.$db->prefix.'search_words AS w INNER JOIN '.$db->prefix.'search_matches AS m ON m.word_id = w.id WHERE w.word LIKE \''.$cur_word.'\''.$search_in_cond;
							}

							$result = $db->query($sql, true) or error('Unable to search for posts', __FILE__, __LINE__, $db->error());

							$row = array();
							while ($temp = $db->fetch_row($result))
							{
								$row[$temp[0]] = 1;

								if (!$word_count)
									$result_list[$temp[0]] = 1;
								else if ($match_type == 'or')
									$result_list[$temp[0]] = 1;
								else if ($match_type == 'not')
									$result_list[$temp[0]] = 0;
							}

							if ($match_type == 'and' && $word_count)
							{
								@reset($result_list);
								while (list($post_id,) = @each($result_list))
								{
									if (!isset($row[$post_id]))
										$result_list[$post_id] = 0;
								}
							}

							++$word_count;
							$db->free_result($result);

							break;
						}
					}
				}

				@reset($result_list);
				while (list($post_id, $matches) = @each($result_list))
				{
					if ($matches)
						$keyword_results[] = $post_id;
				}

				unset($result_list);
			}

			// If it's a search for author name like old version
			if ($author_type == 0 && !$guest && $author && strcasecmp($author, 'Guest') && strcasecmp($author, $lang_common['Guest']))
			{
				switch ($db_type)
				{
					case 'pgsql':
						$result = $db->query('SELECT id FROM '.$db->prefix.'users WHERE username ILIKE \''.$db->escape($author).'\'') or error('Unable to fetch users', __FILE__, __LINE__, $db->error());
						break;

					default:
						$result = $db->query('SELECT id FROM '.$db->prefix.'users WHERE username LIKE \''.$db->escape($author).'\'') or error('Unable to fetch users', __FILE__, __LINE__, $db->error());
						break;
				}

				if ($db->num_rows($result))
				{
					$author_ids = array();
					while ($row = $db->fetch_row($result))
                    {
						$author_ids[] = $row[0];
                    }
                    $author_type = 2;
                }
                else
                {
                    message($lang_search['No hits']);
                }
                $author = null;
            }
            
            // If it's a search for author ID
			if ($author_type == 2 && !$guest)
			{
                if (count($author_ids) == 1)
                {
                    $poster_condition = 'poster_id = ' . reset($author_ids);
                }
                else
                {
                    $poster_condition = 'poster_id IN (' . implode(', ', $author_ids) . ')';
                }
                $result = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE '.$poster_condition) or error('Unable to fetch matched posts list', __FILE__, __LINE__, $db->error());
                $search_ids = array();
                while ($row = $db->fetch_row($result))
                    $author_results[] = $row[0];
                $db->free_result($result); 
            }
            
			if ($author_type == 2 && !$guest && $keywords)
			{
				// If we searched for both keywords and author name we want the intersection between the results
				$search_ids = array_intersect($keyword_results, $author_results);
				unset($keyword_results, $author_results);
			}
			else if ($keywords)
				$search_ids = $keyword_results;
			else
				$search_ids = $author_results;

			$num_hits = count($search_ids);
            
            $post_condition = '';
			if (($author_type == 2 && !$guest) || $keywords)
            {
                if (!$guest && !$num_hits)
                {
                    message($lang_search['No hits']);
                }
                elseif ($num_hits)
                {
                    $post_condition = ' AND p.id IN('.implode(',', $search_ids).')';
                }
            }
			
            if ($guest)
            {
                $guest_condition = 'p.poster_id = 1';
            }
            
            // If there is a condition on poster ID
            $poster_condition = '';
            if (($author_type == 2 && $guest) || $author_type == 3)
			{
                $poster_condition = 'poster_id';
                if (count($author_ids) == 1)
                {
                    $ids = reset($author_ids);
                    if ($author_type == 2)
                    {
                        $poster_condition .= ' = ';
                    }
                    else
                    {
                        $poster_condition .= ' != ';
                    }
                    $poster_condition .= $ids;
                }
                else
                {
                    $poster_condition .= ' IN (' . implode(', ', $author_ids) . ')';
                    if ($author_type == 3)
                    {
                        $poster_condition = 'NOT (' . $poster_condition . ')';
                    }
                }
                
                if ($guest)
                {
                    if ($author_type == 2)
                    {
                        $poster_operand = ' OR ';
                    }
                    else
                    {
                        $poster_operand = ' AND ';
                    }
                    $poster_condition = '(' . $poster_condition . $poster_operand . $guest_condition . ')';
                }
                
                $poster_condition = ' AND ' . $poster_condition;
            }
            elseif ($author_type != 1 && $guest)
            {
                $poster_condition = ' AND ' . $guest_condition;
            }
            elseif ($author_type == 1 && !$guest)
            {
                $poster_condition = ' AND p.poster_id != 1';
            }
            
            // If there is a condition on poster name
            if ($author)
            {
                $poster_condition .= ' AND p.poster ILIKE \''.$db->escape($author).'\'';
            }
            
			if ($show_as == 'topics')
			{
				$result = $db->query('SELECT t.id FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON ('.$where_culture.'f.id=t.forum_id) LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1)'.$c2c_board_condition.$forum_sql.$post_condition.$poster_condition.$ip_condition.' GROUP BY t.id', true) or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());

				$search_ids = array();
				while ($row = $db->fetch_row($result))
					$search_ids[] = $row[0];

				$db->free_result($result);

				$num_hits = count($search_ids);
			}
			else
			{
				$result = $db->query('SELECT p.id FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON ('.$where_culture.'f.id=t.forum_id) LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1)'.$c2c_board_condition.$forum_sql.$post_condition.$poster_condition.$ip_condition, true) or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());

				$search_ids = array();
				while ($row = $db->fetch_row($result))
					$search_ids[] = $row[0];

				$db->free_result($result);

				$num_hits = count($search_ids);
			}
		}
		else if ($action == 'show_new' || $action == 'show_24h' || $action == 'show_user' || $action == 'show_user_topics' || $action == 'show_subscriptions' || $action == 'show_unanswered' || $action == 'show_news')
		{
	    // Excluded formus for the search
            $excluded_forum_ids = array();
            $where_forum_id = '';
            if (!empty($c2c_board_condition))
            {
                $excluded_forum_ids[] = C2C_BOARD_FORUM;
            }
			if (($action == 'show_new' || $action == 'show_24h') && !isset($_GET['all']))
            {
                $excluded_forum_ids[] = PUB_FORUMS;
                $excluded_forum_ids[] = LOVE_FORUMS;
            }
            if (isset($_GET['light']))
            {
                $excluded_forum_ids[] = PARTNER_FORUMS;
                $excluded_forum_ids[] = BUYSELL_FORUMS;
            }
            if (isset($_GET['simple']))
            {
                if ($action != 'show_user_topics')
                {
                    $excluded_forum_ids[] = COMMENTS_FORUM;
                }
                $forum_ids[] = ASSOCIATION_FORUMS;
                if (empty($c2c_board_condition))
                {
                    $excluded_forum_ids[] = C2C_BOARD_FORUM;
                }
            }
            if ($action == 'show_user_topics' && !isset($_GET['comments']))
            {
                $excluded_forum_ids[] = COMMENTS_FORUM;
            }
            if (count($excluded_forum_ids))
            {
                $where_excluded_forum_id = implode(', ', $excluded_forum_ids);
                if (count($excluded_forum_ids) == 1)
                {
                    $where_forum_id .= ' AND f.id != ' . $where_excluded_forum_id;
                }
                else
                {
                    $where_forum_id .= ' AND NOT (f.id IN (' . $where_excluded_forum_id . '))';
                }
            }
            
            // Included forums for the search
            $forum_ids = array();
            if (isset($_GET['fids']))
            {
            	$forum_ids = explode('-', $_GET['fids']);
            }
            if (isset($_GET['assoc']))
            {
            	$forum_ids[] = ASSOCIATION_FORUMS;
            }
            elseif (isset($_GET['v6']))
            {
            	$forum_ids[] = V6_FORUM;
            }
            if (isset($_GET['partner']))
            {
            	$forum_ids[] = PARTNER_FORUMS;
            }
            if (isset($_GET['buysell']))
            {
            	$forum_ids[] = BUYSELL_FORUMS;
            }
            if (isset($_GET['allnews']))
            {
            	$forum_ids[] = ALL_NEWS_FORUMS;
            }
            if (count($forum_ids))
            {
                $where_included_forum_id = implode(', ', $forum_ids);
                if (count($forum_ids) == 1)
                {
                    $where_forum_id .= ' AND f.id = ' . $where_included_forum_id;
                }
                else
                {
                    $where_forum_id .= ' AND (f.id IN (' . $where_included_forum_id . '))';
                }
            }

            // If it's a search for new posts
			if ($action == 'show_new')
			{
                if ($pun_user['is_guest'])
					message($lang_common['No permission']);

				$result = $db->query('SELECT t.id FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1)'.$where_forum_id.' AND '.$where_culture.'t.last_post>'.$pun_user['last_visit'].' AND t.moved_to IS NULL') or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
				$num_hits = $db->num_rows($result);

				if (!$num_hits)
					message($lang_search['No new posts']);
			}
			// If it's a search for todays posts
			else if ($action == 'show_24h')
			{
				$result = $db->query('SELECT t.id FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1)'.$where_forum_id.' AND '.$where_culture.'t.last_post>'.(time() - 86400).' AND t.moved_to IS NULL') or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
				$num_hits = $db->num_rows($result);

				if (!$num_hits)
					message($lang_search['No recent posts']);
			}
			// If it's a search for posts by a specific user ID
			else if ($action == 'show_user')
			{
				$result = $db->query('SELECT t.id FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'posts AS p ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1 OR fp.forum_id=1)'.$where_forum_id.' AND p.poster_id='.$user_id.' GROUP BY t.id') or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
				$num_hits = $db->num_rows($result);

				if (!$num_hits)
					message($lang_search['No user posts']);
			}
			// If it's a search for topics created by a specific user ID
			else if ($action == 'show_user_topics')
			{
				$result = $db->query('SELECT t.id FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'posts AS p ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1 OR fp.forum_id=1)'.$where_forum_id.' AND t.posted=p.posted AND p.poster_id='.$user_id.' GROUP BY t.id') or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
				$num_hits = $db->num_rows($result);

				if (!$num_hits)
					message($lang_search['No user posts']);
			}
			// If it's a search for subscribed topics
			else if ($action == 'show_subscriptions')
			{
				if ($pun_user['is_guest'])
					message($lang_common['Bad request']);

				$result = $db->query('SELECT t.id FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'subscriptions AS s ON (t.id=s.topic_id AND s.user_id='.$pun_user['id'].') INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1 OR fp.forum_id=1)'.$where_forum_id) or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
				$num_hits = $db->num_rows($result);

				if (!$num_hits)
					message($lang_search['No subscriptions']);
			}
			// If it's a search for unanswered posts
			else if ($action == 'show_unanswered')
			{
				$result = $db->query('SELECT t.id FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1)'.$where_forum_id.' AND t.num_replies=0 AND t.moved_to IS NULL') or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
				$num_hits = $db->num_rows($result);

				if (!$num_hits)
					message($lang_search['No unanswered']);
			}
            // If it's a search for all news
            else
            {
				$result = $db->query('SELECT t.id FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.id IN ('.ALL_NEWS_FORUMS.') AND '.$where_culture.'t.moved_to IS NULL') or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
				$num_hits = $db->num_rows($result);
            
                if (!$num_hits)
                    message($lang_search['No hits']);
            }

			// We want to sort things after last post
			$sort_by = 4;

			$search_ids = array();
			while ($row = $db->fetch_row($result))
				$search_ids[] = $row[0];

			$db->free_result($result);

			$show_as = 'topics';
		}
		else
			message($lang_common['Bad request']);

		// Prune "old" search results
        $db->query('DELETE FROM '.$db->prefix.'search_cache WHERE age(now(), created_at) > INTERVAL \'1 hour\'') or error('Unable to delete search results', __FILE__, __LINE__, $db->error());

		// Final search results
		$search_results = implode(',', $search_ids);

		// Fill an array with our results and search properties
		$temp['search_results'] = $search_results;
		$temp['num_hits'] = $num_hits;
		$temp['sort_by'] = $sort_by;
		$temp['sort_dir'] = $sort_dir;
		$temp['show_as'] = $show_as;
		$temp = serialize($temp);
		$search_id = mt_rand(1, 2147483647);

		$ident = ($pun_user['is_guest']) ? get_remote_address() : $pun_user['username'];

		$db->query('INSERT INTO '.$db->prefix.'search_cache (id, ident, search_data) VALUES('.$search_id.', \''.$db->escape($ident).'\', \''.$db->escape($temp).'\')') or error('Unable to insert search results', __FILE__, __LINE__, $db->error());

		if ($action != 'show_new' && $action != 'show_24h')
		{
			$db->end_transaction();
			$db->close();

			// Redirect the user to the cached result page
            $search_location = 'Location: search.php?search_id='.$search_id;
            if ($action != null)
            {
                $search_location .= '&title='.$action;
				if ($action == 'show_user' || $action == 'show_user_topics')
				{
					$search_location .= '&user_id='.$user_id;
				}
            }
			header($search_location);
			exit;
		}
	}


	// Fetch results to display
	if ($search_results != '')
	{
                $group_by_sql = '';
		switch ($sort_by)
		{
			case 1:
				$sort_by_sql = ($show_as == 'topics') ? 't.poster' : 'p.poster';
				break;

			case 2:
				$sort_by_sql = 't.subject';
				break;

			case 3:
				$sort_by_sql = 't.forum_id';
				break;

			case 4:
				$sort_by_sql = 't.last_post';
				break;

			default:
                        {
				$sort_by_sql = ($show_as == 'topics') ? 't.posted' : 'p.posted';

                                if ($show_as == 'topics')
					$group_by_sql = ', t.posted';

				break;
                        }
		}

		if ($show_as == 'posts')
		{
			$substr_sql = ($db_type != 'sqlite') ? 'SUBSTRING' : 'SUBSTR';
			$sql = 'SELECT p.id AS pid, p.poster AS pposter, p.posted AS pposted, p.poster_id, p.poster_ip, '.$substr_sql.'(p.message, 1, 1000) AS message, t.id AS tid, t.poster, t.subject, t.question, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.forum_id FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id WHERE p.id IN('.$search_results.') ORDER BY '.$sort_by_sql;
		}
		else
			$sql = 'SELECT t.id AS tid, t.poster, t.subject, t.question, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.forum_id FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id WHERE t.id IN('.$search_results.') GROUP BY t.id, t.poster, t.subject, t.question, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.forum_id'.$group_by_sql.' ORDER BY '.$sort_by_sql;

		// Determine the topic or post offset (based on $_GET['p'])
		$per_page = ($show_as == 'posts') ? $pun_user['disp_posts'] : $pun_user['disp_topics'];
		$num_pages = ceil($num_hits / $per_page);

		$p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : $_GET['p'];
		$start_from = $per_page * ($p - 1);

		// Generate paging links
		$paging_links = $lang_common['Pages'].': '.paginate($num_pages, $p, 'search.php?search_id='.$search_id);


		$sql .= ' '.$sort_dir.' LIMIT '.$start_from.', '.$per_page;

		$result = $db->query($sql) or error('Unable to fetch search results', __FILE__, __LINE__, $db->error());

		$search_set = array();
		while ($row = $db->fetch_assoc($result))
			$search_set[] = $row;

		$db->free_result($result);

		// Get username when we show posts from one user
		if ($search_action == 'show_user' || $search_action == 'show_user_topics')
		{
                        $user_id = $_GET['user_id'];
                        if (is_numeric($user_id))
                        {
			        $result_users = $db->query('SELECT username FROM '.$db->prefix.'users WHERE id='.$user_id) or error('Unable to fetch users', __FILE__, __LINE__, $db->error());
			        if (!$db->num_rows($result_users))
				        message($lang_common['Bad request']);
			        list($username) = $db->fetch_row($result_users);
			}
                        else
                        {
                                message($lang_common['Bad request']);
                        }
                        $search_title .= $username;
			$context_title .= '<a href="/users/'.$user_id.'">'.$username.'</a>';
		}
        
        $result_stats = ($start_from + 1).' - '.min($start_from + $per_page, $num_hits).' / '.$num_hits;
        $search_title .= ' : ' . $result_stats;
        if ($show_as == 'posts')
        {
            $context_title .= ' : ' . $result_stats;
        }


		$page_title = pun_htmlspecialchars($search_title.' / '.$pun_config['o_board_title']);
		$footer_style = 'search';
		require PUN_ROOT.'header.php';


?>
<div class="linkst">
	<div class="inbox">
		<p class="pagelink conl"><?php echo $paging_links ?></p>
        <?php
        echo "\t\t".'<ul><li><a href="'.get_home_url().'">'.$lang_common['Index'].'</a>&nbsp;</li><li>&raquo;&nbsp;'.$context_title.'</li></ul>';
        ?>
		<div class="clearer"></div>
	</div>
</div>

<?php

		//Set background switching on for show as posts
		$bg_switch = true;

		if ($show_as == 'topics')
		{

?>
<div id="vf" class="blocktable">
	<h2><span><?php echo $search_title; ?></span></h2>
	<div class="box">
		<div class="inbox">
			<table>
			<thead>
				<tr>
					<th class="tcl" scope="col"><?php echo $lang_common['Topic']; ?></th>
					<th class="tc2" scope="col"><?php echo $lang_common['Forum'] ?></th>
					<th class="tc3 hide4smartphone" scope="col"><?php echo $lang_common['Replies'] ?></th>
					<th class="tcr" scope="col"><?php echo $lang_common['Last post'] ?></th>
				</tr>
			</thead>
			<tbody>
<?php

		}

		// Fetch the list of forums
		$result = $db->query('SELECT id, forum_name FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());

		$forum_list = array();
		while ($forum_list[] = $db->fetch_row($result))
			;
        
        $pub_forums = explode(', ', PUB_FORUMS);

		// Finally, lets loop through the results and output them
		for ($i = 0; $i < count($search_set); ++$i)
		{
            // Is this a pub forum ?
            if (in_array($search_set[$i]['forum_id'], $pub_forums))
            {
                $rel = ' rel="nofollow"';
            }
            else
            {
                $rel = '';
            }
            
            @reset($forum_list);
			while (list(, $temp) = @each($forum_list))
			{
				if ($temp[0] == $search_set[$i]['forum_id'])
                {
                    $result_fid = $temp[0];
					$forum = '<a href="viewforum.php?id='.$result_fid.'"'.$rel.'>'.pun_htmlspecialchars($temp[1]).'</a>';
                }
			}

			if ($pun_config['o_censoring'] == '1')
				$search_set[$i]['subject'] = censor_words($search_set[$i]['subject']);


			if ($show_as == 'posts')
			{
				$icon = '<div class="icon"><div class="nosize">'.$lang_common['Normal icon'].'</div></div>'."\n";

                                if ($search_set[$i]['question'] == "" || $search_set[$i]['question'] == 0)
					$subject = '<a href="viewtopic.php?id='.$search_set[$i]['tid'].'"'.$rel.'>'.pun_htmlspecialchars($search_set[$i]['subject']).'</a>';
				else
					$subject = $lang_polls['Poll'] . ': <a href="viewtopic.php?id='.$search_set[$i]['tid'].'"'.$rel.'>'.pun_htmlspecialchars($search_set[$i]['subject']).'</a>';

				if (!$pun_user['is_guest'] && $search_set[$i]['last_post'] > $pun_user['last_visit'])
					$icon = '<div class="icon inew"><div class="nosize">'.$lang_common['New icon'].'</div></div>'."\n";


				if ($pun_config['o_censoring'] == '1')
					$search_set[$i]['message'] = censor_words($search_set[$i]['message']);

				$message = str_replace("\n", '<br />', pun_htmlspecialchars($search_set[$i]['message']));
				$pposter = pun_htmlspecialchars($search_set[$i]['pposter']);

				if ($search_set[$i]['poster_id'] > 1)
					$pposter = '<strong><a href="/users/'.$search_set[$i]['poster_id'].'">'.$pposter.'</a></strong>';

				if (pun_strlen($message) >= 1000)
					$message .= ' &hellip;';

				$vtpost1 = ($i == 0) ? ' vtp1' : '';

				// Switch the background color for every message.
				$bg_switch = ($bg_switch) ? $bg_switch = false : $bg_switch = true;
				$vtbg = ($bg_switch) ? ' rowodd' : ' roweven';


?>
<div class="blockpost searchposts<?php echo $vtbg ?>">
	<h2><?php echo $forum ?>&nbsp;&raquo;&nbsp;<?php echo $subject ?>&nbsp;&raquo;&nbsp;<a href="viewtopic.php?pid=<?php echo $search_set[$i]['pid'].'#p'.$search_set[$i]['pid'] ?>"><?php echo format_time($search_set[$i]['pposted']) ?></a></h2>
	<div class="box">
		<div class="inbox">
			<div class="postleft">
				<dl>
					<dt><?php echo $pposter ?></dt>
					<dd><?php echo $lang_common['Replies'].': '.$search_set[$i]['num_replies'] ?></dd>
					<dd><?php echo $icon; ?><a href="viewtopic.php?pid=<?php echo $search_set[$i]['pid'].'#p'.$search_set[$i]['pid'] ?>" rel="nofollow"><?php echo $lang_search['Go to post'] ?></a></dd>
<?php
                if ($is_admmod)
                {
?>
					<dd>IP: <a href="moderate.php?get_host=<?php echo $search_set[$i]['pid'] ?>"><?php echo $search_set[$i]['poster_ip'] ?></a></dd>
<?php
                }
?>
				</dl>
			</div>
			<div class="postright">
				<div class="postmsg">
					<p><?php echo $message ?></p>
				</div>
			</div>
			<div class="clearer"></div>
		</div>
	</div>
</div>
<?php
			}
			else
			{
                // Does this topic have new posts ?
                $has_new_post = !$pun_user['is_guest'] && topic_is_new($search_set[$i]['tid'], $search_set[$i]['forum_id'],  $search_set[$i]['last_post']);
                
				$icon = '<div class="icon"><div class="nosize">'.$lang_common['Normal icon'].'</div></div>'."\n";
				$icon_text = $lang_common['Normal icon'];
				$item_status = '';
				$icon_type = 'icon';

				if ($search_set[$i]['question'] == "" || $search_set[$i]['question'] == 0)
                {
					$subject = '<a href="viewtopic.php?id='.$search_set[$i]['tid'].'"'.$rel.'>'.pun_htmlspecialchars($search_set[$i]['subject']).'</a>';
                    $by_user = ' <span class="byuser">'.$lang_common['by'].'&nbsp;'.pun_htmlspecialchars($search_set[$i]['poster']).'</span>';
                }
				else
                {
					$subject = $lang_polls['Poll'] . ': <a href="viewtopic.php?id='.$search_set[$i]['tid'].'"'.$rel.'>'.pun_htmlspecialchars($search_set[$i]['subject']).'</a>';
                    $by_user = ' <span class="byuser">'.$lang_common['by'].'&nbsp;'.pun_htmlspecialchars($search_set[$i]['poster']).'</span> [ '.pun_htmlspecialchars($search_set[$i]['question']).' ]';
                }
				if ($search_set[$i]['closed'] != '0')
				{
					$icon_text = $lang_common['Closed icon'];
					$item_status = 'iclosed';
				}

				if ($has_new_post)
				{
					$icon_text .= ' '.$lang_common['New icon'];
					$item_status .= ' inew';
					$icon_type = 'icon inew';
					$subject = '<strong>'.$subject.'</strong>';
					$subject_new_posts = '<span class="newtext">[&nbsp;<a href="viewtopic.php?id='.$search_set[$i]['tid'].'&amp;action=new" title="'.$lang_common['New posts info'].'" rel="nofollow">'.$lang_common['New posts'].'</a>&nbsp;]</span>';
				}
				else
                {
					$subject_new_posts = null;
                }

				if ($is_admmod && $result_fid == COMMENTS_FORUM)
                {
                    $subject .= '&nbsp;[<a href="viewtopic.php?id='.$search_set[$i]['tid'].'&amp;forum">forum</a>]';
                }
                $subject .= $by_user;
                $num_pages_topic = ceil(($search_set[$i]['num_replies'] + 1) / $pun_user['disp_posts']);

				if ($num_pages_topic > 1)
					$subject_multipage = '[&nbsp;'.paginate($num_pages_topic, -1, 'viewtopic.php?id='.$search_set[$i]['tid'], $rel).'&nbsp;]';
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
					<td class="tc2"><?php echo $forum ?></td>
					<td class="tc3 hide4smartphone"><?php echo $search_set[$i]['num_replies'] ?></td>
					<?php
					if ($search_set[$i]['question'] == "" || $search_set[$i]['question'] == 0)
					{
						?><td class="tcr"><?php echo '<a href="viewtopic.php?pid='.$search_set[$i]['last_post_id'].'#p'.$search_set[$i]['last_post_id'].'" rel="nofollow">'.format_time($search_set[$i]['last_post']).'</a> '.$lang_common['by'].'&nbsp;'.pun_htmlspecialchars($search_set[$i]['last_poster']) ?></td><?php
					}
					else
					{
						?><td class="tcr"><?php echo '<a href="viewtopic.php?pid='.$search_set[$i]['last_post_id'].'#p'.$search_set[$i]['last_post_id'].'" rel="nofollow">'.format_time($search_set[$i]['last_post']).'</a> '.$lang_common['by'].'&nbsp;'.pun_htmlspecialchars($search_set[$i]['last_poster']) ?></td><?php
					} ?>
				</tr>
			<?php
			}
		}

		if ($show_as == 'topics')
			echo "\t\t\t".'</tbody>'."\n\t\t\t".'</table>'."\n\t\t".'</div>'."\n\t".'</div>'."\n".'</div>'."\n\n";

?>
<div class="<?php echo ($show_as == 'topics') ? 'linksb' : 'postlinksb'; ?>">
	<div class="inbox">
		<p class="pagelink conl"><?php echo $paging_links ?></p>
        <?php
        echo "\t\t".'<ul><li><a href="'.get_home_url().'">'.$lang_common['Index'].'</a>&nbsp;</li><li>&raquo;&nbsp;'.$context_title.'</li></ul>';
        ?>
		<div class="clearer"></div>
	</div>
</div>
<?php

		require PUN_ROOT.'footer.php';
	}
	else
		message($lang_search['No hits']);

}
$page_title = pun_htmlspecialchars($lang_search['Search'].' / '.$pun_config['o_board_title']);
$focus_element = array('search', 'keywords');
$footer_style = 'search_form';
if (isset($_GET['fid']))
{
    $forum_id = $_GET['fid'];
}
require PUN_ROOT.'header.php';

$form_action = 'search.php';
if (isset($_GET['lang']))
{
    $form_action .='?lang='.$_GET['lang'];
}
?>
<div id="searchform" class="blockform">
	<h2><span><?php echo $lang_search['Search'] ?></span></h2>
	<div class="box">
<!-- embedded google search -->
<script type="text/javascript">
(function(C2C, _q) { _q.push(function() {
  C2C.GoogleSearch = {

    base_url: 'https://www.googleapis.com/customsearch/v1?key=AIzaSyDXFlFziDDG2ThH47z1V3-KmAS6_vA5GUg&cx=013271627684039046788:rqqb4ydcfim&callback=C2C.GoogleSearch.handleResponse',
    alternate_url: 'http://www.google.com/cse?cx=013271627684039046788:rqqb4ydcfim',

    displayPager: function(response) {
      var pagesP = $('<p class="pagelink conl"/>');

      // previous page
      if (response.queries.previousPage) {
        pagesP.append('<a href="#" onclick="C2C.GoogleSearch.search(); return false;">&lt;&lt;</a>\u00a0\u00a0' +
                      '<a href="#" onclick="C2C.GoogleSearch.search(\'&start='+response.queries.previousPage[0].startIndex+'\'); return false;">&lt</a>');
      }

      // current results
      if (response.queries.previousPage || response.queries.nextPage) {
        var start = response.queries.request[0].startIndex;
        var end = start + response.queries.request[0].count;
        pagesP.append('<span>\u00a0\u00a0' + start + '\u00a0-\u00a0' + end + '\u00a0\u00a0</span>');
      }

      // next page
      if (response.queries.nextPage) {
        pagesP.append('<a href="#" onclick="C2C.GoogleSearch.search(\'&start=' + response.queries.nextPage[0].startIndex +
                      '\'); return false;">&gt;</a>');
      }

      $('#google_search_results').append(pagesP);
    },

    handleResponse: function(response) {
      if (response.error) {
        // redirect to the google cse page
        var url = this.alternate_url + '&q=' + $('#google_search_input').val();
        window.location = url;
        return;
      }

      if (response.items && response.items.length > 0) {
        var results = response.items;

         var tbody = $('<tbody/>');
         for (var i = 0, len = results.length; i < len; i++) {
           var title_str = results[i].title.split(' ::')[0];
           tbody
             .append($('<tr/>')
               .append('<td style="background-color:#e2e2e2"><a href="' + results[i].link + '">' + title_str + '</a></td>' +
                       '<td>' + results[i].htmlSnippet + '</td>'));
         }

         $('#google_search_results')
           .html($('<table style="border-style:solid; border-width:1px; border-color:#ff9933;"/>')
             .append(tbody));

         C2C.GoogleSearch.displayPager(response);
      } else {
        $('#google_search_results').text('<?php echo __('No result') ?>');
      }
    },

    search: function(params) {
      // load script asynchronously
      // once loaded, it will call handleResponse()
      var url = this.base_url + '&q=' + $('#google_search_input').val();
      if (params) url += params;

      // note: maybe use $.getJson
      var a = document.createElement('script');
      var h = document.getElementsByTagName('head')[0];
      a.async = 1;
      a.src = url;
      h.appendChild(a);
    }
  };
}); })(window.C2C = window.C2C || {}, window.C2C._q = window.C2C._q || [])
</script>
<!-- end embedded google search script -->
        <form id="gsearch" method="get" action="http://www.google.com/search" onsubmit="C2C.GoogleSearch.search(); return false;">
			<div class="inform">
                <fieldset>
                    <legend><?php echo $lang_search['Google Search'] ?></legend>
					<div class="infldset">
                        <input type="text" id="google_search_input" name="q" value="" size="40"
                               style="background: url(http://www.google.com/coop/intl/<?php echo $lang_common['meta_language']?>/images/google_custom_search_watermark.gif) no-repeat scroll left center #fff"
                               onblur="if (this.value == '') this.style.background = 'url(http://www.google.com/coop/intl/<?php echo $lang_common['meta_language']?>/images/google_custom_search_watermark.gif) no-repeat scroll left center #fff';"
                               onfocus="this.style.background = 'none repeat scroll 0 0 #fff'"
                                /><div id="google_search_submit" onclick="this.up().submit();"></div>
                        <label class="conl"><input type="hidden" name="sitesearch" value="camptocamp.org/forums" /></label>
                    </div>
                    <div id="google_search_results" class="inbox"></div>
                </fieldset>
            </div>
        </form>
		<form id="search" method="get" action="<?php echo $form_action ?>">
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_search['Search criteria legend'] ?></legend>
					<div class="infldset">
						<input type="hidden" name="action" value="search" />
						<label class="conl"><?php echo $lang_search['Keyword search'] ?><br /><input type="text" name="keywords" size="40" maxlength="100" /><br /></label>
						<label class="conl"><?php echo $lang_search['Author search'] ?><br /><input id="author" type="text" name="author" size="25" maxlength="25" /><br /></label>
<?php
if ($is_admmod)
{
?>
						<label class="conl">Author ID<br /><input id="author_id" type="text" name="author_id" size="8" maxlength="32" /><br /></label>
						<label class="conl">Author option<br />
    						<select name="author_type">
    							<option value="0"></option>
    							<option value="1"><?php echo $lang_common['Member'] . ' (' . $lang_common['all'] . ')' ?></option>
    							<option value="2"><?php echo $lang_common['Member'] . ' avec cet ID uniquement' ?></option>
    							<option value="3"><?php echo $lang_common['Member'] . ' avec un ID différent' ?></option>
    						</select>
                            <br />
                        </label>
						<label class="conl"><?php echo $lang_common['Guest'] ?><br /><input id="guest" type="checkbox" name="guest" value="1" /><br /></label>
						<label class="conl">IP<br /><input id="ip" type="text" name="ip" size="15" maxlength="32" /><br /></label>
<?php
}
?>
						<p class="clearb"><?php echo $lang_search['Search info'] ?></p>
					</div>
				</fieldset>
			</div>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_search['Search in legend'] ?></legend>
					<div class="infldset">
						<label class="conl"><?php echo $lang_search['Forum search'] ?>
						<br /><select id="forum" name="forum[]" multiple="multiple" size="10">
<?php

$select_forum = isset($_GET['fid']) ? intval($_GET['fid']) : (-1);

if ($pun_config['o_search_all_forums'] == '1' || $pun_user['g_id'] < PUN_GUEST)
	echo "\t\t\t\t\t\t\t".'<option value="-1">'.$lang_search['All forums'].'</option>'."\n";

$result = $db->query('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.redirect_url, f.parent_forum_id FROM '.$db->prefix.'categories AS c INNER JOIN '.$db->prefix.'forums AS f ON c.id=f.cat_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.redirect_url IS NULL ORDER BY c.disp_position, c.id, f.disp_position', true) or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

$cur_category = 0;
while ($cur_forum = $db->fetch_assoc($result))
{
	if ($cur_forum['cid'] != $cur_category)	// A new category since last iteration?
	{
		if ($cur_category)
			echo "\t\t\t\t\t\t\t".'</optgroup>'."\n";

		echo "\t\t\t\t\t\t\t".'<optgroup label="'.pun_htmlspecialchars($cur_forum['cat_name']).'">'."\n";
		$cur_category = $cur_forum['cid'];
	}
    $selected_forum = ($select_forum == $cur_forum['fid']) ? ' selected="selected"' : '';
    if ($cur_forum['parent_forum_id'] == 0)
    {
        echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_forum['fid'].'"'.$selected_forum.'>'.pun_htmlspecialchars($cur_forum['forum_name']).'</option>'."\n";
    }
    else
    {
        echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_forum['fid'].'"'.$selected_forum.'>&nbsp;&nbsp;&nbsp;'.pun_htmlspecialchars($cur_forum['forum_name']).'</option>'."\n";
    }
}

?>
							</optgroup>
						</select>
						<br /></label>
						<label class="conl"><?php echo $lang_search['Search in'] ?>
						<br /><select id="search_in" name="search_in">
							<option value="topic"><?php echo $lang_search['Topic only'] ?></option>
							<option value="all"><?php echo $lang_search['Message and subject'] ?></option>
							<option value="message"><?php echo $lang_search['Message only'] ?></option>
						</select>
						<br /></label>
						<p class="clearb"><?php echo $lang_search['Search in info'] ?></p>
					</div>
				</fieldset>
			</div>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_search['Search results legend'] ?></legend>
					<div class="infldset">
						<label class="conl"><?php echo $lang_search['Sort by'] ?>
						<br /><select name="sort_by">
							<option value="0"><?php echo $lang_search['Sort by post time'] ?></option>
							<option value="1"><?php echo $lang_search['Sort by author'] ?></option>
							<option value="2"><?php echo $lang_search['Sort by subject'] ?></option>
							<option value="3"><?php echo $lang_search['Sort by forum'] ?></option>
						</select>
						<br /></label>
						<label class="conl"><?php echo $lang_search['Sort order'] ?>
						<br /><select name="sort_dir">
							<option value="DESC"><?php echo $lang_search['Descending'] ?></option>
							<option value="ASC"><?php echo $lang_search['Ascending'] ?></option>
						</select>
						<br /></label>
						<label class="conl"><?php echo $lang_search['Show as'] ?>
						<br /><select name="show_as">
							<option value="topics"><?php echo $lang_search['Show as topics'] ?></option>
							<option value="posts"><?php echo $lang_search['Show as posts'] ?></option>
						</select>
						<br /></label>
						<p class="clearb"><?php echo $lang_search['Search results info'] ?></p>
					</div>
				</fieldset>
			</div>
			<p><input type="submit" name="search" value="<?php echo $lang_common['Submit'] ?>" accesskey="s" /></p>
		</form>
	</div>
</div>
<?php

require PUN_ROOT.'footer.php';
