<?php
/**
 * Code below is adapted from PunBB.
 *
 * sfBBCodeParserPlugin has been previously tested but was replaced because it did not enable
 * to use the same tag several times in the same text (bug!?).
 *
 * $Id: sfPunBBCodeParser.class.php 1753 2007-09-22 13:59:05Z alex $
 */

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

// FIXME: methods names do not match coding standards requirements

class sfPunBBCodeParser
{
    //
    // Make sure all BBCodes are lower case and do a little cleanup
    //
    public static function preparse_bbcode($text, &$errors)
    {
    	// Change all simple BBCodes to lower case
    	$a = array('[B]', '[I]', '[U]', '[S]', '[Q]', '[C]', '[/B]', '[/I]', '[/U]', '[/S]', '[/Q]', '[/C]', '[/P]');
    	$b = array('[b]', '[i]', '[u]', '[s]', '[q]', '[c]', '[/b]', '[/i]', '[/u]', '[/s]', '[/q]', '[/c]', '[/p]');
    	$text = str_replace($a, $b, $text);

    	// Do the more complex BBCodes (also strip excessive whitespace and useless quotes)
    	$a = array( '#\[url=("|\'|)(.*?)\\1\]\s*#i',
    				'#\[url(=\]|\])\s*#i',
    				'#\s*\[/url\]#i',
    				'#\[email=("|\'|)(.*?)\\1\]\s*#i',
    				'#\[email(=\]|\])\s*#i',
    				'#\s*\[/email\]#i',
    				'#\[img=\s?("|\'|)(.*?)\\1\]\s*#i',
     				'#\[img(=\]|\])\s*#i',
    				'#\s*\[/img\]#i',
                    '#\[colou?r=("|\'|)(.*?)\\1\]\s*#i',
                    '#\[/colou?r\]\s*#i',
                    '#\[(cent(er|re|ré)|<>)\]\s*#i',
                    '#\[/(cent(er|re|ré)|<>)\]\s?#i',
                    '#\[(right|rigth|ritgh|rithg|droite?|>)\]\s*#i',
                    '#\[/(right|rigth|ritgh|rithg|droite?|>)\]\s?#i',
                    '#\[(justif(y|ie|ié|)|=)\]\s*#i',
                    '#\[/(justif(y|ie|ié|)|=)\]\s?#i',
                    '#\[p\]\s?#s',
    		        '#\[quote=(&quot;|"|\'|)(.*?)\\1\]\s*#i',
    		        '#\[quote(=\]|\])\s*#i',
    		        '#\s*\[/quote\]\s?#i',
    		        '#\[code\][\r\n]*(.*?)\s*\[/code\]\s?#is'
                );

    	$b = array(	'[url=$2]',
    				'[url]',
    				'[/url]',
    				'[email=$2]',
    				'[email]',
    				'[/email]',
    				'[img=$2]',
    				'[img]',
    				'[/img]',
    				'[color=$2]',
                    '[/color]',
                    '[center]',
                    '[/center]'."\n",
                    '[right]',
                    '[/right]'."\n",
                    '[justify]',
                    '[/justify]'."\n",
                    '[p]'."\n",
    		        '[quote=$1$2$1]',
    		        '[quote]',
    		        '[/quote]'."\n",
    		        '[code]$1[/code]'."\n"
                );
    
        $a[] = '#(?<!^|\n)([ \t]*)([(center|right|justify|quote|code|spoiler|video))#i';
        $b[] = '$1'."\n".'$2';

    	// Run this baby!
    	$text = preg_replace($a, $b, $text);

        $overflow = self::check_tag_order($text, $error);

        if ($error)
            // A BBCode error was spotted in check_tag_order()
            $errors[] = $error;
        else if ($overflow)
            // The quote depth level was too high, so we strip out the inner most quote(s)
            $text = substr($text, 0, $overflow[0]).substr($text, $overflow[1], (strlen($text) - $overflow[0]));

    	return trim($text);
    }


  /**
     * Parse text and make sure that [code] and [quote] syntax is correct
     */
    public static function check_tag_order($text, &$error)
    {
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
    				$error = __('Missing start tag for [/quote].');
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
    				$error = __('Missing end tag for [code].');
    				return;
    			}
    			else
    				$text = substr($text, $tmp+7);
    
    			$cur_index += $tmp+7;
    		}
    
    		// We found a [/code] (this shouldn't happen since we handle both start and end tag in the if clause above)
    		else if ($c_end < min($c_start, $q_start, $q_end))
    		{
    			$error = __('Missing start tag for [/code].');
    			return;
    		}
    	}
    
    	// If $q_depth <> 0 something is wrong with the quote syntax
    	if ($q_depth)
    	{
    		$error = __('Missing one or more end tags for [quote].');
    		return;
    	}
    	else if ($q_depth < 0)
    	{
    		$error = __('Missing one or more start tags for [/quote].');
    		return;
    	}
    
    	// If the quote depth level was higher than $max_depth we return the index for the
    	// beginning and end of the part we should strip out
    	if (isset($overflow_begin))
    		return array($overflow_begin, $overflow_end);
    	else
    		return null;
    }
    
    /**
     * Split text into chunks ($inside contains all text inside $start and $end, and $outside contains all text outside)
     */
    public static function split_text($text, $start, $end)
    {
    
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
    
    /**
     * Truncate URL if longer than 55 characters (add http:// or ftp:// if missing)
     */
    public static function handle_url_tag($url, $link = '', $target = '')
    {
    	$full_url = str_replace(array(' ', '\'', '`', '"'), array('%20', '', '', ''), $url);
    	if ($url == '')
        {
            $url == ' ';
        }
        
        if ($full_url == '' && $link == '')
            return '';
    	else if (strpos($url, 'www.') === 0)			// If it starts with www, we add http://
    		$full_url = 'http://'.$full_url;
    	else if (strpos($url, 'ftp.') === 0)	// Else if it starts with ftp, we add ftp://
    		$full_url = 'ftp://'.$full_url;
    	else if ((strpos("#/", $url[0]) === false) && !preg_match('#^([a-z0-9]{3,6})://#', $url, $bah)) 	// Else if it doesn't start with abcdef:// nor #, we add http://
    		$full_url = 'http://'.$full_url;
    
        if ($link == '' || $link == $url)
        {
            // Truncate link text if its an internal URL
            $base_url = 'http://'.$_SERVER['SERVER_NAME'].'/';
            if ((strlen($full_url) > strlen($base_url)) && (stripos($full_url, $base_url) === 0))
            {
                $link = substr($full_url, strlen($base_url));
            }
            else
            {
                $link = $url;
                if (strpos("#/", $link[0]) !== false)
                {
                    $link = substr($link, 1);
                }
            }

            // Truncate URL if longer than 55 characters
            $link = ((strlen($link) > 55) ? substr($link, 0 , 39).' &hellip; '.substr($link, -10) : $link);
        }
        else
        {
            $link = stripslashes($link);
        }

        if (!empty($target)) $target = ' target="' . $target . '"';

	    // Check if internal or external link
        if ((strpos("#/", $full_url[0]) !== false) || preg_match('#^https?://'.$_SERVER['SERVER_NAME'].'#', $full_url))
            return '<a href="' . $full_url . '"' . $target . '>' . $link . '</a>';
        return '<a class="external_link" href="' . $full_url . '"' . $target . '>' . $link . '</a>';
    }
    
    /**
     * Turns an URL from the [img] tag into an <img> tag or a <a href...> tag
     */
    public static function handle_img_tag($filename, $extension, $legend = '')
    {
        $static_base_url = sfConfig::get('app_static_url');
        return sprintf('<a rel="lightbox[embedded_images]" class="view_big" title="%s" href="%s/uploads/images/%s"><img ' .
                       'class="embedded" src="%s/uploads/images/%s" alt="%s" title="%s" /></a>',
                       $legend,
                       $static_base_url,
                       $filename . 'BI.' . $extension,
                       $static_base_url,
                       $filename . 'MI.' . $extension,
                       $filename . '.' . $extension,
                       empty($legend) ? __('click to enlarge') : $legend);
    }

    /**
     * Email obfuscation against spam
     */
    public static function handle_email_tag($email, $label = NULL)
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
    
    /**
     * Convert BBCodes to their HTML equivalent
     */
    public static function do_bbcode($text, $extended, $force_external_links = false)
    {
    	if ($extended && (strpos($text, 'quote') !== false))
    	{
    		$text = str_replace('[quote]', '</p><blockquote><div class="incqbox"><p>', $text);
    		$text = preg_replace('#\[quote=(&quot;|"|\'|)(.*)\\1\]#seU', '"</p><blockquote><div class=\"incqbox\"><h4>".str_replace(array(\'[\', \'\\"\'), array(\'&#91;\', \'"\'), \'$2\')." :</h4><p>"', $text);
    		$text = preg_replace('#\[\/quote\]\s*#', '</p></div></blockquote><p>', $text);
    	}
    
    	$pattern = array('#\[b\](.*?)\[/b\]#s',
    					 '#\[i\](.*?)\[/i\]#s',
    					 '#\[u\](.*?)\[/u\]#s',
                         '#\[s\](.*?)\[/s\]#s',
                         '#\[q\](.*?)\[/q\]#s',
                         '#\[c\](.*?)\[/c\]#s',
    					 '#\[url\]([^\[]*?)\[/url\]#e',
    					 '#\[url=([^\[]*?)\](.*?)\[/url\]#e',
    					 '#\[email\]([^\[]*?)\[/email\]#e',
    					 '#\[email=([^\[]*?)\](.*?)\[/email\]#e',
                         '#\[acronym\]([^\[]*?)\[/acronym\]#',
                         '#\[acronym=([^\[]*?)\](.*?)\[/acronym\]#',
    					 '#\[colou?r=([a-zA-Z]{3,20}|\#?[0-9a-fA-F]{6})](.*?)\[/colou?r\]#s',
                         '#\[p\]#s',
                         '#\[center\](.*?)\[/center\]\s*#s',
                         '#\[right\](.*?)\[/right\]\s*#s',
                         '#\[justify\](.*?)\[/justify\]\s*#s'
);
    
    	$replace = array('<strong>$1</strong>',
    					 '<em>$1</em>',
    					 '<span style="text-decoration: underline;">$1</span>',
                         '<del>$1</del>',
                         '<q>$1</q>',
                         '<code>$1</code>',
    					 $force_external_links ? 'self::handle_url_tag(\'$1\', \'\', \'_blank\')' : 'self::handle_url_tag(\'$1\')',
    					 $force_external_links ? 'self::handle_url_tag(\'$1\', \'$2\', \'_blank\')' : 'self::handle_url_tag(\'$1\', \'$2\')',
    					 'self::handle_email_tag(\'$1\')',
    					 'self::handle_email_tag(\'$1\', \'$2\')',
                         '<acronym>$1</acronym>',
                         '<acronym title="$1">$2</acronym>',
    					 '<span style="color: $1">$2</span>'
                        );
        if ($extended)
        {
            $replace[] = '</p><p>';
            $replace[] = '</p><div style="text-align: center;"><p>$1</p></div><p>';
            $replace[] = '</p><div style="text-align: right;"><p>$1</p></div><p>';
            $replace[] = '</p><div style="text-align: justify;"><p>$1</p></div><p>';
        }
        else
        {
            $replace[] = "\n";
            $replace[] = '$1';
            $replace[] = '$1';
            $replace[] = '$1';
        }
    
    	// This thing takes a while! :)
    	$text = preg_replace($pattern, $replace, $text);
    
    	return $text;
    }


    //
    // Make hyperlinks between < > or [ ] clickable
    //
    public static function do_clickable($text)
    {
    	$text = ' '.$text;

        $pattern[] ='#((?<=[\s\(\)\>:.;,])|[\<\[]+)(https?|ftp|news){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/((?!\.(\s|\Z))[^"\s\(\)<\>\[\]:;,])*)?)[\>\]]*#i';
        $pattern[] ='#((?<=[\s\(\)\>:;,])|[\<\[]+)(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/((?!\.(\s|\Z))[^"\s\(\)<\>\[\]:;,])*)?)[\>\]]*#i';
        $pattern[] ='#((?<=["\'\s\(\)\>:;,])|[\<\[]+)(([\w\-]+\.)*[\w\-]+)@(([\w\-]+\.)+[\w]+([^"\'\s\(\)<\>\[\]:.;,]*)?)[\>\]]*#i';

        $replace[] = '[url]$2://$3[/url]';
        $replace[] = '[url]$2.$3[/url]';
        $replace[] = '[email]$2@$4[/email]';
        
    	$text = preg_replace($pattern, $replace, $text);
    	
        return substr($text, 1);
    }

    /**
     * Parse message text
     */
    public static function parse_message($text, $hide_smilies = false)
    {
    	// If the message contains a code tag we have to split it up (text within [code][/code] shouldn't be touched)
    	if (strpos($text, '[code]') !== false && strpos($text, '[/code]') !== false)
    	{
    		list($inside, $outside) = self::split_text($text, '[code]', '[/code]');
		
            // Active links between < > or [ ]
            $outside = array_map('self::do_clickable', $outside);
            
    		$outside = array_map('ltrim', $outside);
    		$text = implode('<">', $outside);
    	}
        else
        {
            // Active links between < > or [ ]
            $text = self::do_clickable($text);
        }
    
        $text = self::do_bbcode($text, true);
    
        // accepts only internal images (filename)
        // [img]<image file>[/img] or [img=<image file>]<image legend>[/img]
        $text = preg_replace(array('#\[img\]( *)([0-9_]*?)\.(jpg|jpeg|png|gif)( *)\[/img\]#e',
                                   '#\[img=( *)([0-9_]*?)\.(jpg|jpeg|png|gif)( *)\](.*?)\[/img\]#e' ),
                             array('self::handle_img_tag(\'$2\', \'$3\')', 'self::handle_img_tag(\'$2\', \'$3\', \'$5\')'),
                             $text);
    
    	// Deal with newlines, tabs and multiple spaces
    	$pattern = array("\n", "\t", '	', '  ');
    	$replace = array('<br />', '&nbsp; &nbsp; ', '&nbsp; ', ' &nbsp;');
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
    				$num_lines = ((substr_count($inside[$i], "\n")) + 3) * 1.5;
    				$height_str = ($num_lines > 35) ? '35em' : $num_lines.'em';
    				$text .= '</p><div class="codebox"><div class="incqbox"><h4>Code :</h4><div class="scrollbox" style="height: '.$height_str.'"><pre>'.$inside[$i].'</pre></div></div></div><p>';
    			}
    		}
    	}
    
    	// Add paragraph tag around post, but make sure there are no empty paragraphs
    	$text = str_replace('<p></p>', '', '<p>'.$text.'</p>');
    
    	return $text;
    }

    public static function parse_message_simple($text)
    {
        $text = self::do_clickable($text);
        $text = self::do_bbcode($text, false, true);
    
        // remove embedded images 
        $text = preg_replace('#\[img(.*?)\](.*)\[/img\]#e', '', $text);
    
    	// Deal with newlines, tabs and multiple spaces
    	$pattern = array("\t", '	', '  ');
    	$replace = array('&nbsp; &nbsp; ', '&nbsp; ', ' &nbsp;');
    	$text = str_replace($pattern, $replace, $text);
    
    	return $text;
    }

    public static function parse_message_abstract($text)
    {
        $text = self::do_clickable($text);
        $text = self::do_bbcode($text, true, true);
    
        // remove embedded images 
        $text = preg_replace('#\[img(.*?)\](.*)\[/img\]#e', '', $text);
    
    	// Deal with newlines, tabs and multiple spaces
    	$pattern = array("\n", "\t", '	', '  ');
    	$replace = array('<br />', '&nbsp; &nbsp; ', '&nbsp; ', ' &nbsp;');
    	$text = str_replace($pattern, $replace, $text);
    
    	// Add paragraph tag around post, but make sure there are no empty paragraphs
    	$text = str_replace('<p></p>', '', '<p class="abstract">'.$text.'</p>');
    
    	return $text;
    }
}
