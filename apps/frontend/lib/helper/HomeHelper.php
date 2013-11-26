<?php
/**
 * Home helpers
 */

function nav_title($id, $title, $icon, $id_prefix = 'nav', $link = '', $rss_link = '', $rss_tips = '')
{
    $id = $id_prefix . '_' . $id;
    $cookie_position = array_search($id, sfConfig::get('app_personalization_cookie_fold_positions'));
    $html = '<div id="' . $id . '_section_title" class="nav_box_title" title="' . __('section close') .
            '" data-toggle-view="' . $id . '" data-cookie-position="' . $cookie_position . '">';
    $html .= '<div id="' . $id . '_toggle" class="nav_box_image picto_' . $icon . '"></div>';
    if (!empty($rss_link))
    {
        $html .= link_to('', $rss_link,
                         array('class' => 'nav_title_right picto_rss',
                               'title' => $rss_tips,
                               'rel' => 'nofollow'));
    }
    if (!empty($link))
    {
        $title = link_to($title, $link);
    }
    $html .= '<div class="nav_box_title_text">' . $title . '</div>';
    $html .= '</div>';
    return $html;
}


// We assume that $text is 'good and valid xhtml' with good propreties
// - tags are properly written
// - tags are open and closed correctly (no <b><i></b></i>)
// - no < or > inside scripts (only antispam scripts)
// - etc
//
// It has also some simplifications to make it simpler:
// - antispam e-mails always count for 25 chars (whatever the real size of the e-mail)
// - Special chars like &eacute; or &lt; count for 8 or 4 chars, not 1
//   We just make sure there is no trailing broken one
function truncate_article_abstract($text, $size)
{
    if (strlen($text) <= $size) return '<p class="abstract">'.$text.'</p>';

    $parts = explode('<', $text);

    $tags = array();
    $output = '';
    $count = 0;

    foreach ($parts as $part)
    {
        // detect tag
        $partlen = strlen($part);
        if ($partlen && preg_match('/^(\/?)([a-z]+)(\s|>)/', $part, $matches))
        {
            $tag = $matches[2];

            // keep count of opening and closing tags
            if ($tag != 'br')
            {
                if (empty($matches[1])) // opening tag
                {
                    array_push($tags, $tag);
                    $opening_tag = true;
                }
                else // closing tag
                {
                    if (array_pop($tags) != $tag)
                    {
                        // our function is not clever enough
                        return 'Ooops';
                    }
                }
            }

            $end_of_tag = strpos($part, '>');
            if ($end_of_tag === false) {
                return 'Oooops';
            }

            if ($tag == 'script' && $opening_tag) // it's e-mail antispam. To keep it simple, count it like 25 chars
            {
                $count += 25;
                $output .= '<' . $part;
            }
            else if ($count + $partlen - $end_of_tag - 1 > $size)
            {
                $output .= '<' . substr($part, 0, $size - $count)
                               . _handle_trailing_htmlchars(substr($part, $size - $count - 8, 16));
                $count += $size - $count;
                break;
            }
            else
            {
                $count += $partlen - $end_of_tag - 1;
                $output .= '<' . $part;
            }
            
        }
        else if ($partlen)
        {
           // No tag. That's because text doesn't begin with a tag
           if ($count + $partlen <= $size)
           {
                $output .= $part;
                $count += $partlen;
           }
           else
           {
               $output .= substr($part, 0, $size - $count)
                          . _handle_trailing_htmlchars(substr($part, $size - $count - 8, 16));
               $count += $size - $count;
               break;
           }
        }
    }

    // Add ... if we cut the text
    if ($count == $size)
        $output .= "...";

    // close remaining opened tags
    $tags = array_reverse($tags);
    foreach ($tags as $opened_tag)
    {
        $output .= "</$tag>";
    }

    return '<p class="abstract">'.trim($output).'</p>';
}

function _handle_trailing_htmlchars($text)
{
    $amp_pos = strrpos($text, '&', -8);
    if ($amp_pos === false) return '';
    $semicolon_pos = strpos($text, ';', $amp_pos);
    if ($semicolon_pos === false || $semicolon_pos < 8) return '';
    return substr($text, 8, $semicolon_pos - 7);
}
