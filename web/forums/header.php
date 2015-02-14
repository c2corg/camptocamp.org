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


// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;

// Send no-cache headers

header('Expires:');	// When yours truly first set eyes on this world! :)
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control:');
header('Pragma:');		// For HTTP/1.0 compability


// Load the template
if (defined('PUN_ADMIN_CONSOLE'))
	$tpl_main = file_get_contents(PUN_ROOT.'include/template/main.tpl');
else if (defined('PUN_HELP'))
	$tpl_main = file_get_contents(PUN_ROOT.'include/template/help.tpl');
else
	$tpl_main = file_get_contents(PUN_ROOT.'include/template/main.tpl');


// START SUBST - <pun_content_direction>
$tpl_main = str_replace('<pun_content_direction>', $lang_common['lang_direction'], $tpl_main);
// END SUBST - <pun_content_direction>

// START SUBST - <pun_meta_language>
$tpl_main = str_replace('<pun_meta_language>', $lang_common['meta_language'], $tpl_main);
// END SUBST - <pun_meta_language>

// START SUBST - <pun_char_encoding>
$tpl_main = str_replace('<pun_char_encoding>', $lang_common['lang_encoding'], $tpl_main);
// END SUBST - <pun_char_encoding>


// START SUBST - <pun_head>
ob_start();

// symfony integration for javascripts and stylesheets
if (empty($sf_response))
{
    $sf_response = sfContext::getInstance()->getResponse();
}
$sf_response->addJavascript('/static/js/fold.js');

// Is this a page that we want search index spiders to index?
if (defined('PUN_ALLOW_INDEX'))
{
    $robot_index = 'index';
}
else
{
    $robot_index = 'noindex';
}
if (defined('PUN_NO_FOLLOW'))
{
    $robot_follow = 'nofollow';
}
else
{
    $robot_follow = 'follow';
}
echo '<meta name="robots" content="' . $robot_index . ', ' . $robot_follow . '" />'."\n";

if (!isset($page_description))
{
    $page_description = $lang_common['meta_description'];
}

echo '<link rel="canonical" href="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" />';
?>
<meta name="title" content="<?php echo $page_title ?>" />
<meta name="description" content="<?php echo $page_description ?>" />
<meta name="keywords" content="<?php echo $lang_common['meta_keywords'] ?>" />
<title><?php echo $page_title ?></title>
<?php

$sf_response->addStylesheet('/static/css/forums.css');

if (defined('PUN_ADMIN_CONSOLE'))
        $sf_response->addStylesheet('/forums/style/imports/base_admin.css');

if (defined('FORUM_FEED') && FORUM_FEED != 'all') {
        echo '<link rel="alternate" type="application/rss+xml" href="extern.php?type=rss&amp;action=active&amp;fid='.FORUM_FEED.'" />'."\n";
}

if (defined('FORUM_FEED') && FORUM_FEED == 'all') {
        echo '<link rel="alternate" type="application/rss+xml" href="extern.php?type=rss&amp;action=active" />'."\n";
}

if (isset($required_fields))
{
	// Output JavaScript to validate form (make sure required fields are filled out)

?>
<script type="text/javascript">
<!--
function process_form(the_form)
{
	var element_names = new Object()
<?php

	// Output a JavaScript array with localised field names
	while (list($elem_orig, $elem_trans) = @each($required_fields))
		echo "\t".'element_names["'.$elem_orig.'"] = "'.addslashes(str_replace('&nbsp;', ' ', $elem_trans)).'"'."\n";

?>

	if (document.all || document.getElementById)
	{
		for (i = 0; i < the_form.length; ++i)
		{
			var elem = the_form.elements[i]
			if (elem.name && elem.name.substring(0, 4) == "req_")
			{
				if (elem.type && (elem.type=="text" || elem.type=="textarea" || elem.type=="password" || elem.type=="file") && elem.value=='')
				{
					alert("\"" + element_names[elem.name] + "\" <?php echo $lang_common['required field'] ?>")
					elem.focus()
					return false
				}
			}
		}
	}

	return true
}
// -->
</script>
<?php

}

// in orde rto optimize cache for forums js, we always include following js
// so that every forum page has the same js file
// moreover the file is rather small (<1k once compressed)
//if (in_array(basename($_SERVER['PHP_SELF']), array('index.php', 'search.php')))
//{
    $sf_response->addJavascript('/static/js/dyncat.js');
//}

$tpl_temp = trim(ob_get_contents());
$tpl_main = str_replace('<pun_head>', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <pun_head>


// START SUBST - <body>
if (isset($focus_element))
{
	$tpl_main = str_replace('<body onload="', '<body onload="document.getElementById(\''.$focus_element[0].'\').'.$focus_element[1].'.focus();', $tpl_main);
	$tpl_main = str_replace('<body>', '<body onload="document.getElementById(\''.$focus_element[0].'\').'.$focus_element[1].'.focus()">', $tpl_main);
}
// END SUBST - <body>


// START SUBST - <pun_page>
$tpl_main = str_replace('<pun_page>', htmlspecialchars(basename($_SERVER['PHP_SELF'], '.php')), $tpl_main);
// END SUBST - <pun_page>

// START SUBST - <pun_page_class>
$page_class = '';
if (isset($forum_id) && in_array($forum_id, array(3, 33, 34, 35, 36, 37, 38, 40, 53, 73, 74, 84, 95)))
{
    $page_class = ' c2c-asso';
}
$tpl_main = str_replace('<pun_page_class>', $page_class, $tpl_main);
// END SUBST - <pun_page_class>


// START SUBST - <pun_title>
$tpl_main = str_replace('<pun_title>', '<h1><span>'.pun_htmlspecialchars($pun_config['o_board_title']).'</span></h1>', $tpl_main);
// END SUBST - <pun_title>


// START SUBST - <pun_desc>
$tpl_main = str_replace('<pun_desc>', '<p><span>'.$pun_config['o_board_desc'].'</span></p>', $tpl_main);
// END SUBST - <pun_desc>


// START SUBST - <pun_navlinks>
$tpl_main = str_replace('<pun_navlinks>','<div id="brdmenu" class="inbox">'."\n\t\t\t". generate_navlinks()."\n\t\t".'</div>', $tpl_main);
// END SUBST - <pun_navlinks>


// START SUBST - <pun_status>
// If no header style has been specified, we use the default
$footer_style = isset($footer_style) ? $footer_style : NULL;
$is_admmod = isset($is_admmod) ? $is_admmod : false;
$is_admmod_2 = ($pun_user['g_id'] == PUN_ADMIN || $pun_user['g_id'] == PUN_MOD) ? true : false;
$is_assoc = (in_array($pun_user['g_id'], explode(', ', PUN_ASSOCIATION))) ? true : false;
$is_v6 = (in_array($pun_user['g_id'], explode(', ', PUN_V6))) ? true : false;
if (!isset($forum_id))
{
    switch($pun_user['language'])
    {
        case 'French':
            $forum_id = 24;
            break;
        case 'Italian':
            $forum_id = 41;
            break;
        case 'German':
            $forum_id = 61;
            break;
        case 'English':
            $forum_id = 58;
            break;
        case 'Spanish':
            $forum_id = 64;
            break;
        case 'Catalan':
            $forum_id = 67;
            break;
        case 'Euskara':
            $forum_id = 80;
            break;
        default:
            $forum_id = 24;
            break;
    }
}

if ($is_admmod_2)
{
	$forum_modo = '';
	if ($lang == 'fr')
	{
		$forum_modo = MODO_FR_FORUM;
	}
	elseif ($lang == 'it')
	{
		$forum_modo = MODO_IT_FORUM;
	}
	if (!empty($forum_modo))
	{
		$forum_modo = ' - <a href="viewforum.php?id='.$forum_modo.'">Forum modos</a>';
	}
}

$tpl_temp = '<div id="brdwelcome" class="block">'."\n\t".'<div class="box">'."\n\t\t".'<div class="inbox">'."\n\t\t\t".'<div class="conl">';

if ($footer_style != NULL && $footer_style != 'index')
{
	// Display the "Jump to" drop list
	if ($pun_config['o_quickjump'] == '1')
	{
		// Load cached quickjump
        ob_start();
		@include PUN_ROOT.'cache/cache_quickjump_'.$pun_user['g_id'].'.php';
		if (!defined('PUN_QJ_LOADED'))
		{
			require_once PUN_ROOT.'include/cache.php';
			generate_quickjump_cache($pun_user['g_id']);
            require PUN_ROOT.'cache/cache_quickjump_'.$pun_user['g_id'].'.php';
		}
        $tpl_temp .= trim(ob_get_contents());
        ob_end_clean();
	}
}

$tpl_temp .= '<ul>';

if ($footer_style == 'search_form')
{
    $tpl_temp .= "\n\t\t\t\t".'<li><a href="'.get_home_url().'">'.$lang_common['Index'].'</a></li>';
}
else
{
    $select_forum = isset($forum_id) ? '?fid='.$forum_id : '';
    $tpl_temp .= "\n\t\t\t\t".'<li><a href="search.php'.$select_forum.'">'.$lang_common['Search'].'</a></li>';
}

require(PUN_ROOT.'include/pms/header_new_messages.php');

if ($is_admmod_2)
{
    $result_header = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'reports WHERE zapped IS NULL') or error('Unable to fetch reports info', __FILE__, __LINE__, $db->error());

    $nb_reports = $db->result($result_header);
    if ($nb_reports)
    {
        if ($nb_reports == 1)
        {
            $report_text = 'nouveau signalement';
        }
        else
        {
            $report_text = 'nouveaux signalements';
        }
        $tpl_temp .= "\n\t\t\t\t".'<li class="reportlink"><strong><a href="admin_reports.php">' . $nb_reports . ' ' . $report_text . '</a></strong></li>';
    }

    if ($pun_config['o_maintenance'] == '1')
    {
        $tpl_temp .= "\n\t\t\t\t".'<li class="maintenancelink"><strong><a href="admin_options.php#maintenance">Le mode maintenance est activé&nbsp;!</a></strong></li>';
    }
    
    $tpl_temp .= "\n\t\t\t\t".'<li>';
    if ($is_admmod)
    {
        if ($footer_style == 'viewtopic')
        {
            $tpl_temp .= '<a href="moderate.php?fid='.$forum_id.'&amp;move_topics='.$id.'">'.$lang_common['Move topic'].'</a> | ';
        }
        if ($footer_style == 'viewforum' || $footer_style == 'viewtopic')
        {
            $p_temp = isset($p) ? '&amp;p='.$p : '';
            $tpl_temp .= '<a href="moderate.php?fid='.$forum_id.$p_temp.'">'.$lang_common['Moderate forum'].'</a> | ';
        }
    }
    $tpl_temp .= '<a href="admin_users.php">Admin</a>'.$forum_modo.'</li>';
}

$tpl_temp .= "\n\t\t\t".'</ul></div>'."\n\t\t\t".'<ul class="conr">';

$lang = get_lang_code();
if ($lang == 'fr')
{
    $all_lang_text = $lang_common['multilanguage'];
}
else
{
    $all_lang_text = $lang_common['all'];
}

if (!$pun_user['is_guest'])
{
    $tpl_temp .= '<li><a href="search.php?action=show_user&amp;user_id='.$pun_user['id'].'">'.$lang_common['Show your posts'].'</a></li>';
    $tpl_temp .= '<li><a href="search.php?action=show_new&amp;lang='.$lang.'">'.$lang_common['Show new posts'].' ['.$lang.']</a> - <a href="search.php?action=show_new">['.$all_lang_text.']</a></li>';
    $tpl_filters = '';
    if ($lang == 'fr')
    {
        $tpl_filters .= '<a href="search.php?action=show_new&amp;lang='.$lang.'&amp;all">['.$lang.$lang_common['with pub'].']</a>'
                   . ' - <a href="search.php?action=show_new&amp;lang='.$lang.'&amp;light">[light]</a>';
    }
    if ($is_assoc)
    {
        $tpl_filters .= ' - <a href="search.php?action=show_new&amp;assoc">[assoc]</a>';
    }
    if ($is_v6)
    {
        $tpl_filters .= ' - <a href="search.php?action=show_new&amp;v6">[V6]</a>';
    }
    if (!empty($tpl_filters))
    {
    	$tpl_temp .= '<li>' . $tpl_filters . '</li>';
    }
    $tpl_temp .= '<li><a href="#brdfooter">'.$lang_common['Bottom'].'</a></li>';
    if ($footer_style == 'index' || $footer_style == 'search')
    {
        $tpl_temp .= '<li><a href="misc.php?action=markread">'.$lang_common['Mark all as read'].'</a></li>';
    }
    else if ($footer_style == 'viewforum')
    {
        $tpl_temp .= '<li><a href="misc.php?action=markforumread&amp;id='.$id.'">'.$lang_common['Mark forum as read'].'</a></li>';
    }
}
else
{
    $tpl_temp .= '<li><a href="search.php?action=show_24h&amp;lang='.$lang.'">'.$lang_common['Show recent posts'].' ['.$lang.']</a> - <a href="search.php?action=show_24h">['.$all_lang_text.']</a></li>';
    if ($lang == 'fr')
    {
        $tpl_temp .= '<li><a href="search.php?action=show_24h&amp;lang='.$lang.'&amp;all">['.$lang.$lang_common['with pub'].']</a>'
                   . ' - <a href="search.php?action=show_24h&amp;lang='.$lang.'&amp;light">[light]</a></li>';
    }
	$tpl_temp .= '<li><a href="#brdfooter">'.$lang_common['Bottom'].'</a></li>';
}

$tpl_temp .= "\n\t\t\t".'</ul>'."\n\t\t\t".'<div class="clearer"></div>'."\n\t\t".'</div></div></div>';

$tpl_main = str_replace('<pun_status>', $tpl_temp, $tpl_main);
// END SUBST - <pun_status>


// START SUBST - <pun_announcement>
if ($pun_config['o_announcement'] == '1')
{
	ob_start();

?>
<div id="announce" class="block">
	<h2><span><?php echo $lang_common['Announcement'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<div><?php echo $pun_config['o_announcement_message'] ?></div>
		</div>
	</div>
</div>
<?php

	$tpl_temp = trim(ob_get_contents());
	$tpl_main = str_replace('<pun_announcement>', $tpl_temp, $tpl_main);
	ob_end_clean();
}
else
	$tpl_main = str_replace('<pun_announcement>', '', $tpl_main);
// END SUBST - <pun_announcement>


// START SUBST - <pun_main>
ob_start();


define('PUN_HEADER', 1);
