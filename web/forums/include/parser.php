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
    $a = array('[B]', '[I]', '[U]', '[S]', '[Q]', '[C]', '[P]', '[/B]', '[/I]', '[/U]', '[/S]', '[/Q]', '[/C]');
    $b = array('[b]', '[i]', '[u]', '[s]', '[q]', '[c]', '[p]', '[/b]', '[/i]', '[/u]', '[/s]', '[/q]', '[/c]');
    $text = str_replace($a, $b, $text);

    // Do the more complex BBCodes (also strip excessive whitespace and useless quotes)
    $base_url = 'http://'.$_SERVER['SERVER_NAME'];
    
    $a = array( '#\[url=("|\'|)(.*?)\\1\s*\]\s*#i',
                '#\[url(=\]|\])\s*#i',
                '#\s*\[/url\]#i',
                '#\[url=(.*?)\]\\1\[/url\]#i',
                '#\[url(=|\])((https?:)?(//)?(w+|m+)\.|)camptocamp\.org(/([^\[\]]+))#i',
                '#\[email=("|\'|)(.*?)\\1\s*\]\s*#i',
                '#\[email(=\]|\])\s*#i',
                '#\s*\[/email\]#i',
                '#\[email=(.*?)\]\\1\[/email\]#i',
                '#\[img=\s*("|\'|)(.*?)\\1\s*\]\s*#i',
                '#\[img(=\]|\])\s*#i',
                '#\[img(=|\])' . $base_url . '#i',
                '#\s*\[/img\]#i',
                '#\[colou?r=("|\'|)(.*?)\\1\s*\]\s*#i',
                '#\[/colou?r\]#i',
                '#\[(cent(er|re|ré)|<>)\]\s*#i',
                '#\[/(cent(er|re|ré)|<>)\]\s?#i',
                '#\[(right|rigth|ritgh|rithg|droite?|>)\]\s*#i',
                '#\[/(right|rigth|ritgh|rithg|droite?|>)\]\s?#i',
                '#\[(justif(y|ie|ié|)|=)\]\s*#i',
                '#\[/(justif(y|ie|ié|)|=)\]\s?#i'
              );

    $b = array( '[url=$2]',
                '[url]',
                '[/url]',
                '[url]$1[/url]',
                '[url$1$6',
                '[email=$2]',
                '[email]',
                '[/email]',
                '[email]$1[/email]',
                '[img=$2]',
                '[img]',
                '[img$1',
                '[/img]',
                '[color=$2]',
                '[/color]',
                '[center]',
                '[/center]'."\n",
                '[right]',
                '[/right]'."\n",
                '[justify]',
                '[/justify]'."\n"
              );

    if (!$is_signature)
    {
        // For non-signatures, we have to do the quote and code tags as well
        $a[] = '#\[quote=(&quot;|"|\'|)(.*?)\\1\s*\]\s*#i';
        $a[] = '#\[quote(=\]|\])\s*#i';
        $a[] = '#\s*\[/quote\]\s?#i';
        $a[] = '#\[code\][\r\n]*(.*?)\s*\[/code\]\s?#is';
        $a[] = '#\[spoiler=("|\'|)(.*?)\\1\]\s*#i';
        $a[] = '#\[spoiler(=\]|\])\s*#i';
        $a[] = '#\s*\[/spoiler\]\s?#i';
        $a[] = '#\[video([^0-9\]]*)([0-9]+)([^0-9\]]+)([0-9]+)([^0-9\]]*)\]\s*#i';
        $a[] = '#\[video\]\s*#i';
        $a[] = '#\s*\[/video\]\s?#i';
        $a[] = '#\[p\]\s?#i';

        $b[] = '[quote=$1$2$1]';
        $b[] = '[quote]';
        $b[] = '[/quote]'."\n";
        $b[] = '[code]$1[/code]'."\n";
        $b[] = '[spoiler=$2]';
        $b[] = '[spoiler]';
        $b[] = '[/spoiler]'."\n";
        $b[] = '[video $2,$4]';
        $b[] = '[video]';
        $b[] = '[/video]'."\n";
        $b[] = '[p]'."\n";
    }
    
    $a[] = '#(?<!^|\n)([ \t]*)(\[(center|right|justify|quote|code|spoiler|video))#i';
    $b[] = '$1'."\n".'$2';
    
    // Run this baby!
    $text = preg_replace($a, $b, $text);

    if (!$is_signature)
    {
        $overflow = check_tag_order($text, $error);

        if ($error)
            // A BBCode error was spotted in check_tag_order()
            $errors[] = $error;
        elseif ($overflow)
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
// Reduce internal url
//
function preparse_url($text)
{
    $a = array( '#(?<=[^\w]|^)((https?:)?(//)?(w+|m+)\.|(?<!\.))camptocamp\.org(/(outings|routes|summits|sites|huts|parkings|images|articles|areas|books|products|map|users|portals|forums|tools))#i',
                '%(?<=[^\w/]|^)/*forums/viewforum.php\?id=(\d+)(&p=\d+)?%i',
                '%(?<=[^\w/]|^)/*forums/viewtopic.php\?id=(\d+)&action=new%i',
                '%(?<=[^\w/]|^)/*forums/viewtopic.php\?id=(\d+)(&p=\d+)?%i',
                '%(?<=[^\w/]|^)/*forums/viewtopic.php\?pid=\d+#p(\d+)%i',
                '%(?<=[^\w/]|^)/*forums/viewtopic.php\?pid=(\d+)%i'
              );
    
    $b = array( '$5',
                '#f$1',
                '#t$1+',
                '#t$1',
                '#p$1',
                '#p$1'
              );
      
    $text = preg_replace($a, $b, $text);

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
        elseif ($q_end < min($q_start, $c_start, $c_end))
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
        elseif ($c_start < min($c_end, $q_start, $q_end))
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
        elseif ($c_end < min($c_start, $q_start, $q_end))
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
    elseif ($q_depth < 0)
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
// Convert starting quote tag
//
function handle_quote_tag($poster_name, $post_id)
{
    global $showed_post_list, $lang_common;
    
    $start_quote = '</p><blockquote><div class="incqbox"><h4>';
    $poster_wrote = str_replace(array('[', '\"'), array('&#91;', '"'), $poster_name).' '.$lang_common['wrote'].':';
    if ($post_id == '')
    {
        $start_quote .= $poster_wrote;
    }
    elseif (is_numeric($post_id))
    {
        $post_id = intval($post_id);
        $post_link = '';
        $rel = '';
        if (!in_array($post_id, $showed_post_list))
        {
            $post_link = '/forums/viewtopic.php?pid='.$post_id;
            $rel = ' rel="nofollow"';
        }
        $post_link .= '#p'.$post_id;
        $start_quote .= '<a href="'.$post_link.'"'.$rel.'>'.$poster_wrote.'</a>';
    }
    else
    {
        $start_quote .= handle_url_tag($post_id, $poster_wrote);
    }
    
    $start_quote .= '</h4><p>';
    
    return $start_quote;
}


//
// Truncate URL if longer than 55 characters (add http:// or ftp:// if missing)
//
function handle_url_tag($url, $link = '', $show_video = false)
{
    global $showed_post_list, $lang_common, $pun_config;

    // prevent double inclusion of links (happens for example if we use [url=http://example.com]http://example.com[/url]
    // if we have a <a> tag in link just skip the inner content.
    if (!empty($link) && strpos($link, '<a') !== false)
    {
        $link = '';
    }

    $hreflang = '';
    $rel = '';
    
    $full_url = str_replace(array(' ', '\'', '`', '"'), array('%20', '', '', ''), $url);
    if ($url == '')
    {
        $url == ' ';
    }

    $full_url = preg_replace('#^((https?:)?(//)?(w+|m+)\.|(?<!\.))camptocamp\.org/?(.*)#', '/${5}', $full_url);
    if ($empty_link = (empty($link) || $link == $url))
    {
        if ($full_url == '/')
        {
            $link = $url;
        }
        else
        {
            $link = $full_url;
        }
    }
    
    $is_forum_url = false;
    if ($full_url == '' && $link == '')
    {
        return '';
    }
    elseif (preg_match('#(?<=[^\w/]|^)/*forums/view(topic|forum).php\?p?id=\d+#i', $full_url, $bah))     // Else if it is a forum url
    {
        $a = array( '%(?<=[^\w/]|^)/*forums/viewforum.php\?id=(\d+)(&p=\d+)?%i',
                    '%(?<=[^\w/]|^)/*forums/viewtopic.php\?id=(\d+)&action=new%i',
                    '%(?<=[^\w/]|^)/*forums/viewtopic.php\?id=(\d+)(&p=\d+)?%i',
                    '%(?<=[^\w/]|^)/*forums/viewtopic.php\?pid=\d+#p(\d+)%i',
                    '%(?<=[^\w/]|^)/*forums/viewtopic.php\?pid=(\d+)%i'
                  );

        $b = array( '#f$1',
                    '#t$1+',
                    '#t$1',
                    '#p$1',
                    '#p$1'
                  );
        $full_url = preg_replace($a, $b, $full_url);
        $is_forum_url = true;
    }
    elseif (strpos($full_url, 'www.') === 0)            // If it starts with www, we add http://
    {
        $full_url = 'http://'.$full_url;
    }
    elseif (strpos($full_url, 'ftp.') === 0)    // Else if it starts with ftp, we add ftp://
    {
        $full_url = 'ftp://'.$full_url;
    }
    elseif ((strpos("#/", $full_url[0]) === false) && !preg_match('#^([a-z0-9]{3,6})://#', $full_url, $bah))     // Else if it doesn't start with abcdef:// nor / nor #, we add http://
    {
        $full_url = 'http://'.$full_url;
    }
    else
    {
        $is_forum_url = true;
    }
    
    if ($is_forum_url && preg_match('/^#([fpt])(\d+)(\+?)/', $full_url, $params))
    {
        if ($empty_link)
        {
            $link = $full_url;
        }
        
        $id = $params[2];
        if ($params[1] == 't')
        {
            $full_url = '/forums/viewtopic.php?id='.$id;
            if ($params[3] == '+')
            {
                $full_url .= '&action=new';
            }
        }
        elseif ($params[1] == 'p' && (!is_array($showed_post_list) || !in_array($id, $showed_post_list)))
        {
            $full_url = '/forums/viewtopic.php?pid='.$id.'#p'.$id;
            $rel = ' rel="nofollow"';
        }
        elseif ($params[1] == 'f')
        {
            $full_url = '/forums/viewforum.php?id='.$id;
            $pub_forums = explode(', ', PUB_FORUMS);
            if (in_array($id, $pub_forums))
            {
                $rel = ' rel="nofollow"';
            }
        }
    }
    
    $is_internal_url = (strpos("#/", $full_url[0]) !== false);
        
    if ($empty_link)
    {
        // Truncate link text if its an internal URL
        if (strpos("#/", $link[0]) !== false)
        {
            $link = substr($link, 1);
        }

        // Truncate URL if longer than 55 characters
        $link = ((strlen($link) > 55) ? substr($link, 0 , 39).' &hellip; '.substr($link, -10) : $link);
    }
    else
    {
        $link = stripslashes($link);
    }

    // possibility to display pdf or ppt via google doc service
    if (preg_match('/\.(ppt|pdf)$/i', $full_url) && !c2cTools::mobileVersion())
    {
        $param_url = str_replace('%', '%25', $full_url);
        if ($is_internal_url)
        {
            $param_url = 'http://www.camptocamp.org' . $param_url;
        }
        $suffix = ' <a class="embedded_ppt_pdf" href="#" style="display:none" onclick="$(this).next().show(); $(this).hide();' .
                  ' $(this).next().next().remove(); return false;">' . __('close embedded') . '</a>' .
                  ' <a class="embedded_ppt_pdf" href="#" onclick="$(this).after(\'<iframe class=\\\'embedded_ppt_pdf\\\'' .
                  ' src=\\\'http://docs.google.com/gview?url=' . $param_url . '&amp;embedded=true\\\'></iframe>\');' .
                  ' $(this).prev().show(); $(this).hide(); return false;">' . __('see embedded') . '</a>';
    }
    else
    {
        $suffix = '';
    }

    // Check if internal or external link
    $class = ' class="external_link"';

    if ($is_internal_url) 
    { 
        $class = '';
        
        // si la langue est mentionnée et si elle est diffférente de la langue de l'interface, ajout du hreflang
        if (preg_match('#^/\w+/[\d]+/(\w{2})(/[\w-]+)?#i', $full_url, $params))
        {
            $hreflang = ' hreflang="' . $params[1] . '"';
        }
        
        // "nofollow" sur lien vers liste avec critère sur intitulé
        if (preg_match('#^/(outings|routes|summits|sites|huts|parkings|images|articles|areas|books|products|maps|users|portals)/[^\d]+/(.*)name?/#i', $full_url))
        {
            $rel = ' rel="nofollow"';
        }
    }

    // for forums, we try to automatically display videos for common providers
    // FIXME this is not very clean, but we don't want to do complex
    // regexp for each url, so we first check for presence of some keywords
    if ($show_video && $empty_link && preg_match('/(youtu|dailymotion|vimeo)/', $full_url))
    {
        $tag = '[video]' . $full_url . '[/video]';
        $output = do_video($tag);
        if (strstr($output, 'class="video'))
        {
            return $output;
        }
    }

    return '<a' . $class . ' href="' . $full_url . '"' . $hreflang . $rel . '>' . $link . '</a>' . $suffix;
}


//
// Turns an URL from the [img] tag into an <img> tag or a <a href...> tag
//
function handle_img_tag($url, $align, $is_signature = false, $alt=null)
{
    global $lang_common, $pun_config, $pun_user;

    $options = explode(' ', $align);
    $centered = false;
    
    if (in_array('left', $options))
    {
        $img_class = ' embedded_left';
    }
    elseif (in_array('right', $options))
    {
        $img_class = ' embedded_right';
    }
    elseif (in_array('inline', $options))
    {
        $img_class = ' embedded_inline';
    }
    elseif (in_array('inline_left', $options))
    {
        $img_class = ' embedded_inline_left';
    }
    elseif (in_array('inline_right', $options))
    {
        $img_class = ' embedded_inline_right';
    }
    elseif (in_array('center', $options))
    {
        $img_class = ' embedded_center';
        $centered = true;
    }
    else
    {
        $img_class = '';
    }
    
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

    $img_tag = '<a href="'.$url.'">&lt;&nbsp;'.$image_text.'&nbsp;&gt;</a>';

    $alt = '&lt;&nbsp;'.$lang_common['Image link'].'&nbsp;: '.$alt.'&nbsp;&gt;';

    if ($is_signature && $pun_user['show_img_sig'] != '0')
    {
        $img_tag = '<img class="sigimage'.$img_class.'" src="'.$url.$title.'" alt="'.$alt.'" />';
    }
    elseif (!$is_signature && $pun_user['show_img'] != '0')
    {
        $img_tag = '<img class="postimg'.$img_class.'" src="'.$url.$title.'" alt="'.$alt.'" />';
    }
    
    if (preg_match('#(^|\s)(\d+)($|\s)#s', $align, $matches))
    {
        $img_tag = '<a href="/images/'.$matches[2].'">'.$img_tag.'</a>';
    }
    
    if ($centered)
    {
        $img_tag = '</p><div style="text-align: center;">'.$img_tag.'</div><p>';
    }

    return $img_tag;
}


//
// Turns a [img] tag from camptocamp.org image page into an <img> tag or a <a href...> tag
//
function handle_c2c_img_tag($url, $ext, $align, $is_signature = false, $alt=null)
{
    global $lang_common, $pun_config, $pun_user;

    $options = explode(' ', $align);
    $centered = false;
    
    if (in_array('left', $options))
    {
        $img_class = ' embedded_left';
    }
    elseif (in_array('right', $options))
    {
        $img_class = ' embedded_right';
    }
    elseif (in_array('inline', $options))
    {
        $img_class = ' embedded_inline';
    }
    elseif (in_array('inline_left', $options))
    {
        $img_class = ' embedded_inline_left';
    }
    elseif (in_array('inline_right', $options))
    {
        $img_class = ' embedded_inline_right';
    }
    elseif (in_array('center', $options))
    {
        $img_class = ' embedded_center';
        $centered = true;
    }
    else
    {
        $img_class = '';
    }
        
//    $base_url_tmp = parse_url($pun_config['o_base_url']);
//    $base_url = $base_url_tmp['sheme'].'://'.$base_url_tmp['host'].'/uploads/images/';
    $base_url = PUN_STATIC_URL.'/uploads/images/';
    
    if (in_array('big', $options))
    {
        $size = 'BI';
    }
    elseif (in_array('small', $options))
    {
        $size = 'SI';
    }
    else
    {
        $size = 'MI';
    }
    $small_img_url = $base_url.$url.$size.'.'.$ext;
    
    
    if (preg_match('#(^|\s)(\d+)($|\s)#s', $align, $matches))
    {
        $img_url = '/images/'.$matches[2];
        $alt_url = $img_url;
    }
    else
    {
        $img_url = $base_url.$url.'.'.$ext;
        $alt_url = $url.'.'.$ext;
    }
    
    if ($alt == null)
    {
        $alt = $alt_url;
        $title='';
        $image_text = $lang_common['Image link'];
    }
    else
    {
        $title = '" title="'.$alt;
        $image_text = $lang_common['Image link'].'&nbsp;: '.$alt;
    }

    $img_tag = '<a href="'.$img_url.'">&lt;&nbsp;'.$image_text.'&nbsp;&gt;</a>';

    $alt = '&lt;&nbsp;'.$lang_common['Image link'].'&nbsp;: '.$alt.'&nbsp;&gt;';

    if ($is_signature && $pun_user['show_img_sig'] != '0')
    {
        $img_tag = '<a href="'.$img_url.'"><img class="sigimage'.$img_class.'" src="'.$small_img_url.$title.'" alt="'.$alt.'" /></a>';
    }
    elseif (!$is_signature && $pun_user['show_img'] != '0')
    {
        $img_tag = '<a href="'.$img_url.'"><img class="postimg'.$img_class.'" src="'.$small_img_url.$title.'" alt="'.$alt.'" /></a>';
    }
    
    if ($centered)
    {
        $img_tag = '</p><div style="text-align: center;">'.$img_tag.'</div><p>';
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
    $string = str_split($string, 7);
    // prevent &sthg; characters to be cut (invalid xhtml)
    foreach ($string as $key => $part)
    {
        $start = strpos($part, '&');
        $end = strpos($part, ';');
        if ($start !== false)
        {
             $end = strpos($part, ';', $start);
             if ($end === false)
             {
                 $end = strpos($string[$key+1], ';');
                 if ($end !== false)
                 {
                     $string[$key] = $string[$key] . substr($string[$key+1], 0, $end + 1);
                     $string[$key+1] = substr($string[$key+1], $end + 1);
                 }
             }
        }
    }
    foreach ($string as $part)
    {
        $s = array('<', '>');
        $r = array('%3C', '%3E');
        $part = str_replace($s, $r, addslashes($part));
        $js .= "document.write(unescape('$part'));";
    }

    return '<script type="text/javascript">' . $js . '</script>';
}


//
// Convert BBCodes to their HTML equivalent
//
function do_bbcode($text, $is_signature = false, $post_list = array())
{
    global $lang_common, $lang_topic, $pun_user, $pun_config, $showed_post_list;

    $showed_post_list = $post_list;
    $show_video = $is_signature ? 'false' : 'true';
    
    if (strpos($text, 'quote') !== false)
    {
        $text = str_replace('[quote]', '</p><blockquote><div class="incqbox"><p>', $text);
        $text = preg_replace('#\[quote=(&quot;|"|&\#039;|\'|)(.*?)\\1\|?((?<=\|)\s*([0-9]+|[^\]]+)|)\s*\]#se', 'handle_quote_tag(\'$2\', \'$3\')', $text);
        $text = preg_replace('#\[\/quote\]\s?#', '</p></div></blockquote><p>', $text);
    }

    $pattern = array('#\[b\](.*?)\[/b\]#s',
                     '#\[i\](.*?)\[/i\]#s',
                     '#\[u\](.*?)\[/u\]#s',
                     '#\[s\](.*?)\[/s\]#s',
                     '#\[q\](.*?)\[/q\]#s',
                     '#\[c\](.*?)\[/c\]#s',
                     '#\[url\]((?:[^\[<]|\[\])*?)\[/url\]#e',
                     '#\[url=((?:[^\[<]|\[\])*?)\](.*?)\[/url\]#e',
                     '#\[center\]\s*(.*?)\s*\[/center\]\s?#s',
                     '#\[right\]\s*(.*?)\s*\[/right\]\s?#s',
                     '#\[justify\]\s*(.*?)\s*\[/justify\]\s?#s',
                     '#\[email\]([^\[<]*?)\[/email\]#e',
                     '#\[email=([^\[<]*?)\](.*?)\[/email\]#e',
                     '#\[spoiler(=([^\[]*?)|)\](.*?)\s*\[/spoiler\]\s?#s',
                     '#\[acronym=([^\[]*?)\](.*?)\[/acronym\]#',
                     '#\[---+(.*?)\]#s',
                     '#\[picto=?\s*([\w]+)\s*\/\]#s',
                     '#\s?\[p\]\s?#s');

    $replace = array('<strong>$1</strong>',
                     '<em>$1</em>',
                     '<span class="bbu">$1</span>',
                     '<del>$1</del>',
                     '<q>$1</q>',
                     '<code>$1</code>',
                     'handle_url_tag(\'$1\', \'\', ' . $show_video . ')',
                     'handle_url_tag(\'$1\', \'$2\', ' . $show_video . ')',
                     '</p><div style="text-align: center;"><p>$1</p></div><p>',
                     '</p><div style="text-align: right;"><p>$1</p></div><p>',
                     '</p><div style="text-align: justify;"><p>$1</p></div><p>',
                     'handle_email_tag(\'$1\')',
                     'handle_email_tag(\'$1\', \'$2\')',
                     '</p><blockquote><div class="incqbox" onclick="C2C.toggle_spoiler(this)"><h4>$2 ('.$lang_topic['Click to open'].')</h4><div style="display:none;"><p>$3</p></div></div></blockquote><p>',
                     '<acronym title="$1">$2</acronym>',
                     '</p><hr /><p>',
                     '<span class="picto $1"> </span>',
                     '</p><div class="clearer"></div><p>');

    if (!$is_signature)
    {
        $pattern[] = '#\[colou?r=([a-zA-Z]{3,20}|\#?[0-9a-fA-F]{6})](.*?)\[/colou?r\]#s';
        $replace[] = '<span style="color: $1">$2</span>';
    }
    
    if ((!$is_signature && $pun_config['p_message_img_tag'] == '1') || ($is_signature && $pun_config['p_sig_img_tag'] == '1'))
    {
        $pattern[] = '#\[img=((ht|f)tps?://|/static/|/uploads/)([^\s"\[<|]*?)((\||\s)([\w\s]+))?\](.*?)\[/img\]\n?#ise';
        $pattern[] = '#\[img(=([^\[<|]+))?((\||\s)([\w\s]+))?\]((ht|f)tps?://|/static/|/uploads/)([^\s<"]*?)\[/img\]\n?#ise';
        $pattern[] = '#\[img=([0-9_]+)\.(\w+)((\||\s)([\w\s]+))?\](.*?)\[/img\]\n?#ise';
        $pattern[] = '#\[img(=([^\[<|]+))?((\||\s)([\w\s]+))?\]([0-9_]+)\.(\w+)\[/img\]\n?#ise';
        
        $is_sig_str = $is_signature ? 'true' : 'false';
        
        $replace[] = 'handle_img_tag(\'$1$3\', \'$6\', '.$is_sig_str.', \'$7\')';
        $replace[] = 'handle_img_tag(\'$6$8\', \'$5\', '.$is_sig_str.', \'$2\')';
        $replace[] = 'handle_c2c_img_tag(\'$1\', \'$2\', \'$5\', '.$is_sig_str.', \'$6\')';
        $replace[] = 'handle_c2c_img_tag(\'$6\', \'$7\', \'$5\', '.$is_sig_str.', \'$2\')';
    }

    // This thing takes a while! :)
    $text = preg_replace($pattern, $replace, $text);

    return $text;
}


//
// Make hyperlinks clickable
//
function do_clickable($text)
{
    global $pun_config;
    
    $text = ' '.$text;

    $pattern[] ='#((?<=[\s\(\)\>\]:.;,])(?<!\[url\]|\[img\]|\[video\]|,\d{3}\])|[\<\[]+)(https?|ftp|news){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/((?![,.:;](\s|\Z))[^"\s\(\)<\>\[\]]|[\>\<]\d)*)?)[\>\]]*#i';
    $pattern[] ='#((?<=[\s\(\)\>\]:;,])(?<!\[url\]|\[img\]|\[video\]|,\d{3}\])|[\<\[]+)(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/((?![,.:;](\s|\Z))[^"\s\(\)<\>\[\]]|[\>\<]\d)*)?)[\>\]]*#i';
    $pattern[] = '/((?<=[\s\(\)\>\]:.;,])(?<!\[url\]|\[img\]|\[video\]|,\d{3}\])|[\<\[]+)(#([fpt])\d+\+?)[\>\]]*/';
    $pattern[] = '#((?<=[\s\(\)\>\]:.;,])(?<!\[url\]|\[img\]|\[video\]|,\d{3}\])|[\<]+)/*(((outings|routes|summits|sites|huts|parkings|images|articles|areas|books|products|maps|users|portals|forums|tools)/|map\?)((?![,.:;\>\<](\s|\Z))[^"\s\(\)<\>\[\]]|[\>\<]\d)*)[/\>\]]*#';
    $pattern[] = '#((?<=[\s\(\)\>\]:.;,])(?<!\[url\]|\[img\]|\[video\]|,\d{3}\])|[\<]+)/((outings|routes|summits|sites|huts|parkings|images|articles|areas|books|products|maps?|users|portals|forums|tools)(?=[,.:;\>\<"\s\(\)\[\]]|\Z))[\>\]]*#';
    $pattern[] ='#((?<=["\'\s\(\)\>\]:;,])(?<!\[email\])|[\<\[]+)(([\w\-]+\.)*[\w\-]+)(@|\[~\]|\(%\))(([\w\-]+\.)+[\w]+([^"\'\s\(\)<\>\[\]:.;,]*)?)[\>\]]*#i';

    if ($pun_config['p_message_bbcode'] == '1')
    {
        $replace[] = '[url]$2://$3[/url]';
        $replace[] = '[url]$2.$3[/url]';
        $replace[] = '[url]$2[/url]';
        $replace[] = '[url]/$2[/url]';
        $replace[] = '[url]/$2[/url]';
        $replace[] = '[email]$2@$5[/email]';
    }
    else
    {
        $replace[] = '$2://$3 ';
        $replace[] = '$2.$3 ';
        $replace[] = '/$2 ';
        $replace[] = '$2 ';
        $replace[] = '$2/ ';
        $replace[] = '$2(%)$4 ';
    }
    
    $text = preg_replace($pattern, $replace, $text);
    
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
        $text = preg_replace("#(?<=.\W|\W.|^\W)".preg_quote($smiley_text[$i], '#')."(?=.\W|\W.|\W$)#m", '$1<img src="'.PUN_STATIC_URL.'/static/images/forums/smilies/'.$smiley_img[$i].'" width="15" height="15" alt="'.$smiley_text[$i].'" />$2', $text);

    return substr($text, 1, -1);
}


//
// Convert video tags to HTML
//
function do_video($text)
{
    if (stripos($text, '[/video]') !== FALSE)
    {
        $mobile_version = c2cTools::mobileVersion();
        $width = $mobile_version ? 310 : 400;
        $height = $mobile_version ? 232 : 300;

        // first replace all [video] by [video $width,$height]
        // for mobile version, we force the dimensions
        $text = $mobile_version ? preg_replace('#\[video( ([0-9]{2,4}),([0-9]{2,4}))?\]#i', "[video $width,$height]", $text)
                                : preg_replace('#\[video\]#i', "[video $width,$height]", $text);
        $patterns = array(
            // youtube http://www.youtube.com/watch?v=3xMk3RNSbcc(&something)
            '#\[video( ([0-9]{2,4}),([0-9]{2,4}))?\]https?:\/\/(www\.)?youtube\.com/watch\?([=&\w]+&)?v=([-\w]+)(&.+)?(\#.*)?\[/video\]#isU',
            // youtube short links http://youtu.be/3xMk3RNSbcc
            '#\[video( ([0-9]{2,4}),([0-9]{2,4}))?\]https?:\/\/(www\.)?youtu\.be/([-\w]+)(\#.*)?\[/video\]#isU',
            // dailymotion http://www.dailymotion.com/video/x28z33_chinese-man-records-skank-in-the-ai_music
            '#\[video( ([0-9]{2,4}),([0-9]{2,4}))?\]https?://www\.dailymotion\.com/video/([\da-zA-Z]+)_[-&;\w]+(\#.*)?\[/video\]#isU',
            // googlevideo http://video.google.com/videoplay?docid=3340274697167011147#
            '#\[video( ([0-9]{2,4}),([0-9]{2,4}))?\]https?://video\.google\.com/videoplay\?docid=(\d+)(\#.*)?\[/video\]#isU',
            // vimeo http://vimeo.com/8654134
            '#\[video( ([0-9]{2,4}),([0-9]{2,4}))?\]https?://(www\.)?vimeo\.com/(\d+)(\#.*)?\[/video\]#isU',
            // metacafe http://www.metacafe.com/watch/4003782/best_shot_of_movie_troy(/|.swf)
            '#\[video( ([0-9]{2,4}),([0-9]{2,4}))?\]https?://www\.metacafe\.com/watch/(\d+/[_a-z]+)(/|\.swf)(\#.*)?\[/video\]#isU',
        );

        $replacements = array(
            // youtube - See http://apiblog.youtube.com/2010/07/new-way-to-embed-youtube-videos.html
            '<iframe class="video youtube-player" width="$2" height="$3" src="//www.youtube.com/embed/$6"></iframe>',
            '<iframe class="video youtube-player" width="$2" height="$3" src="//www.youtube.com/embed/$5"></iframe>',
            // dailymotion
            '<iframe class="video" width="$2" height="$3" src="//www.dailymotion.com/embed/video/$4?theme=none&amp;wmode=transparent"></iframe>',
            // googlevideo
            '<object class="video" width="$2" height="$3" data="http://video.google.com/googleplayer.swf?docId=$4"><param name="movie" value="http://video.google.com/googleplayer.swf?docId=$4" /><embed src="http://video.google.com/googleplayer.swf?docId=$4" type="application/x-shockwave-flash" width="$2" height="$3" /></object>',
            // vimeo
            '<iframe class="video" src="//player.vimeo.com/video/$5?title=0&amp;byline=0&amp;portrait=0&amp;color=ff9933" width="$2" height="$3"></iframe>',
            // metacafe
            '<iframe class="video" src="http://www.metacafe.com/embed/$4/" width="$2" height="$3" allowFullScreen frameborder=0></iframe>',
        );

        $text = preg_replace($patterns, $replacements, $text);
    }
    return $text;
}



//
// Parse message text
//
function parse_message($text, $hide_smilies, $post_list = array())
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
            $outside = array_map('do_clickable', $outside);
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
            $text = do_clickable($text);
        }
        
        // Convert applicable characters to HTML entities
        $text = pun_htmlspecialchars($text);
    }

    if ($pun_config['o_smilies'] == '1' && $pun_user['show_smilies'] == '1' && $hide_smilies == '0')
        $text = do_smilies($text);

    if ($pun_config['p_message_bbcode'] == '1' && strpos($text, '[') !== false && strpos($text, ']') !== false)
    {
        $text = do_bbcode($text, false, $post_list);
        $text = do_video($text);
    }

    // Deal with newlines, tabs and multiple spaces
    $pattern = array("\n", "\t", '    ', '  ', '<p><br />');
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
        
    // Add new line in the HTML code
    $pattern = array('<br />', '<p>', '</p>', '<pre>', '</pre>', '<ul', '<ol', '<li>', '</ul>', '</ol>');
    $replace = array("<br />\n", "<p>\n", "\n</p>", "<pre>\n", "\n</pre>", "\n<ul", "\n<ol", "\n<li>", "\n</ul>\n", "\n</ol>\n");
    $text = str_replace($pattern, $replace, $text);

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
        $text = do_clickable($text);

    $text = pun_htmlspecialchars($text);

    if ($pun_config['o_smilies_sig'] == '1' && $pun_user['show_smilies'] != '0')
        $text = do_smilies($text);

    if ($pun_config['p_sig_bbcode'] == '1' && strpos($text, '[') !== false && strpos($text, ']') !== false)
    {
        $text = do_bbcode($text, true);
    }

    // Deal with newlines, tabs and multiple spaces
    $pattern = array("\n", "\t", '    ', '  ', '<p><br />');
    $replace = array('<br />', '&nbsp; &nbsp; ', '&nbsp; ', ' &nbsp;', '<p>');
    $text = str_replace($pattern, $replace, $text);

    return $text;
}
