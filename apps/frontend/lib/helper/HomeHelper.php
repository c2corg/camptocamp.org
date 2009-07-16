<?php
/**
 * Home helpers
 */

function nav_title($id, $title, $icon)
{
    $option1 = __('section close');
    $option2 = __('section open');
    $html = '<div id="nav_' . $id . '_section_title" class="nav_box_title" title="' . $option1 .
            '" onclick="toggleHomeSectionView(\'nav_' . $id . '\', \'' . $option1 .
            '\', \'' . $option2 . '\'); return false;">';
    $html .= '<div id="nav_' . $id . '_toggle" class="nav_box_image picto_' . $icon . '"></div>';
    $html .= '<div class="nav_box_title_text">' . $title . '</div>';
    $html .= '</div>';
    return $html;
}


// we assume that $text is 'good and valid xhtml'   
// - tags are properly written
// - tags are open and closed correctly (no <b><i></b></i>)
// - no < or > inside scripts
// - etc
// TODO do not cut and properly things like '&eacute;'
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
                $output .= '<' . substr($part, 0, $size - $count);
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
               $output .= substr($part, 0, $size - $count);
               $count += $size - $count;
               break;
           }
        }
    }

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

