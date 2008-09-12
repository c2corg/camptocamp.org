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

// Load the functions script
require_once PUN_ROOT.'include/functions.php';


// Here you can add additional smilies if you like (please note that you must escape singlequote and backslash)
$smiley_text = array(':)', '=)', ':|', '=|', ':(', '=(', ':D', '=D', ':o', ':O', ';)', ':/', ':P', ':lol:', ':mad:', ':rolleyes:', ':cool:');
$smiley_img = array('smile.png', 'smile.png', 'neutral.png', 'neutral.png', 'sad.png', 'sad.png', 'big_smile.png', 'big_smile.png', 'yikes.png', 'yikes.png', 'wink.png', 'hmm.png', 'tongue.png', 'lol.png', 'mad.png', 'roll.png', 'cool.png');

// Uncomment the next row if you add smilies that contain any of the characters &"'<>
//$smiley_text = array_map('pun_htmlspecialchars', $smiley_text);


//
// Make sure all BBCodes are lower case and do a little cleanup
//
function preparse_bbcode($text, &$errors, $is_signature = false)
{
	// Change all simple BBCodes to lower case
	$a = array('[B]', '[I]', '[U]', '[S]', '[Q]', '[C]', '[/B]', '[/I]', '[/U]', '[/S]', '[/Q]', '[/C]');
	$b = array('[b]', '[i]', '[u]', '[s]', '[q]', '[c]', '[/b]', '[/i]', '[/u]', '[/s]', '[/q]', '[/c]');
	$text = str_replace($a, $b, $text);

	// Do the more complex BBCodes (also strip excessive whitespace and useless quotes)
	$a = array( '#\[url=("|\'|)(.*?)\\1\]\s*#i',
				'#\[url(=\]|\])\s*#i',
				'#\s*\[/url\]#i',
				'#\[email=("|\'|)(.*?)\\1\]\s*#i',
				'#\[email(=\]|\])\s*#i',
				'#\s*\[/email\]#i',
				'#\[img=("|\'|)(.*?)\\1\]\s*#i',
 				'#\[img(=\]|\])\s*#i',
				'#\s*\[/img\]#i',
                '#\[colou?r=("|\'|)(.*?)\\1\](.*?)\[/colou?r\]#is');

	$b = array(	'[url=$2]',
				'[url]',
				'[/url]',
				'[email=$2]',
				'[email]',
				'[/email]',
				'[img=$2]',
				'[img]',
				'[/img]',
				'[color=$2]$3[/color]');

	if (!$is_signature)
	{
		// For non-signatures, we have to do the quote and code tags as well
		$a[] = '#\[quote=(&quot;|"|\'|)(.*?)\\1\]\s*#i';
		$a[] = '#\[quote(=\]|\])\s*#i';
		$a[] = '#\s*\[/quote\]\s*#i';
		$a[] = '#\[code\][\r\n]*(.*?)\s*\[/code\]\s*#is';
		$a[] = '#\[spoiler=("|\'|)(.*?)\\1\\]\s*#i';
		$a[] = '#\[spoiler(=\]|\])\s*#i';
		$a[] = '#\s*\[/spoiler\]\s*#i';
		$a[] = '#\[center\]\s*#i';
		$a[] = '#\s*\[/center\]\s*#i';
		$a[] = '#\[video([^0-9\]]*)([0-9]+)([^0-9\]]+)([0-9]+)([^0-9\]]*)\]\s*#i';
		$a[] = '#\[video\]\s*#i';
		$a[] = '#\s*\[/video\]\s*#i';

		$b[] = '[quote=$1$2$1]';
		$b[] = '[quote]';
		$b[] = '[/quote]'."\n";
		$b[] = '[code]$1[/code]'."\n";
		$b[] = '[spoiler=$2]';
		$b[] = '[spoiler]';
		$b[] = '[/spoiler]'."\n";
		$b[] = '[center]';
		$b[] = '[/center]'."\n";
		$b[] = '[video $2,$4]';
		$b[] = '[video]';
		$b[] = '[/video]'."\n";
	}

	// Run this baby!
	$text = preg_replace($a, $b, $text);

	if (!$is_signature)
	{
		$overflow = check_tag_order($text, $error);

		if ($error)
			// A BBCode error was spotted in check_tag_order()
			$errors[] = $error;
		else if ($overflow)
			// The quote depth level was too high, so we strip out the inner most quote(s)
			$text = substr($text, 0, $overflow[0]).substr($text, $overflow[1], (strlen($text) - $overflow[0]));
	}
	else
	{
		global $lang_prof_reg;

		if (preg_match('#\[quote=(&quot;|"|\'|)(.*)\\1\]|\[quote\]|\[/quote\]|\[code\]|\[/code\]#i', $text))
			message($lang_prof_reg['Signature quote/code']);
	}

	return trim($text);
}


//
// Parse text and make sure that [code] and [quote] syntax is correct
//
function check_tag_order($text, &$error)
{
	global $lang_common;

	// The maximum allowed quote depth
	$max_depth = 3;

	$cur_index = 0;
	$q_depth = 0;

	while (true)
	{
		// Look for regular code and quote tags
		$c_start = strpos($text, '[code]');
		$c_end = strpos($text, '[/code]');
		$q_start = strpos($text, '[quote]');
		$q_end = strpos($text, '[/quote]');

		// Look for [quote=username] style quote tags
		if (preg_match('#\[quote=(&quot;|"|\'|)(.*)\\1\]#sU', $text, $matches))
			$q2_start = strpos($text, $matches[0]);
		else
			$q2_start = 65536;

		// Deal with strpos() returning false when the string is not found
		// (65536 is one byte longer than the maximum post length)
		if ($c_start === false) $c_start = 65536;
		if ($c_end === false) $c_end = 65536;
		if ($q_start === false) $q_start = 65536;
		if ($q_end === false) $q_end = 65536;

		// If none of the strings were found
		if (min($c_start, $c_end, $q_start, $q_end, $q2_start) == 65536)
			break;

		// We are interested in the first quote (regardless of the type of quote)
		$q3_start = ($q_start < $q2_start) ? $q_start : $q2_start;

		// We found a [quote] or a [quote=username]
		if ($q3_start < min($q_end, $c_start, $c_end))
		{
			$step = ($q_start < $q2_start) ? 7 : strlen($matches[0]);

			$cur_index += $q3_start + $step;

			// Did we reach $max_depth?
			if ($q_depth == $max_depth)
				$overflow_begin = $cur_index - $step;

			++$q_depth;
			$text = substr($text, $q3_start + $step);
		}

		// We found a [/quote]
		else if ($q_end < min($q_start, $c_start, $c_end))
		{
			if ($q_depth == 0)
			{
				$error = $lang_common['BBCode error'].' '.$lang_common['BBCode error 1'];
				return;
			}

			$q_depth--;
			$cur_index += $q_end+8;

			// Did we reach $max_depth?
			if ($q_depth == $max_depth)
				$overflow_end = $cur_index;

			$text = substr($text, $q_end+8);
		}

		// We found a [code]
		else if ($c_start < min($c_end, $q_start, $q_end))
		{
			// Make sure there's a [/code] and that any new [code] doesn't occur before the end tag
			$tmp = strpos($text, '[/code]');
			$tmp2 = strpos(substr($text, $c_start+6), '[code]');
			if ($tmp2 !== false)
				$tmp2 += $c_start+6;

			if ($tmp === false || ($tmp2 !== false && $tmp2 < $tmp))
			{
				$error = $lang_common['BBCode error'].' '.$lang_common['BBCode error 2'];
				return;
			}
			else
				$text = substr($text, $tmp+7);

			$cur_index += $tmp+7;
		}

		// We found a [/code] (this shouldn't happen since we handle both start and end tag in the if clause above)
		else if ($c_end < min($c_start, $q_start, $q_end))
		{
			$error = $lang_common['BBCode error'].' '.$lang_common['BBCode error 3'];
			return;
		}
	}

	// If $q_depth <> 0 something is wrong with the quote syntax
	if ($q_depth)
	{
		$error = $lang_common['BBCode error'].' '.$lang_common['BBCode error 4'];
		return;
	}
	else if ($q_depth < 0)
	{
		$error = $lang_common['BBCode error'].' '.$lang_common['BBCode error 5'];
		return;
	}

	// If the quote depth level was higher than $max_depth we return the index for the
	// beginning and end of the part we should strip out
	if (isset($overflow_begin))
		return array($overflow_begin, $overflow_end);
	else
		return null;
}


//
// Split text into chunks ($inside contains all text inside $start and $end, and $outside contains all text outside)
//
function split_text($text, $start, $end)
{
	global $pun_config;

	$tokens = explode($start, $text);

	$outside[] = $tokens[0];

	$num_tokens = count($tokens);
	for ($i = 1; $i < $num_tokens; ++$i)
	{
		$temp = explode($end, $tokens[$i]);
		$inside[] = $temp[0];
		$outside[] = $temp[1];
	}

	if ($pun_config['o_indent_num_spaces'] != 8 && $start == '[code]')
	{
		$spaces = str_repeat(' ', $pun_config['o_indent_num_spaces']);
		$inside = str_replace("\t", $spaces, $inside);
	}

	return array($inside, $outside);
}


//
// Truncate URL if longer than 55 characters (add http:// or ftp:// if missing)
//
function handle_url_tag($url, $link = '')
{
	global $pun_user, $pun_config;

	$full_url = str_replace(array(' ', '\'', '`', '"'), array('%20', '', '', ''), $url);
	if (strpos($url, 'www.') === 0)			// If it starts with www, we add http://
		$full_url = 'http://'.$full_url;
	else if (strpos($url, 'ftp.') === 0)	// Else if it starts with ftp, we add ftp://
		$full_url = 'ftp://'.$full_url;
	else if ((strpos($url, '#') !== 0) || !preg_match('#^([a-z0-9]{3,6})://#', $url, $bah)) 	// Else if it doesn't start with abcdef:// nor #, we add http://
		$full_url = 'http://'.$full_url;

    if ($link == '' || $link == $url)
    {
        // Truncate link text if its an internal forum URL
        $base_url = $pun_config['o_base_url'].'/';
        if ((strlen($full_url) > strlen($base_url)) && (stripos($full_url, $base_url) === 0))
        {
            $link = substr($full_url, strlen($base_url));
        }
        // Truncate URL if longer than 55 characters
        else
        {
            $link = ((strlen($url) > 55) ? substr($url, 0 , 39).' &hellip; '.substr($url, -10) : $url);
        }
    }
    else
    {
        $link = stripslashes($link);
    }

	return '<a href="'.$full_url.'">'.$link.'</a>';
}


//
// Turns an URL from the [img] tag into an <img> tag or a <a href...> tag
//
function handle_img_tag($url, $is_signature = false, $alt=null)
{
	global $lang_common, $pun_config, $pun_user;

    if ($alt == null)
    {
        $alt = $url;
        $title='';
        $image_text = $lang_common['Image link'];
    }
    else
    {
        $title = '" title="'.$alt;
        $image_text = $lang_common['Image link'].'&nbsp;: '.$alt;
    }

	$img_tag = $img_tag = '<a href="'.$url.'">&lt;&nbsp;'.$image_text.'&nbsp;&gt;</a>';

    $alt = '&lt;&nbsp;'.$lang_common['Image link'].'&nbsp;: '.$alt.'&nbsp;&gt;';

    if ($is_signature && $pun_user['show_img_sig'] != '0')
    {
		$img_tag = '<img class="sigimage" src="'.$url.$title.'" alt="'.$alt.'" />';
    }
	else if (!$is_signature && $pun_user['show_img'] != '0')
    {
		$img_tag = '<img class="postimg" src="'.$url.$title.'" alt="'.$alt.'" />';
    }

	return $img_tag;
}


//
// Turns a [img] tag from camptocamp.org image page into an <img> tag or a <a href...> tag
//
function handle_c2c_img_tag($url, $ext, $is_signature = false, $alt=null)
{
	global $lang_common, $pun_config, $pun_user;

//	$base_url_tmp = parse_url($pun_config['o_base_url']);
//	$base_url = $base_url_tmp['sheme'].'://'.$base_url_tmp['host'].'/uploads/images/';
	$base_url = '/uploads/images/';
	$small_img_url = $base_url.$url.'MI.'.$ext;
	$img_url = $base_url.$url.'.'.$ext;
	
	if ($alt == null)
    {
        $alt = $url.'.'.$ext;
        $title='';
        $image_text = $lang_common['Image link'];
    }
    else
    {
        $title = '" title="'.$alt;
        $image_text = $lang_common['Image link'].'&nbsp;: '.$alt;
    }

	$img_tag = $img_tag = '<a href="'.$img_url.'">&lt;&nbsp;'.$image_text.'&nbsp;&gt;</a>';

    $alt = '&lt;&nbsp;'.$lang_common['Image link'].'&nbsp;: '.$alt.'&nbsp;&gt;';

    if ($is_signature && $pun_user['show_img_sig'] != '0')
    {
		$img_tag = '<a href="'.$img_url.'"><img class="sigimage" src="'.$small_img_url.$title.'" alt="'.$alt.'" /></a>';
    }
	else if (!$is_signature && $pun_user['show_img'] != '0')
    {
		$img_tag = '<a href="'.$img_url.'"><img class="postimg" src="'.$small_img_url.$title.'" alt="'.$alt.'" /></a>';
    }

	return $img_tag;
}


//
// Email obfuscation against spam
//
function handle_email_tag($email, $label = NULL)
{
    if (empty($email)) return '';
    if (empty($label)) $label = $email;

    $string = sprintf('<a href="mailto:%s">%s</a>', $email, $label);

    $js = '';
    foreach (str_split($string, 7) as $part)
    {
        $part = addslashes($part);
        $js .= "document.write('$part');";
    }

    return '<script type="text/javascript">' . $js . '</script>';
}


//
// Convert BBCodes to their HTML equivalent
//
function do_bbcode($text)
{
	global $lang_common, $lang_topic, $pun_user, $pun_config;

	if (strpos($text, 'quote') !== false)
	{
		$text = str_replace('[quote]', '</p><blockquote><div class="incqbox"><p>', $text);
		$text = preg_replace('#\[quote=(&quot;|"|\'|)(.*)\\1\]#seU', '"</p><blockquote><div class=\"incqbox\"><h4>".str_replace(array(\'[\', \'\\"\'), array(\'&#91;\', \'"\'), \'$2\')." ".$lang_common[\'wrote\'].":</h4><p>"', $text);
		$text = preg_replace('#\[\/quote\]\s*#', '</p></div></blockquote><p>', $text);
	}

	$pattern = array('#\[b\](.*?)\[/b\]#s',
					 '#\[i\](.*?)\[/i\]#s',
					 '#\[u\](.*?)\[/u\]#s',
                     '#\[s\](.*?)\[/s\]#s',
                     '#\[q\](.*?)\[/q\]#s',
                     '#\[c\](.*?)\[/c\]#s',
					 '#\[url\]([^\[<]*?)\[/url\]#e',
					 '#\[url=([^\[<]*?)\](.*?)\[/url\]#e',
                     '#\[center\](.*?)\[/center\]\s*#s',
					 '#\[email\]([^\[<]*?)\[/email\]#',
					 '#\[email=([^\[<]*?)\](.*?)\[/email\]#',
					 '#\[spoiler(=([^\[]*?)|)\](.*?)\[/spoiler\]\s*#s',
                     '#\[acronym\]([^\[]*?)\[/acronym\]#',
                     '#\[acronym=([^\[]*?)\](.*?)\[/acronym\]#',
					 '#\[colou?r=([a-zA-Z]{3,20}|\#?[0-9a-fA-F]{6})](.*?)\[/colou?r\]#s',
                     '#\[---\]#s');

	$replace = array('<strong>$1</strong>',
					 '<em>$1</em>',
					 '<span class="bbu">$1</span>',
                     '<del>$1</del>',
                     '<q>$1</q>',
                     '<code>$1</code>',
					 'handle_url_tag(\'$1\')',
					 'handle_url_tag(\'$1\', \'$2\')',
                     '</p><div style="text-align: center;"><p>$1</p></div><p>',
					 'handle_email_tag(\'$1\')',
					 'handle_email_tag(\'$1\', \'$2\')',
					 '</p><blockquote><div class="incqbox" onclick="toggle_spoiler(this)"><h4>$2 ('.$lang_topic['Click to open'].')</h4><p style="visibility:hidden; display:none; height:0;">$3</p></div></blockquote><p>',
                     '<acronym>$1</acronym>',
                     '<acronym title="$1">$2</acronym>',
					 '<span style="color: $1">$2</span>',
                     '</p><hr /><p>');

	if ($pun_config['p_message_img_tag'] == '1')
	{
		$pattern[] = '#\[img\]((ht|f)tps?://)([^\s<"]*?)\[/img\]#e';
		$pattern[] = '#\[img=([^\[]*?)\]((ht|f)tps?://)([^\s<"]*?)\[/img\]#e';
		$pattern[] = '#\[img\]([0-9_]+)\.(\w+)\[/img\]#e';
		$pattern[] = '#\[img=([^\[]*?)\]([0-9_]+)\.(\w+)\[/img\]#e';
		if ($is_signature)
		{
			$replace[] = 'handle_img_tag(\'$1$3\', true)';
			$replace[] = 'handle_img_tag(\'$2$4\', true, \'$1\')';
			$replace[] = 'handle_c2c_img_tag(\'$1\', \'$2\', true)';
			$replace[] = 'handle_c2c_img_tag(\'$2\', \'$3\', true, \'$1\')';
		}
		else
		{
			$replace[] = 'handle_img_tag(\'$1$3\', false)';
			$replace[] = 'handle_img_tag(\'$2$4\', false, \'$1\')';
			$replace[] = 'handle_c2c_img_tag(\'$1\', \'$2\', false)';
			$replace[] = 'handle_c2c_img_tag(\'$2\', \'$3\', false, \'$1\')';
		}
	}

	// This thing takes a while! :)
	$text = preg_replace($pattern, $replace, $text);

	return $text;
}


//
// Make hyperlinks between < > or [ ] clickable
//
function pre_do_clickable($text)
{
	global $pun_config;
    
	$text = ' '.$text;

    $pattern[] ='#((?<=[\s\(\)\>:.;])|[\<\[]+)(https?|ftp|news){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^"\s\(\)<\>\[\]]*)?)[\>\]]*#i';
    $pattern[] ='#((?<=[\s\(\)\>:;])|[\<\[]+)(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^"\s\(\)<\>\[\]]*)?)[\>\]]*#i';

    if ($pun_config['p_message_bbcode'] == '1')
    {
        $replace[] = '[url]$2://$3[/url]';
        $replace[] = '[url]$2.$3[/url]';
    }
    else
    {
        $replace[] = '$2://$3 ';
        $replace[] = '$2.$3 ';
    }
    
	$text = preg_replace($pattern, $replace, $text);
	
    return substr($text, 1);
}


//
// Make hyperlinks clickable
//
function do_clickable($text)
{
	global $pun_user;

	$text = ' '.$text;

	$text = preg_replace('#([\s\(\):.;])(https?|ftp|news){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^"\s\(\)<\[]*)?)#ie', '\'$1\'.handle_url_tag(\'$2://$3\')', $text);
	$text = preg_replace('#([\s\(\):;])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^"\s\(\)<\[]*)?)#ie', '\'$1\'.handle_url_tag(\'$2.$3\', \'$2.$3\')', $text);

	return substr($text, 1);
}


//
// Convert a series of smilies to images
//
function do_smilies($text)
{
	global $smiley_text, $smiley_img;

	$text = ' '.$text.' ';

	$num_smilies = count($smiley_text);
	for ($i = 0; $i < $num_smilies; ++$i)
		$text = preg_replace("#(?<=.\W|\W.|^\W)".preg_quote($smiley_text[$i], '#')."(?=.\W|\W.|\W$)#m", '$1<img src="img/smilies/'.$smiley_img[$i].'" width="15" height="15" alt="'.$smiley_text[$i].'" />$2', $text);

	return substr($text, 1, -1);
}


//
// Convert video tags to HTML
//
function do_video($text)
{
    if (stripos($text, '[/video]') !== FALSE)
    {
    	$largeur = 400;
    	$hauteur = 300;
    	$alternatif = '<strong>Veuillez installer le pluggin FLASH</strong>';
    	
        // Dailymotion
    	$code_du_lecteur = "\n\t\t\t\t\t<object width=\"".$largeur."\" height=\"".$hauteur."\">\n\t\t\t\t\t  <param name=\"movie\" value=\"http://www.dailymotion.com/swf/$1"."&v3=1&related=1\"></param><embed src=\"http://www.dailymotion.com/swf/$1"."&v3=1&related=1\" type=\"application/x-shockwave-flash\" width=\"".$largeur."\" height=\"".$hauteur."\"></embed>\n\t\t\t\t\t</object>\n\t\t\t\t\t";
    	$text = preg_replace('#\[video\].+/video/([^  _]+)_.+\[/video\]#isU', $code_du_lecteur, $text);
    	$code_du_lecteur_taille = "\n\t\t\t\t\t<object width=\"$1\" height=\"$2\">\n\t\t\t\t\t  <param name=\"movie\" value=\"http://www.dailymotion.com/swf/$3"."&v3=1&related=1\"></param><embed src=\"http://www.dailymotion.com/swf/$3"."&v3=1&related=1\" type=\"application/x-shockwave-flash\" width=\"$1\" height=\"$2\"></embed>\n\t\t\t\t\t</object>\n\t\t\t\t\t";
    	$text =  preg_replace('#\[video ([0-9]{2,4}),([0-9]{2,4})\].+/video/([^ _]+)_.+\[/video\]#isU', $code_du_lecteur_taille, $text);
    	
        // Youtube
    	$code_du_lecteur = "\n\t\t\t\t\t<object width=\"".$largeur."\" height=\"".$hauteur."\">\n\t\t\t\t\t  <param name=\"movie\" value=\"http://www.youtube.com/v/$1"."&rel=1\"></param><embed src=\"http://www.youtube.com/v/$1"."&rel=1\" type=\"application/x-shockwave-flash\" width=\"".$largeur."\" height=\"".$hauteur."\"></embed>\n\t\t\t\t\t</object>\n\t\t\t\t\t";
    	$text = preg_replace('#\[video\].+watch\?v=(.+)\[/video\]#isU', $code_du_lecteur, $text);
    	$code_du_lecteur_taille = "\n\t\t\t\t\t<object width=\"$1\" height=\"$2\">\n\t\t\t\t\t  <param name=\"movie\" value=\"http://www.youtube.com/v/$3"."&rel=1\"></param><embed src=\"http://www.youtube.com/v/$3"."&rel=1\" type=\"application/x-shockwave-flash\" width=\"$1\" height=\"$2\"></embed>\n\t\t\t\t\t</object>\n\t\t\t\t\t";
    	$text =  preg_replace('#\[video ([0-9]{2,4}),([0-9]{2,4})\].+watch\?v=(.+)\[/video\]#isU', $code_du_lecteur_taille, $text);
    	
        // Google Video
    	$code_du_lecteur = "\n\t\t\t\t\t<object width=\"".$largeur."\" height=\"".$hauteur."\">\n\t\t\t\t\t  <param name=\"movie\" value=\"http://video.google.com/googleplayer.swf?docId=$1\"></param><embed src=\"http://video.google.com/googleplayer.swf?docId=$1\" type=\"application/x-shockwave-flash\" width=\"".$largeur."\" height=\"".$hauteur."\"></embed>\n\t\t\t\t\t</object>\n\t\t\t\t\t";
    	$text = preg_replace('#\[video\].+videoplay\?docid=([^  ]+)\[/video\]#isU', $code_du_lecteur, $text);
    	$code_du_lecteur_taille = "\n\t\t\t\t\t<object width=\"$1\" height=\"$2\">\n\t\t\t\t\t  <param name=\"movie\" value=\"http://video.google.com/googleplayer.swf?docId=$3\"></param><embed src=\"http://video.google.com/googleplayer.swf?docId=$3\" type=\"application/x-shockwave-flash\" width=\"$1\" height=\"$2\"></embed>\n\t\t\t\t\t</object>\n\t\t\t\t\t";
    	$text =  preg_replace('#\[video ([0-9]{2,4}),([0-9]{2,4})\].+videoplay\?docid=([^  ]+)\[/video\]#isU', $code_du_lecteur_taille, $text);
    	
        // Stage6
    	$code_du_lecteur = "\n\t\t\t\t\t<object codebase=\"http://go.divx.com/plugin/DivXBrowserPlugin.cab\" width=\"".$largeur."\" height=\"".$hauteur."\">\n\t\t\t\t\t  <param name=\"autoplay\" value=\"false\" /><param name=\"src\" value=\"http://video.stage6.com/$1/.divx\"></param><embed src=\"http://video.stage6.com/$1/.divx\" type=\"video/divx\" width=\"".$largeur."\" height=\"".$hauteur."\" autoplay=\"false\"></embed>\n\t\t\t\t\t</object>\n\t\t\t\t\t";
    	$text = preg_replace('#\[video\].+/video/(.+)/.+\[/video\]#isU', $code_du_lecteur, $text);
    	$code_du_lecteur_taille = "\n\t\t\t\t\t<object codebase=\"http://go.divx.com/plugin/DivXBrowserPlugin.cab\" width=\"$1\" height=\"$2\">\n\t\t\t\t\t  <param name=\"autoplay\" value=\"false\" /><param name=\"src\" value=\"http://video.stage6.com/$3/.divx\"></param><embed src=\"http://video.stage6.com/$3/.divx\" type=\"video/divx\" width=\"$1\" height=\"$2\" autoplay=\"false\"></embed>\n\t\t\t\t\t</object>\n\t\t\t\t\t";
    	$text =  preg_replace('#\[video ([0-9]{2,4}),([0-9]{2,4})\].+/video/(.+)/.+\[/video\]#isU', $code_du_lecteur_taille, $text);
    }
    
    return $text;
}


//
// Parse message text
//
function parse_message($text, $hide_smilies)
{
	global $pun_config, $pun_user, $lang_common, $lang_topic;

	if ($pun_config['o_censoring'] == '1')
		$text = censor_words($text);

	// If the message contains a code tag we have to split it up (text within [code][/code] shouldn't be touched)
	if (strpos($text, '[code]') !== false && strpos($text, '[/code]') !== false)
	{
		list($inside, $outside) = split_text($text, '[code]', '[/code]');
		
        // Active links between < > or [ ]
        if ($pun_config['o_make_links'] == '1')
        {
            $outside = array_map('pre_do_clickable', $outside);
        }
        
        // Convert applicable characters to HTML entities
        $inside = array_map('pun_htmlspecialchars', $inside);
        $outside = array_map('pun_htmlspecialchars', $outside);
        
        // Implode non code text in one string for next parsing
        $outside = array_map('ltrim', $outside);
		$text = implode('<">', $outside);
	}
    else
    {
        // Active links between < > or [ ]
        if ($pun_config['o_make_links'] == '1')
        {
            $text = pre_do_clickable($text);
        }
        
        // Convert applicable characters to HTML entities
    	$text = pun_htmlspecialchars($text);
    }

	if ($pun_config['o_make_links'] == '1')
		$text = do_clickable($text);

	if ($pun_config['o_smilies'] == '1' && $pun_user['show_smilies'] == '1' && $hide_smilies == '0')
		$text = do_smilies($text);

	if ($pun_config['p_message_bbcode'] == '1' && strpos($text, '[') !== false && strpos($text, ']') !== false)
	{
		$text = do_bbcode($text);
        $text = do_video($text);
	}

	// Deal with newlines, tabs and multiple spaces
	$pattern = array("\n", "\t", '	', '  ', '<p><br />');
	$replace = array('<br />', '&nbsp; &nbsp; ', '&nbsp; ', ' &nbsp;', '<p>');
	$text = str_replace($pattern, $replace, $text);

	// If we split up the message before we have to concatenate it together again (code tags)
	if (isset($inside))
	{
		$outside = explode('<">', $text);
		$text = '';

		$num_tokens = count($outside);

		for ($i = 0; $i < $num_tokens; ++$i)
		{
			$text .= $outside[$i];
			if (isset($inside[$i]))
			{
				$num_lines = ((substr_count($inside[$i], "\n")) + 2) * 1.4;
				$height_str = ($num_lines > 35) ? '35em' : $num_lines.'em';
				$text .= '</p><div class="codebox"><div class="incqbox"><h4>'.$lang_common['Code'].':</h4><div class="scrollbox" style="height: '.$height_str.'"><pre>'.$inside[$i].'</pre></div></div></div><p>';
			}
		}
	}

	// Add paragraph tag around post, but make sure there are no empty paragraphs
	$text = str_replace('<p></p>', '', '<p>'.$text.'</p>');

	return $text;
}


//
// Parse signature text
//
function parse_signature($text)
{
	global $pun_config, $pun_user, $lang_common, $lang_topic;

	if ($pun_config['o_censoring'] == '1')
		$text = censor_words($text);

	if ($pun_config['o_make_links'] == '1')
		$text = pre_do_clickable($text);

	$text = pun_htmlspecialchars($text);

	if ($pun_config['o_make_links'] == '1')
		$text = do_clickable($text);

	if ($pun_config['o_smilies_sig'] == '1' && $pun_user['show_smilies'] != '0')
		$text = do_smilies($text);

	if ($pun_config['p_sig_bbcode'] == '1' && strpos($text, '[') !== false && strpos($text, ']') !== false)
	{
		$text = do_bbcode($text);
	}

	// Deal with newlines, tabs and multiple spaces
	$pattern = array("\n", "\t", '  ', '  ', '<p><br />');
	$replace = array('<br />', '&nbsp; &nbsp; ', '&nbsp; ', ' &nbsp;', '<p>');
	$text = str_replace($pattern, $replace, $text);

	return $text;
}
