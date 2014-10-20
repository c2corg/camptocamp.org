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
    // Convert \r\n and \r to \n
    //
    public static function parse_linebreaks($str)
    {
        return str_replace("\r", "\n", str_replace("\r\n", "\n", $str));
    }
    
    
    //
    // Make sure all BBCodes are lower case and do a little cleanup
    //
    public static function preparse_bbcode($text, &$errors)
    {
        // Change all simple BBCodes to lower case
        $a = array('[B]', '[I]', '[U]', '[S]', '[Q]', '[C]', '[P]', '[/B]', '[/I]', '[/U]', '[/S]', '[/Q]', '[/C]');
        $b = array('[b]', '[i]', '[u]', '[s]', '[q]', '[c]', '[p]', '[/b]', '[/i]', '[/u]', '[/s]', '[/q]', '[/c]');
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
                    '#\[/colou?r\]#i',
                    '#\s?\[(cent(er|re|ré)|<>)\]\s*#i',
                    '#\s*\[/(cent(er|re|ré)|<>)\]\s?#i',
                    '#\s?\[(right|rigth|ritgh|rithg|droite?|>)\]\s*#i',
                    '#\s*\[/(right|rigth|ritgh|rithg|droite?|>)\]\s?#i',
                    '#\s?\[(justif(y|ie|ié|)|=)\]\s*#i',
                    '#\s*\[/(justif(y|ie|ié|)|=)\]\s?#i',
                    '#\[p\]\s?#s',
                    '#\s?\[quote=(&quot;|"|\'|)(.*?)\\1\]#i',
                    '#\s?\[quote(=\]|\])#i',
                    '#\[/quote\]\s?#i',
                    '#\s?\[code\]\s*(.*?)\s*\[/code\]\s?#is'
                );

        $b = array( '[url=$2]',
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
                    "\n[center]\n",
                    "\n[/center]\n",
                    "\n[right]\n",
                    "\n[/right]\n",
                    "\n[justify]\n",
                    "\n[/justify]\n",
                    "[p]\n",
                    "\n[quote=$1$2$1]\n",
                    "\n[quote]\n",
                    "\n[/quote]\n",
                    "\n[code]\n$1\n[/code]\n"
                );
    
        $a[] = '#(?<!^|\n)([ \t]*)(\[(center|right|justify|quote|code|spoiler|video))#i';
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
    public static function handle_url_tag($url, $link = '', $viewer = true, $target = '')
    {
        $hreflang = '';
        $rel = '';
        
        $url = str_replace(array('\'', '`', '"'), array('', '', ''), $url);
        $full_url = str_replace(array(' '), array('%20'), $url);
        $url = preg_replace('#^(ht+ps?|ftp|news)?:*/*((www|ftp)(\.|$))?\.?#i', '', $url);
        if ($url == '')
        {
            return $link;
        }
        
        $full_url = preg_replace('#^(ht+ps?)?:*/*w*m*\.*camptocamp\.org/?(.*)#', '/${2}', $full_url);
        $is_internal_url = (strpos("#/", $full_url[0]) !== false);
            
        if ($empty_link = (empty($link) || $link == $url))
        {
            if ($full_url == '/')
            {
                $link = $url;
            }
            elseif ($is_internal_url)
            {
                $link = $full_url;
            }
            else
            {
                $link = $url;
            }
        }
        
        $is_forum_url = false;
        
        if ($full_url == '' && $link == '')
            return '';
        elseif (preg_match('#(?<=[^\w/]|^)/*forums/view(topic|forum).php\?p?id=\d+#i', $full_url, $bah)) 	// Else if it is a forum url
        {
            $a = array( '%(?<=[^\w/]|^)/*forums/viewforum.php\?id=(\d+)(&p=\d+)?%i',
                        '%(?<=[^\w/]|^)/*forums/viewtopic.php\?id=(\d+)&action=new%i',
                        '%(?<=[^\w/]|^)/*forums/viewtopic.php\?id=(\d+)(&p=\d+)?%i',
                        '%(?<=[^\w/]|^)/*forums/viewtopic.php\?pid=\d+#p(\d+)%i',
                        '%(?<=[^\w/]|^)/*forums/viewtopic.php\?pid=(\d+)%i'
                      );

            $b = array(	'#f$1',
                        '#t$1+',
                        '#t$1',
                        '#p$1',
                        '#p$1'
                      );
            $full_url = preg_replace($a, $b, $full_url);
            $is_forum_url = true;
        }
        else if (strpos($full_url, 'www.') === 0)            // If it starts with www, we add http://
            $full_url = 'http://'.$full_url;
        else if (strpos($full_url, 'ftp.') === 0)    // Else if it starts with ftp, we add ftp://
            $full_url = 'ftp://'.$full_url;
        else if (!$is_internal_url && !preg_match('#^([a-z0-9]{3,6}):/+#', $full_url, $bah))     // Else if it doesn't start with abcdef:// nor #, we add http://
            $full_url = 'http://'.$full_url;
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

        if (!empty($target)) $target = ' target="' . $target . '"';

        // external link TODO use objects instead of iframe (but ie doesn't like it with external html...)
        if (preg_match('/\.(ppt|pdf)$/i', $full_url) && $viewer && !c2cTools::mobileVersion())
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
            
            // si doc collaboratif, le lien est tronqué avant la langue
        //    if (preg_match('#^(/(routes|summits|sites|huts|parkings|images|articles|areas|books|products|maps|users|portals)/[\d]+)/\w+(/[\w-]+)?#i', $full_url, $params))
        //    {
        //        $full_url = $params[1];
        //    }
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

        return '<a' . $class . ' href="' . $full_url . '"' . $hreflang . $target . $rel . '>' . $link . '</a>' . $suffix;
    }
    
    public static function handle_static_img_tag($filename, $extension, $align, $legend = '')
    {
        $options = explode(' ', $align);
        $centered = false;
        
        if (in_array('left', $options))
        {
            $img_class = 'embedded_left';
        }
        elseif (in_array('right', $options))
        {
            $img_class = 'embedded_right';
        }
        elseif (in_array('inline', $options))
        {
            $img_class = 'embedded_inline';
        }
        elseif (in_array('inline_left', $options))
        {
            $img_class = 'embedded_inline_left';
        }
        elseif (in_array('inline_right', $options))
        {
            $img_class = 'embedded_inline_right';
        }
        elseif (in_array('inline_0', $options))
        {
            $img_class = 'embedded_inline_0';
        }
        elseif (in_array('center', $options))
        {
            $img_class = 'embedded_center';
            $centered = true;
        }
        else
        {
            $img_class = '';
        }
        if (!empty($img_class))
        {
            $img_class = 'class="' . $img_class . '" ';
        }

        $static_base_url = sfConfig::get('app_static_url') . '/static/images';
        $image_tag = sprintf('<img ' . 
                             $img_class.'src="%s/%s" alt="%s"%s />', 
                             $static_base_url, 
                             $filename . '.' . $extension, 
                             $filename . '.' . $extension, 
                             (empty($legend) ? '' : ' title="' . $legend . '"'));
        if ($centered)
        {
            $image_tag = '</p><figure class="center">'.$image_tag.'</figure><p>';
        }

        return $image_tag;
    }

    public static function handle_img_id_tag($image_id, $align, $legend = '', $images = null, $filter_image_type = true)
    {
        if ($images == null) return '';

        $options = explode(' ', $align);
        $img_class = array();
        
        $centered = false;
        $inline = false;
        if (in_array('left', $options))
        {
            $img_class[] = 'embedded_left';
        }
        elseif (in_array('right', $options))
        {
            $img_class[] = 'embedded_right';
        }
        elseif (in_array('inline', $options))
        {
            $img_class[] = 'embedded_inline';
            $inline = true;
        }
        elseif (in_array('inline_left', $options))
        {
            $img_class[] = 'embedded_inline_left';
        }
        elseif (in_array('inline_right', $options))
        {
            $img_class[] = 'embedded_inline_right';
        }
        elseif (in_array('center', $options))
        {
            $img_class[] = 'embedded_center';
            $centered = true;
        }
        
        $show_legend = true;
        if (in_array('no_legend', $options))
        {
            $show_legend = false;
            $legend = self::do_spaces($legend, false);
        }
        else
        {
            $img_class[] = 'img_box';
        }
        
        if ($show_legend && in_array('no_border', $options))
        {
            $img_class[] = 'no_border';
        }
        
        // big images are not used in mobile version (replaced by medium version)
        if (in_array('big', $options) && !c2cTools::mobileVersion())
        {
            $size = 'BI.';
        }
        elseif (in_array('small', $options))
        {
            $size = 'SI.';
        }
        else
        {
            $size = 'MI.';
        }

        $image = null;
        foreach ($images as $image_temp)
        {
            if ($image_temp['id'] == $image_id)
            {
                $image = $image_temp;
            }
        }
        $error_image = is_null($image);
        
        // Error image
        if ($error_image)
        {
            if (!$show_legend)
            {
                $show_legend = true;
                $img_class[] = 'img_box';
            }
            $img_class[] = 'img_error';
            
            $path = '/static/images/picto';
            $filename = 'warning';
            $extension = 'png';
            
            $short_title = __('Image could not be loaded');
            $legend = __('Image could not be loaded long') . '<br />' . link_to(__('View image details'), '@document_by_id?module=images&id='.$image_id);
        }
        else
        {
            if (empty($legend))
            {
                $legend = $image['name'];
            }
            
            $path = '/uploads/images';
            list($filename, $extension) = explode('.', $image['filename']);
            
            $alt = $filename . '.' . $extension;
            $title = self::do_spaces($legend, false);
            if (!empty($title))
            {
                $title = ' title="' . $title . '"';
            }
            
            // Warning image - TODO to be removed after transition period, use error img instead
            if ($filter_image_type && $image['image_type'] == 2)
            {
                if (!$show_legend)
                {
                    $show_legend = true;
                    $img_class[] = 'img_box';
                }
                $img_class[] = 'img_error';
                $img_class[] = 'img_warning';
                
                $legend = __('Wrong image type');
            }
        }
        
        $path = sfConfig::get('app_static_url') . $path;

        $img_class = implode(' ', $img_class);
        if (!empty($img_class))
        {
            $img_class = ' class="' . $img_class . '"';
        }

        if ($error_image)
        {
            $image_tag = sprintf('<img src="%s/%s" alt="%s" title="%s" />',
                                 $path,
                                 $filename . '.' . $extension,
                                 $short_title,
                                 $short_title);
        }
        else
        {
            $image_tag = sprintf('<a data-lightbox="embedded_images" id="lightbox_%s_%s_embedded" class="view_big" href="%s/%s"%s><img%s src="%s/%s" alt="%s" itemprop="image" /></a>',
                                 $image['id'],
                                 $image['image_type'],
                                 $path,
                                 $filename . 'BI.' . $extension,
                                 strip_tags($title),
                                 ($show_legend ? '' : $img_class ),
                                 $path,
                                 $filename . $size . $extension,
                                 $alt); // alt todo use title if available.....
        }
        
        if ($show_legend)
        {
              $image_tag = '<figure' . $img_class . '>' . $image_tag;
              if (strpos($img_class, 'img_error') === false)
              {
                  $image_tag = $image_tag
                      .link_to(__('View image details'),
                              '@document_by_id_lang_slug?module=images&id=' . $image['id'] . '&lang=' . $image['culture'] . '&slug=' . formate_slug($image['search_name']),
                              array('class' => 'picto_images view_details',
                                    'title'   => __('View image details')));
              }
              // note: it is safe (at least should be :)) to translate \" to " here
              // since we only get them because of e modifier for img pre_replace
              // (elsewhere it is translated to html special chars)
              // FIXME preg_replace with e delimiter is kinda deprecated,we should rather use preg_replace_callback
              $image_tag = $image_tag . '<figcaption>' . stripslashes($legend) . '</figcaption></figure>';
        }

        if ($centered)
        {
            $image_tag = '<div class="center">'.$image_tag.'</div>';
        }
        else if (c2cTools::mobileVersion()) /* needed in order to center images for smartphones in portrait mode */
        {
            $image_tag = '<div class="img_mobile">'.$image_tag.'</div>';
        }

        if (!$inline)
        {
            $image_tag = '</p>'.$image_tag.'<p>';
        }
        
        return $image_tag;
    }

    /**
     * Email obfuscation against spam
     */
    public static function handle_email_tag($email, $label = NULL)
    {
        if (empty($email)) return '';
        if (empty($label)) $label = $email;

        $string = sprintf('<a href="mailto:%s">%s</a>', $email, $label);

        // no obfuscation if ajax request
        if (sfContext::getInstance()->getRequest()->isXmlHttpRequest())
        {
            return $string;
        }

        $js = '';
        $string = str_split($string, 7);
        // prevent &sthg; characters to be cut (invalid xhtml)
        foreach ($string as $key => $part)
        {
            $start = strpos($part, '&');
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
    
    public static function handle_col_tag($text, $options)
    {
        if (empty($text)) return '';

        $has_width = preg_match('#(\d+)#', $options, $width);
        $options = explode(' ', $options);
        
        $class = 'col';
        if (in_array('left', $options))
        {
            $class .= '_left';
        }
        elseif (in_array('right', $options))
        {
            $class .= '_right';
        }
        
        if ($has_width)
        {
            $class .= ' col_' . $width[1];
        }
        else
        {
            $class .= ' col_50';
        }
        
        $alone = in_array('alone', $options);
        if ($alone)
        {
            $class .= ' alone';
        }
        if (in_array('top', $options))
        {
            $class .= ' top';
        }

        $result = '<div class="' . $class . '"><p>' . stripslashes($text) . '</p></div>';
        if (!$alone)
        {
            $result = '</p>' . $result . '<p>';
        }
        return $result;
    }
    
    /**
     * Convert BBCodes to their HTML equivalent
     */
    public static function do_bbcode($text, $extended, $viewer = true, $force_external_links = false)
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
                         '#\[sup\](.*?)\[/sup\]#s',
                         '#\[ind\](.*?)\[/ind\]#s',
                         '#\[url\]((?:[^\[]|\[\])*?)\[/url\]#e',
                         '#\[url=((?:[^\[]|\[\])*?)\](.*?)\[/url\]#e',
                         '#\[email\]([^\[]*?)\[/email\]#e',
                         '#\[email=([^\[]*?)\](.*?)\[/email\]#e',
                         '#\[acr(onym)?=([^\[]*?)\](.*?)\[/acr(onym)?\]#',
                         '#\[colou?r=([a-zA-Z]{3,20}|\#?[0-9a-fA-F]{6})](.*?)\[/colou?r\]#s',
                         '#\[picto=?\s*([\w]+)\s*\/\]#s',
                         '#\s?\[p\]\s?#s',
                         '#\[center\]\s*(.*?)\s*\[/center\]\s?#s',
                         '#\[right\]\s*(.*?)\s*\[/right\]\s?#s',
                         '#\[left\]\s*(.*?)\s*\[/left\]\s?#s',
                         '#\[justify\]\s*(.*?)\s*\[/justify\]\s?#s',
                         '#\[abs(tract)?\]\s*(.*?)\s*\[/abs(tract)?\]\s{0,2}#s',
                         '#\[imp(ortant)?\]\s*(.*?)\s*\[/imp(ortant)?\]\s?#s',
                         '#\[warn(ing)?\]\s*(.*?)\s*\[/warn(ing)?\]\s?#s',
                         '#\s?\[col(\s+)([\w\s]*)\]\s*(.*?)\s*\[/col\]\s?#se'
        );

        $target = $force_external_links ? '_blank' : '';
        $viewer = $viewer ? 'true' : 'false';
        $replace = array('<strong>$1</strong>',
                         '<em>$1</em>',
                         '<span style="text-decoration: underline;">$1</span>',
                         '<del>$1</del>',
                         '<q>$1</q>',
                         '<code>$1</code>',
                         '<sup>$1</sup>',
                         '<sub>$1</sub>',
                         'self::handle_url_tag(\'$1\', \'\', ' . $viewer . ', \'' . $target . '\')',
                         'self::handle_url_tag(\'$1\', \'$2\', ' . $viewer . ', \'' . $target . '\')',
                         'self::handle_email_tag(\'$1\')',
                         'self::handle_email_tag(\'$1\', \'$2\')',
                         '<acronym title="$2">$3</acronym>',
                         '<span style="color: $1">$2</span>',
                         '<span class="picto $1"> </span>'
                        );
        if ($extended)
        {
            $replace[] = '</p><div class="clearer"></div><p>';
            $replace[] = '</p><div style="text-align: center;"><p>$1</p></div><p>';
            $replace[] = '</p><div style="text-align: right;"><p>$1</p></div><p>';
            $replace[] = '</p><div style="text-align: left;"><p>$1</p></div><p>';
            $replace[] = '</p><div style="text-align: justify;"><p>$1</p></div><p>';
            $replace[] = '</p><p class="abstract">$2</p><p>';
            $replace[] = '</p><p class="important_message">$2</p><p>';
            $replace[] = '</p><p class="warning_message">$2</p><p>';
            $replace[] = 'self::handle_col_tag(\'$3\', \'$2\')';
        }
        else
        {
            $replace[] = "\n";
            $replace[] = '$1';
            $replace[] = '$1';
            $replace[] = '$1';
            $replace[] = '$1';
            $replace[] = '$2';
            $replace[] = '$2';
            $replace[] = '$2';
            $replace[] = '$3';
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

        $pattern[] ='#\[\[http://[\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?/([^\|\]]*)#i';
        $pattern[] ='#\[url=\]#i';
        $pattern[] ='#\[url=((?:[^\[]|\[\])*?)\]((https?|ftp|news)?://(www)?|www|ftp)\.#i';
        $pattern[] ='#((?<=[\s\(\)\>\]:.;,])(?<!\[url\]|\[video\]|\d{3}\])|[\<\[]+)(https?|ftp|news){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/((?![,.;](\s|\Z))[^"\s\(\)<\>\[\]]|[\>\<]\d)*)?)[\>\]]*#i';
        $pattern[] ='#((?<=[\s\(\)\>\]:;,])(?<!\[url\]|\[video\]|\d{3}\])|[\<\[]+)(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/((?![,.;](\s|\Z))[^"\s\(\)<\>\[\]]|[\>\<]\d)*)?)[\>\]]*#i';
        $pattern[] = '/((?<=[\s\(\)\>\]:.;,])(?<!\[url\]|\[video\]|\d{3}\])|[\<\[]+)(#([fpt])\d+\+?)[\>\]]*/';
        $pattern[] = '#((?<=[\s\(\)\>\]:.;,])(?<!\[url\]|\[video\]|\d{3}\])|[\<]+)/*(((outings|routes|summits|sites|huts|parkings|images|articles|areas|books|products|maps|users|portals|forums|tools)/|map\?)((?![,.:;\>\<](\s|\Z))[^"\s\(\)<\>\[\]]|[\>\<]\d)*)[/\>\]]*#';
        $pattern[] = '#((?<=[\s\(\)\>\]:.;,])(?<!\[url\]|\[video\]|\d{3}\])|[\<]+)/((outings|routes|summits|sites|huts|parkings|images|articles|areas|books|products|maps?|users|portals|forums|tools)(?=[,.:;\>\<"\s\(\)\[\]]|\Z))[\>\]]*#';
        $pattern[] ='#((?<=["\'\s\(\)\>\]:;,])(?<!\[email\])|[\<\[]+)(([\w\-]+\.)*[\w\-]+)@(([\w\-]+\.)+[\w]+([^"\'\s\(\)<\>\[\]:.;,]*)?)[\>\]]*#i';

        $replace[] = '[[$3';
        $replace[] = '[url]';
        $replace[] = '[url=$1]';
        $replace[] = '[url]$2://$3[/url]';
        $replace[] = '[url]$2.$3[/url]';
        $replace[] = '[url]$2[/url]';
        $replace[] = '[url]/$2[/url]';
        $replace[] = '[url]/$2[/url]';
        $replace[] = '[email]$2@$4[/email]';
        
        $text = preg_replace($pattern, $replace, $text);
        
        return substr($text, 1);
    }
    
    
    //
    // Convert sub-title
    //
    public static function do_headers($text) {
        global $header_level, $toc_level, $toc_visible_level, $toc_level_max, $toc_enable, $toc;

        $header_level = 0;
        $toc_level = 0;
        $toc_visible_level = 0;
        $toc_level_max = 5;

        if (preg_match('#\[toc[ ]*(\d*)[ ]*(right|left)?\]#i', $text, $matches))
        {
            $toc_enable = true;
            if (!empty($matches[1]))
            {
                $toc_level_max = $matches[1];
            }
            $toc_position = ' embedded_left';
            $add_clearer = true;
            if (!empty($matches[2]))
            {
                if ($matches[2] == 'right')
                {
                    $toc_position = ' embedded_right';
                }
                $add_clearer = false;
            }
            $toc = '</p><table class="toc' . $toc_position . '" id="toc"><tbody><tr><td><div id="toctitle"><h2>' . __('Summary') . '</h2></div><ul class="toc">';
        }
        else
        {
            $toc_enable = false;
        }

        /* Setext-style headers:
              Header 2
              ========
          
              Header 3
              --------
        */
        $text = preg_replace_callback(
            '{
                (\n{0,2})                           # $1 = header at start of text
                (^.+?)                              # $2 = header text
                (?:[ ]+\{\#([-_:a-zA-Z0-9]+)\})?    # $3 = id attribute
                [ ]*\n(=+|-+)                       # $4 = header footer
                (c\d?)?[ ]*\n+                      # $5 = color
            }mx',
            array('self', 'do_headers_callback_setext'), $text);

        /* atx-style headers:
            # Header 1
            ## Header 2
            ## Header 2 with closing hashes ##
            ...
            ###### Header 6
        */
        $text = preg_replace_callback('{
                (\n{0,2})       # $1 = header at start of text
                ^(\#{2,6})      # $2 = string of #\'s
                ((c\d?)[ ])?    # $4 = color
                [ ]*
                (.+?)           # $5 = Header text
                [ ]*
                \#*             # optional closing #\'s (not counted)
                (?:[ ]+\{\#([-_:a-zA-Z0-9]+)\})?    # $6 = anchor name
                (?:[ ](?<=(?:\#|\})[ ])(.*?))?      # $7 = extra text
                [ ]*
                \n+
            }xm',
            array('self', 'do_headers_callback_atx'), $text);
        
        // Insert TOC
        if ($toc_enable)
        {
            for ($i = 0; $i < min($toc_level, $toc_level_max); $i++)
            {
                $toc .= '</li></ul>';
            }
            $toc .= '</td></tr></tbody></table>';
            if ($add_clearer)
            {
                $toc .= '<div class="clearer"></div>';
            }
            $toc .= '<p>';
            $text = preg_replace('#\n?\[toc[ ]*\d*[ ]*(right|left)?\]\n?#i', $toc, $text, 1);
        }
        
        return $text;
    }
    
    public static function do_headers_callback_setext($matches) {
        // Check we haven't found an empty list item.
        if ($matches[4] == '-' && preg_match('{^-(?: |$)}', $matches[2]))
            return $matches[0];
        
        $level = $matches[4]{0} == '=' ? '##' : '###';
        if (isset($matches[5]))
        {
            $level .= $matches[5];
        }
        $anchor_name = $matches[3] == '' ? '' : ' {#' . $matches[3] . '}';
        $block = $matches[1] . $level . ' ' . $matches[2] . $anchor_name . "\n";
        return $block;
    }
    
    public static function do_headers_callback_atx($matches) {
        global $header_level, $toc_level, $toc_visible_level, $toc_level_max, $toc_enable, $toc;
        
        $level = strlen($matches[2]);
        if (!isset($matches[6]))
        {
            $matches[6] = '';
        }
        if (!isset($matches[7]))
        {
            $matches[7] = '';
        }
        $block = self::get_header_code($matches[5], $matches[6], $level, $matches[1],  $matches[4], $matches[7]);
        return $block;
    }
    
    public static function get_header_code($header_name, $anchor_name = '', $level, $start_header = '', $color = '', $extra_text = '')
    {
        global $header_level, $toc_level, $toc_visible_level, $toc_level_max, $toc_enable, $toc;
        
        if($anchor_name == '')
        {
            $anchor_name = $header_name;
            $pattern = array('#\[b\](.*?)\[/b\]#s',
                             '#\[i\](.*?)\[/i\]#s',
                             '#\[u\](.*?)\[/u\]#s',
                             '#\[s\](.*?)\[/s\]#s',
                             '#\[color=(.*?)\](.*?)\[/color\]#s');
            $replace = array('$1', '$1', '$1', '$1', '$2');
            $anchor_name = preg_replace($pattern, $replace, $anchor_name);
        }
        $anchor_name = self::get_anchor_name($anchor_name);
        
        $hfirst = '';
        
        if ($toc_level == 0 && $start_header == "")
        {
            $hfirst = ' hfirst';
        }
        
        if ($color == '')
        {
            $color_class = '';
        }
        else
        {
            $color = explode('c', $color);
            $color_class = ' class="hcolor' . $color[1] . '"';
        }
        
        $toc_link = '';
        
        if ($toc_enable)
        {
            $toc_item = '';
            
            if ($toc_level == 0)
            {
                $toc_level = 1;
            }
            else if ($level > $header_level)
            {
                $delta_level = min($level - $header_level, 5 - $toc_level);
                $toc_level += $delta_level;
                if ($toc_level <= $toc_level_max)
                {
                    if ($delta_level > 0)
                    {
                        $toc_item .= '<ul class="toc">';
                        for ($i = 1; $i < $delta_level; $i++)
                        {
                            $toc_item .= '<li><ul class="toc">';
                        }
                    }
                    else
                    {
                        $toc_item .= '</li>';
                    }
                }
            }
            else if ($level < $header_level)
            {
                $delta_level = min($header_level - $level, $toc_level - 1);
                if ($toc_level <= $toc_level_max)
                {
                    $delta_visible_level = $delta_level;
                }
                else
                {
                    $delta_visible_level = min($toc_level_max - $toc_level + $delta_level, $toc_visible_level - 1);
                }
                $toc_level -= $delta_level;
                if ($toc_level <= $toc_level_max)
                {
                    if ($delta_visible_level > 0)
                    {
                        for ($i = 0; $i < $delta_visible_level; $i++)
                        {
                            $toc_item .= '</li></ul>';
                        }
                    }
                    $toc_item .= '</li>';
                }
            }
            else if ($toc_level <= $toc_level_max)
            {
                $toc_item .= '</li>';
            }
            
            if ($toc_level <= $toc_level_max)
            {
                $toc_visible_level = $toc_level;
                $toc_item .= '<li><a href="#'.$anchor_name.'">'.$header_name.'</a>';
                $toc .= $toc_item;
            }
            
            $header_level = $level;
            
            if ($toc_level == 1)
            {
                $toc_link = '<span class="toc_link"><a href="#toc">' . __('Summary') . '</a></span>';
            }
        }
        
        if ($extra_text != "")
        {
            $extra_text = '<span class="hextra">' . $extra_text . '</span>';
        }
        
        $header_code = "</p><h$level".' class="htext'.$hfirst.'" id="'.$anchor_name.'"><a'.$color_class.' href="#'.$anchor_name.'">'.$header_name.'</a>'.$extra_text.$toc_link."</h$level><p>";
        
        return $header_code;
    }
    
    public static function get_anchor_name($anchor_str)
    {
        $anchor_name = html_entity_decode($anchor_str, ENT_QUOTES, 'UTF-8');
        $anchor_name = str_replace('?', '-', $anchor_name);
        $anchor_name = strtolower(strtr(utf8_decode($anchor_name),
                                 utf8_decode("ÀÁÂÃÄÅÆàáâãäåÇČçčÈÉÊËèéêëÌÍÎÏìíîïÑñÒÓÔÕÖØòóôõöøŠšÙÚÛÜùúûüÝΫýÿŽž"),
                                             "AAAAAAaaaaaaaCCccEEEEeeeeIIIIiiiiNnOOOOOOooooooSsUUUUuuuuYYyyZz"));
        $pattern = array('~[\W\s_]+~u', '~[^-\w]+~');
        $replace = array('-', '');
        $anchor_name = preg_replace($pattern, $replace, $anchor_name);
        $anchor_name = trim($anchor_name, '-');
        if (is_numeric($anchor_name[0])) $anchor_name = '_'.$anchor_name;
        return $anchor_name;
    }


    //
    // Convert ordered (numbered) and unordered (bulleted) lists.
    //
//    static var $tab_width = 4;
//     static var $list_level = 0;
   
    public static function do_lists($text) {
        global $list_level;

        # Re-usable patterns to match list item bullets and number markers:
        $marker_ul_re  = '[*+-]';
        $marker_ol_re  = '\d+[.]';
        $marker_any_re = "(?:$marker_ul_re|$marker_ol_re)";

        $markers_relist = array($marker_ul_re, $marker_ol_re);

        foreach ($markers_relist as $marker_re) {
            # Re-usable pattern to match any entirel ul or ol list:
            $whole_list_re = '
                (                                # $1 = whole list
                  (                                # $2
                    ('.$marker_re.')            # $3 = first list item marker
                    [ ]+
                  )
                  (?s:.+?)
                  (                                # $4
                      \z
                    |
                      \n?(?=</p>)
                    |
                      \n{2}
                      (?=\n*\S)
                      (?!                        # Negative lookahead for another list item marker
                        [ ]*
                        '.$marker_re.'[ ]+
                      )
                  )
                )
            '; // mx
            
            # We use a different prefix before nested lists than top-level lists.
            # See extended comment in _ProcessListItems().
        
            if ($list_level) {
                $text = preg_replace_callback('{
                        ^
                        '.$whole_list_re.'
                    }mx',
                    array('self', '_doLists_callback'), $text);
            }
            else {
                $text = preg_replace_callback('{
                        (?:(?<=[ ]\n)|(?<=<p>)\n?|\n{2}|\A\n?) # Must eat the newline
                        '.$whole_list_re.'
                    }mx',
                    array('self', '_doLists_callback'), $text);
            }
        }

        return $text;
    }
    public static function _doLists_callback($matches) {
        global $list_level;
        
        # Re-usable patterns to match list item bullets and number markers:
        $marker_ul_re  = '[*+-]';
        $marker_ol_re  = '\d+[.]';
        $marker_any_re = "(?:$marker_ul_re|$marker_ol_re)";
        
        $list = $matches[1];
        $list_type = preg_match("/$marker_ul_re/", $matches[3]) ? "ul" : "ol";
        
        $marker_any_re = ( $list_type == "ul" ? $marker_ul_re : $marker_ol_re );
        
        $list .= "\n";
        $result = self::processListItems($list, $marker_any_re);
        
        $result = "<$list_type class=\"text\">" . $result . "</$list_type>";
        
        if ($list_level == 0)
        {
            $result = '</p>' . $result . '<p>';
        }
        
        return $result;
    }

    public static function processListItems($list_str, $marker_any_re) {
    #
    #    Process the contents of a single ordered or unordered list, splitting it
    #    into individual list items.
    #
        # The self::list_level global keeps track of when we're inside a list.
        # Each time we enter a list, we increment it; when we leave a list,
        # we decrement. If it's zero, we're not in a list anymore.
        #
        # We do this because when we're not inside a list, we want to treat
        # something like this:
        #
        #        I recommend upgrading to version
        #        8. Oops, now this line is treated
        #        as a sub-list.
        #
        # As a single paragraph, despite the fact that the second line starts
        # with a digit-period-space sequence.
        #
        # Whereas when we're inside a list (or sub-list), that line will be
        # treated as the start of a sub-list. What a kludge, huh? This is
        # an aspect of Markdown's syntax that's hard to parse perfectly
        # without resorting to mind-reading. Perhaps the solution is to
        # change the syntax rules such that sub-lists must start with a
        # starting cardinal number; e.g. "1." or "a.".
        
        global $list_level;
        
        $list_level++;

        # trim trailing blank lines:
        $list_str = preg_replace("/\n{2,}\\z/", "\n", $list_str);

        $list_str = preg_replace_callback('{
            (\n)?                            # leading line = $1
            (^[ ]*)                            # leading whitespace = $2
            ('.$marker_any_re.'                # list marker and space = $3
                (?:[ ]+|(?=\n))    # space only required if item is not empty
            )
            ((?s:.*?))                        # list item text   = $4
            (?:(\n+(?=\n))|\n)                # tailing blank line = $5
            (?= \n* (\z | \2 ('.$marker_any_re.') (?:[ ]+|(?=\n))))
            }xm',
            array('self', '_processListItems_callback'), $list_str);

        $list_level--;
        return $list_str;
    }
    public static function _processListItems_callback($matches) {
        $item = $matches[4];
        $leading_line =& $matches[1];
        $leading_space =& $matches[2];
        $marker_space = $matches[3];
        $tailing_blank_line =& $matches[5];

        if ($leading_line || $tailing_blank_line || 
            preg_match('/\n{2,}/', $item))
        {
            # Replace marker with the appropriate whitespace indentation
        //    $item = $leading_space . str_repeat(' ', strlen($marker_space)) . $item;
            $item = self::outdent($item);
        }
        else {
            # Recursion for sub-lists:
            $item = self::do_lists(self::outdent($item));
            $item = preg_replace('/\n+$/', '', $item);
        }

        return "<li>" . $item . "</li>";
    }
    public static function outdent($text) {
    #
    # Remove one level of line-leading tabs or spaces
    #
    //    return preg_replace('/^(\t|[ ]{1,'.self::$tab_width.'})/m', '', $text);
        return preg_replace('/^(\t|[ ])/m', '', $text);
    }


    //
    // Convert line code to html table.
    //
   
    public static function do_lines($text) {
        global $line_index, $abseil_index, $line_index_old, $abseil_index_old, $line_suffix, $abseil_suffix, $line_reference, $abseil_reference, $first_line, $first_block_line, $header_line, $nb_col, $nb_col_max, $doc_module, $cell_index;
        
        $line_index = 0;
        $line_index_old = 0;
        $abseil_index = 0;
        $abseil_index_old = 0;
        $line_suffix = '';
        $abseil_suffix = '';
        $line_reference = -1;
        $abseil_reference = -1;
        $first_line = true;
        $first_block_line = true;
        $header_line = false;
        $nb_col = 0;
        $nb_col_max = 0;
        $doc_module = sfContext::getInstance()->getModuleName();
        $cell_index = 0;

        $whole_list_re = '
            (                   # $1 = whole list
              ([LR]\#[^:|\s]*)      # $2 = first list item marker
              (?s:.+?)
              (                 # $3
                  \z
                |
                  \n?(?=</p>)
                |
                  \n{2}
                  (?=\n*\S)
                  (?![LR]\#)       # Negative lookahead for another list item marker
              )
            )
        '; // mx

    //    $whole_list_re = '(([LR]\#[^:|\s]*)(?s:.+?)(\z|\n?(?=</p>)|\n{2}(?=\n*\S)(?![LR]\#)))'; // mx
    //    $whole_list_re_all = '{(?:(?<=[ ]\n)|(?<=<p>)\n?|\n{2}|\A\n?)(([LR]\#[^:|\s]*)(?s:.+?)(\z|\n?(?=</p>)|\n{2}(?=\n*\S)(?![LR]\#)))}m';

        $text = preg_replace_callback('{
                (?:(?<=[ ]\n)|(?<=<p>)\n?|\n{2}|\A\n?) # Must eat the newline
                '.$whole_list_re.'
            }mx',
            array('self', '_doLines_callback'), $text);

        return $text;
    }
    public static function _doLines_callback($matches) {
        global $line_index, $abseil_index, $line_index_old, $abseil_index_old, $line_suffix, $abseil_suffix, $line_reference, $abseil_reference, $first_line, $first_block_line, $header_line, $nb_col, $nb_col_max, $doc_module, $cell_index;
        
        $first_block_line = true;
        $list = $matches[1] . "\n";
        
        # trim trailing blank lines:
        $list = preg_replace("/\n{2,}\\z/", "\n", $list);

        $list = preg_replace_callback('{
            \n?                        # leading line
            ^([LR])\#                  # line marker = $1
            (\+?)                      # relative index = $2
            (\d*)                      # new line index = $3
            ([^\d-+!:|\s][^-+!:|\s]*|) # new line suffix = $4
            (?:-(\+?)(\d+))?           # multi line index = $5 $6
            (!?)                       # reference index flag = $7
            \s*[:|]*                   # first separator
            ((?s:.*?))                 # line item text = $8
            (?:\n+(?=\n)|\n)           # tailing blank line
            (?= \n* (\z | [LR]\#))
            }xm',
            array('self', '_processLineItems_callback'), $list);

            // '{\n?^([LR])\#(\+?)(\d*)([^\d-!:|\s][^-!:|\s]*|)(?:-(\+?)(\d+))?(!?)\s*[:|]*((?s:.*?))(?:\n+(?=\n)|\n)(?=\n*(\z|[LR]\#))}m'
        
        if ($nb_col_max > $nb_col)
        {
            $nb_col = $nb_col_max - $nb_col;
            
            $list = preg_replace_callback('{<(t[dh]) colspan="(\d+)">}', array('self', '_processUpdateColspan'), $list);
        }
        
        $result = '</p><table class="route_lines"><tbody>' . $list . '</tbody></table><p>';
        
        return $result;
    }

    public static function _processLineItems_callback($matches) {
        global $is_line, $line_index, $abseil_index, $line_index_old, $abseil_index_old, $line_suffix, $abseil_suffix, $line_reference, $abseil_reference, $first_line, $first_block_line, $header_line, $nb_col, $nb_col_max, $doc_module, $cell_index;
        
        $cell_index = 0;
        
        $marker_type = $matches[1];
        $is_line = ($marker_type == 'L');
        $new_marker_relative = $matches[2];
        $new_marker_index = $matches[3];
        $new_marker_suffix = $matches[4];
        $multi_line_relative = $matches[5];
        $multi_line_index = $matches[6];
        $reference_flag = $matches[7];
        $item = $matches[8];
        $cell_tag = 'th';
        
        if ($new_marker_suffix != '~')  // description de longueur
        {
            if ($new_marker_suffix == '=')  // ligne de titre
            {
                $header_line = true;
                $new_marker_suffix = '';
                $new_marker_index = '';
                $multi_line_index = '';
                $multi_line_relative = '';
                $multi_line_index = '';
                $line_suffix = '';
                $abseil_suffix = '';
                $line_reference = -1;
                $abseil_reference = -1;
            }
            else
            {
                $header_line = false;
            }
            if (!empty($new_marker_relative) && $new_marker_index == '')
            {
                $new_marker_index = 1;
            }
            if (!empty($multi_line_relative) && $multi_line_index == '')
            {
                $multi_line_index = 1;
            }
            if (!empty($new_marker_relative))
            {
                $index_incr = $new_marker_index;
            }
            else
            {
                $index_incr = 1;
            }
            
            if (!$header_line && $marker_type == 'L') // L : line
            {
                if ($doc_module == 'sites')
                {
                    $marker_type = '';
                    $cell_tag = 'td';
                }
                else
                {
                    $marker_type = __('route_line_prefix');
                }
                
                $line_suffix_old = $line_suffix;
                if ($new_marker_suffix == '_')
                {
                    $line_suffix = '';
                }
                elseif (!empty($new_marker_suffix))
                {
                    $line_suffix = preg_replace('#^(\w)#', '&nbsp;$1', $new_marker_suffix);
                }

                if (empty($new_marker_relative) && $new_marker_index != '')
                {
                    if ($first_line == false && $line_suffix != $line_suffix_old)
                    {
                        $line_index_old = $line_index;
                        $line_index = $new_marker_index;
                    }
                    else
                    {
                        $line_index = $new_marker_index;
                        $line_index_old = $line_index;
                    }
                    if (empty($reference_flag))
                    {
                        $line_reference = -1;
                    }
                }
                else
                {
                    if ($first_line == false && $line_suffix != $line_suffix_old)
                    {
                        if (empty($line_suffix))
                        {
                            $line_reference = -1;
                        }
                        $line_index_tmp = $line_index;
                        if ($line_reference > -1)
                        {
                            $line_index = $line_reference;
                        }
                        else
                        {
                            $line_index = $line_index_old;
                        }
                        if (empty($line_suffix_old))
                        {
                            $line_index_old = $line_index_tmp;
                        }
                        if (empty($line_suffix) || !empty($new_marker_relative))
                        {
                            $line_index += $index_incr;
                            if ($line_reference == -1)
                            {
                                $line_index_old = $line_index;
                            }
                        }
                    }
                    else
                    {
                        $line_index += $index_incr;
                    }
                    if (empty($line_suffix))
                    {
                        $line_index_old = $line_index;
                    }
                }
                
                if (!empty($reference_flag))
                {
                    $line_reference = $line_index;
                }
                
                $row_header = $marker_type . $line_index . $line_suffix;
                
                if ($multi_line_index != '')
                {
                    if (!empty($multi_line_relative))
                    {
                        $multi_line_index += $line_index; 
                    }
                    
                    if (preg_match('#^&nbsp;#', $line_suffix))
                    {
                        $separator = ' - ';
                    }
                    else
                    {
                        $separator = '&nbsp;-&nbsp;';
                    }

                    $row_header .= $separator . $marker_type . $multi_line_index . $line_suffix;
                    $line_index = $multi_line_index;
                    if (empty($line_suffix))
                    {
                        $line_index_old = $line_index;
                    }
                }
            }
            elseif (!$header_line && $marker_type == 'R')  // R : abseil
            {
                $marker_type = __('route_abseil_prefix');
                $abseil_suffix_old = $abseil_suffix;
                if ($new_marker_suffix == '_')
                {
                    $abseil_suffix = '';
                }
                elseif (!empty($new_marker_suffix))
                {
                    $abseil_suffix = preg_replace('#^(\w)#', '&nbsp;$1', $new_marker_suffix);
                }

                if (empty($new_marker_relative) && $new_marker_index != '')
                {
                    if ($first_line == false && $abseil_suffix != $abseil_suffix_old)
                    {
                        $abseil_index_old = $abseil_index;
                        $abseil_index = $new_marker_index;
                    }
                    else
                    {
                        $abseil_index = $new_marker_index;
                        $abseil_index_old = $abseil_index;
                    }
                    if (empty($reference_flag))
                    {
                        $abseil_reference = -1;
                    }
                }
                else
                {
                    if ($first_line == false && $abseil_suffix != $abseil_suffix_old)
                    {
                        if (empty($abseil_suffix))
                        {
                            $abseil_reference = -1;
                        }
                        $abseil_index_tmp = $abseil_index;
                        if ($abseil_reference > -1)
                        {
                            $abseil_index = $abseil_reference;
                        }
                        else
                        {
                            $abseil_index = $abseil_index_old;
                        }
                        if (empty($abseil_suffix_old))
                        {
                            $abseil_index_old = $abseil_index_tmp;
                        }
                        if (empty($abseil_suffix) || !empty($new_marker_relative))
                        {
                            $abseil_index += $index_incr;
                            if ($abseil_reference == -1)
                            {
                                $abseil_index_old = $abseil_index;
                            }
                        }
                    }
                    else
                    {
                        $abseil_index += $index_incr;
                    }
                    if (empty($abseil_suffix))
                    {
                        $abseil_index_old = $abseil_index;
                    }
                }
                
                if (!empty($reference_flag))
                {
                    $abseil_reference = $abseil_index;
                }
                
                $row_header = $marker_type . $abseil_index . $abseil_suffix;
                
                if ($multi_line_index != '')
                {
                    if (!empty($multi_line_relative))
                    {
                        $multi_line_index += $abseil_index; 
                    }
                    
                    if (preg_match('#^&nbsp;#', $abseil_suffix))
                    {
                        $separator = ' - ';
                    }
                    else
                    {
                        $separator = '&nbsp;-&nbsp;';
                    }

                    $row_header .= $separator . $marker_type . $multi_line_index . $abseil_suffix;
                    $abseil_index = $multi_line_index;
                    if (empty($abseil_suffix))
                    {
                        $abseil_index_old = $abseil_index;
                    }
                }
            }
            else
            {
                $row_header = '';
            }
            
            // protection des wikiliens
            $pattern[] = '{\[\[([^|]+?)\|([^\]]+?)\]\]}';
            $replace[] = '[[$1@#@$2]]';
            $item = preg_replace($pattern, $replace, $item);
            
            // traitement des références dans l'item
            $item = self::processLineItemLineReference($item);
            
            // traitement de l'item
            $pattern_item = '{\s*((?s:.*?))\s*([|]+|:{2,}|\z)\s*}m';
            
            /*    $item = preg_replace('{
                    \s*                      # cell start
                    ((?s:.*?))               # cell text  = $1
                    \s*([|]+|:{2,}|\z)\s*    # cell end   = $2
                    }xm',
                    '<td>$1</td>', $item);
            */
            
            $item = preg_replace_callback($pattern_item, array('self', '_processListCell'), $item);
            
            if ($first_block_line == true)
            {
                $nb_col = $cell_index;
                $nb_col_max = $nb_col;
            }
            elseif ($cell_index > $nb_col_max)
            {
                $nb_col_max = $cell_index;
            }
            
            $pattern = array();
            $replace = array();
            
            // suppression des cases vides en fin de ligne du tableau
            $pattern[] = '{(<td></td>)$}';
            $pattern[] = '{(<th></th>)$}';
            $replace[] = '';
            $replace[] = '';
            
            // déprotection des wikiliens
            $pattern[] = '{\[\[([^|\n]+?)@#@([^\]\n]+?)\]\]}';
            $replace[] = '[[$1|$2]]';
            
            // ajout d'espaces insécables
            $pattern[] = '{(\d) (\w)}';
            $replace[] = '$1&nbsp;$2';
            
            $item = preg_replace($pattern, $replace, $item);
            
            if ($cell_index < $nb_col)
            {
                $col_diff = $nb_col - $cell_index;
                $colspan = ' colspan="' . $col_diff . '"';
                $item .= '<td' . $colspan . '> </td>';
            }
            
            $first_line = false;
            $first_block_line = false;
                
            return '<tr><' . $cell_tag . '>' . $row_header . '</' . $cell_tag . '>' . $item . '</tr>';
        }
        else   // texte multicolonne inter-longueurs
        {
            $item = self::processLineItemLineReference($item);
            return '<tr class="interline"><td colspan="' . $nb_col . '">' . $item . '</td></tr>';
        }
    }
    
    // traitement des références dans une case
    public static function processLineItemLineReference($item)
    {
        global $doc_module, $is_line, $line_index, $abseil_index, $line_index_old, $abseil_index_old, $line_suffix, $abseil_suffix;
        
        $pattern_item = '{
            (?<=^|\W)([LR])\#          # line marker = $1
            (?:(\+|-)(\d+))?           # relative index = $2 $3
            ([^\d-+!:,;|\s][^-+!:,|\s]*|) # new line suffix = $4
            (?:-(\+|-)(\d+))?          # multi line index = $5 $6
            }xm';
        // '{(?<=^|\W)([LR])\#(?:(\+|-)(\d+))?([^\d-!:,;|\s][^-!:,|\s]*|)(?:-(\+|-)(\d+))?}m'
        
        return preg_replace_callback($pattern_item, array('self', '_processLineReference'), $item);
    }
    
    // traitement d'une référence
    public static function _processLineReference($matches)
    {
        global $doc_module, $is_line, $line_index, $abseil_index, $line_index_old, $abseil_index_old, $line_suffix, $abseil_suffix;
        
        $marker_type = $matches[1];
        $new_marker_relative = $matches[2];
        $new_marker_index = $matches[3];
        $new_marker_suffix = $matches[4];
        $multi_line_relative = $matches[5];
        $multi_line_index = $matches[6];
        
        if ($is_line)
        {
            if ($marker_type == 'L')
            {
                if ($doc_module == 'sites')
                {
                    $marker_type = '';
                }
                else
                {
                    $marker_type = __('route_line_prefix');
                }
            }
            else
            {
                $marker_type = __('route_belay_prefix');
            }
            $current_index = $line_index;
            $current_suffix = $line_suffix;
        }
        else
        {
            if ($marker_type == 'L')
            {
                $marker_type = __('route_line_prefix');
            }
            else
            {
                $marker_type = __('route_abseil_prefix');
            }
            $current_index = $abseil_index;
            $current_suffix = $abseil_suffix;
        }
        
        if (!empty($new_marker_relative) && $new_marker_index == '')
        {
            $new_marker_index = 1;
        }
        if (!empty($multi_line_relative) && $multi_line_index == '')
        {
            $multi_line_index = 1;
        }
        
        if ($new_marker_index == '')
        {
            $line_index_tmp = $current_index;
        }
        elseif ($new_marker_relative == '+')
        {
            $line_index_tmp = $current_index + $new_marker_index;
        }
        else
        {
            $line_index_tmp = $current_index - $new_marker_index;
        }
        
        if ($new_marker_suffix == '_')
        {
            $line_suffix_tmp = '';
        }
        elseif (!empty($new_marker_suffix))
        {
            $line_suffix_tmp = preg_replace('#^(\w)#', '&nbsp;$1', $new_marker_suffix);
        }
        else
        {
            $line_suffix_tmp = $current_suffix;
        }
        
        $row_header = $marker_type . $line_index_tmp . $line_suffix_tmp;
        
        if ($multi_line_index != '')
        {
            if ($multi_line_relative == '+')
            {
                $multi_line_index = $line_index_tmp + $multi_line_index; 
            }
            else
            {
                $multi_line_index = $line_index_tmp - $multi_line_index; 
            }
            $row_header .= ' - ' . $marker_type . $multi_line_index . $line_suffix_tmp;
        }
        
        return $row_header;
    }
    
    public static function _processListCell($matches)
    {
        global $doc_module, $header_line, $cell_index;
        
        $cell_index ++;
        
        if ($header_line || ($doc_module == 'sites' && $cell_index == 1))
        {
            $cell_tag = 'th';
        }
        else
        {
            $cell_tag = 'td';
        }
        
        return '<' . $cell_tag . '>' . $matches[1] . '</' . $cell_tag . '>';
    }
    
    public static function _processUpdateColspan($matches)
    {
        global $nb_col;
        
        $cell_tag = $matches[1];
        $col_diff = $matches[2] + $nb_col;
        
        return '<' . $cell_tag . ' colspan="' . $col_diff . '">';
    }
    
    
    //
    // convert img tag
    //
    public static function do_images($text, $images = null, $filter_image_type = true, $show_images = true)
    {
        // accepts only internal images (filename)
        // [img=ID /] or [img=ID position /] or [img=ID position]legende[/img] // TODO check different spaces possibilities
        if ($show_images)
        {
            $pattern = array('#\[img=(\s*)([0-9]*)([\w\s]*)\](.*?)\[/img\]\n?#ise', // img tags
                             '#\[img=(\s*)([0-9]*)([\w\s]*)\/\]\n?#ise',
                             '#\[img=(\s*)(.*?)\.(jpg|jpeg|png|gif)([\w\s]*)\/\]\n?#ise', // static insertion (pictos etc)
                             '#\[img=(\s*)(.*?)\.(jpg|jpeg|png|gif)([\w\s]*)\](.*?)\[/img\]\n?#ise');
            $replace = array("self::handle_img_id_tag('$2', '$3', '$4', \$images, \$filter_image_type)",
                             "self::handle_img_id_tag('$2', '$3', '', \$images, \$filter_image_type)",
                             'self::handle_static_img_tag(\'$2\', \'$3\', \'$4\')',
                             'self::handle_static_img_tag(\'$2\', \'$3\', \'$4\', \'$5\')');
        }
        else
        {
            $pattern = array('#\[img(.*?)\](.*)\[/img\]\n?#s',
                             '#\[img(.*?)\/\]\n?#s');
            $replace = array('', '');
        }
        $text = preg_replace($pattern, $replace, $text);
        
        return $text;
    }

    public static function do_videos($text, $show_videos = true)
    {
        if (stripos($text, '[/video]') !== false)
        {
            $mobile_version = c2cTools::mobileVersion();
            $width = $mobile_version ? 310 : 400;
            $height = $mobile_version ? 232 : 300;
            $alternatif = '<strong>Flash plugin needed</strong>';

            // first replace all [video] by [video $width,$height]
            // for mobile version, we force the dimensions
            $text = $mobile_version ? preg_replace('#\[video( ([0-9]{2,4}),([0-9]{2,4}))?\]#i', "[video $width,$height]", $text)
                                    : preg_replace('#\[video\]#i', "[video $width,$height]", $text);

            if ($show_videos)
            {
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
            }
            else
            {
                $patterns = array('#\[video(.*?)\](.*)\[/video\]\n?#s');
                $replacements = array('');
            }

            $text = preg_replace($patterns, $replacements, $text);
        }

        return $text;
    }
    
    
    //
    // Deal with newlines, tabs and multiple spaces
    //
    public static function do_spaces($text, $keep_newline = true)
    {
        $pattern = array("\n", "\t", '    ', '  ');
        if ($keep_newline)
        {
            $replace = array('<br />', '&nbsp; &nbsp; ', '&nbsp; ', ' &nbsp;');
        }
        else
        {
            $replace = array(' ', '&nbsp; &nbsp; ', '&nbsp; ', ' &nbsp;');
        }
        $text = str_replace($pattern, $replace, $text);
        
        $pattern = array('#(\d) ( *)((mm?(in)?|°|km|h|s)(\W|$))#gi', ' ([?!:])');
        $replace = array('\1\2\3', '&nbsp;\1');
        $text = preg_replace($pattern, $replace, $text);
        
        return $text;
    }
    
    
    //
    // Add new line in the HTML code
    //
    public static function improve_html_code($text)
    {
        $pattern = array('<br />', '<p>', '</p>', '<pre>', '</pre>', '<ul', '<ol', '<li>', '</ul>', '</ol>');
        $replace = array("<br />\n", "<p>\n", "\n</p>", "<pre>\n", "\n</pre>", "\n<ul", "\n<ol", "\n\t<li>", "\n</ul>\n", "\n</ol>\n");
        $text = str_replace($pattern, $replace, $text);
        
        return $text;
    }
    
    
    //
    // Extract abstract from [abstract]  markup
    //
    public static function extract_abstract($text)
    {
        $abstract = array();
        $pattern = '#\[abs(tract)?\]\s*(.*?)\s*\[/abs(tract)?\]#s';
        $has_abstract = preg_match($pattern, $text, $abstract);
        if ($has_abstract)
        {
            return $abstract[2];
        }
        else
        {
            return $text;
        }
    }
    
    
    /**
     * Parse message text
     */
    public static function parse_message($text, $images = null, $collaborative_doc = true, $show_images = true)
    {
        global $list_level;
        $list_level = 0;
        
        $text = self::parse_linebreaks($text);
        
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
        $text = self::do_headers($text);
        $text = self::do_lines($text);
        $text = self::do_lists($text);
        $text = self::do_bbcode($text, true);
        $text = self::do_images($text, $images, $collaborative_doc, $show_images);
        $text = self::do_videos($text, !$collaborative_doc);
        $text = self::do_spaces($text, true);
    
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
        
        // Add class "img" to paragraph with only one image
        $text = preg_replace('#((</h\d>|^)(\s*))<p>((\s*)<a data-lightbox="(.*?)/></a>(\s*)</p>(\s*)<h\d)#is', '$1<p class="img">$4', $text);
        
        // Add new line in the HTML code
        $text = self::improve_html_code($text);
    
        return $text;
    }

    public static function parse_message_simple($text)
    {
        $text = self::parse_linebreaks($text);
        $text = self::do_clickable($text);
        $text = self::do_bbcode($text, false, false);
        $text = self::do_images($text, null, false, false);
        $text = self::do_videos($text, false);
        $text = preg_replace(array('/(?<![&LR])#+((c\d?)[ ])?[ ]?/', '#\[toc[ ]*(\d*)[ ]*(right|left)?\]#i'), array('', ''), $text);
        $text = self::do_spaces($text, false);
    
        return $text;
    }

    public static function parse_message_abstract($text)
    {
        $text = self::parse_linebreaks($text);
        $text = self::do_clickable($text);
        $text = self::do_bbcode($text, true);
        $text = self::do_images($text, null, false, false);
        $text = self::do_videos($text, false);
        $text = self::do_spaces($text, true);
    
        // Make sure there are no empty paragraphs
        $text = str_replace('<p></p>', '', $text);
        
        // Add new line in the HTML code
        $text = self::improve_html_code($text);
    
        return $text;
    }
}
