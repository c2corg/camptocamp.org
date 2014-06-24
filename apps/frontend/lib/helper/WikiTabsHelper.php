<?php
/**
 * $Id: WikiTabsHelper.php 2378 2007-11-20 09:21:02Z fvanderbiest $
 */
//use_helper('Forum');

function setActiveIf($current_tab, $active_tab)
{
    if (!empty($active_tab) && $current_tab == $active_tab)
    {
        return ' class="active_tab"';
    }
}

function tab_tag($tab_name, $active_link, $active_tab, $url, $tab_class, $commCount = 0, $forum_link = false)
{
    $tab_title = __($tab_name.'_tab_help');
    $tab_text = __(ucfirst($tab_name));
    if ($commCount)
    {
        $tab_text = '<span class="reduced">' . $commCount . '</span>'
                  . '<span>' . $tab_text . ' (' . $commCount . ')' . '</span>';
    }
    else
    {
        $tab_text = '<span>' . $tab_text . '</span>';
    }
    
    if ($forum_link)
    {
        
        if ($active_link)
        {
            use_helper('Forum');
            $link = f_link_to($tab_text, $url,
                              array('class' => $tab_class, 'title' => $tab_title));
        }
        else
        {
            $link = '<div class="' . $tab_class . '" title="' . $tab_title . '">'
                  . $tab_text
                  . '</div>';
        }
    }
    else
    {
        $options_array = array('class' => $tab_class, 'title' => $tab_title);
        if (!$active_link) // FIXME necessary to handle link_to_if bug with 1.0.11
        {
            $options_array['tag'] = 'div';
        }
        $link = link_to_if($active_link, $tab_text, $url, $options_array);
    }

    return '<li' . setActiveIf($tab_name, $active_tab) . '>' . $link . '</li>';
}

function tabs_list_tag($id, $lang, $exists_in_lang, $active_tag, $version = null, $slug = '', $nb_comments = null)
{
    $instance = sfContext::getInstance();
    $module = $instance->getModuleName();

    if ($active_tag)
    {
        if ($nb_comments == 0)
        {
            // check if anonymous users can create comments
            if (!sfContext::getInstance()->getUser()->isConnected() && !in_array($lang, sfConfig::get('app_anonymous_comments_allowed_list')))
            {
                $comment_tag = tab_tag('comments', 0, $active_tag, '', 'action_comment', $nb_comments);
            }
            else
            {
                $comment_tag = tab_tag('comments', $id, $active_tag, 'post.php?fid=1&subject=' . $id . '_' . $lang, 'action_comment', $nb_comments, true);
            }
        }
        else
        {
            $comment_tag = tab_tag('comments', $id, $active_tag, "@document_comment?module=$module&id=$id&lang=$lang", 'action_comment', $nb_comments);
        }
    }
    else
    {
        $comment_tag = tab_tag('comments', $id, $active_tag, '', 'action_comment', $nb_comments);
    }
    
    // check if it is an old version
    if (!is_null($version))
    {
        return '<nav id="nav_edit" class="nav_box"><ul>' .
           tab_tag('view', $id, $active_tag, "@document_by_id_lang_version?module=$module&id=$id&lang=$lang&version=$version", 'action_filter') . 
           tab_tag('edit', $id, $active_tag, "@document_edit_archive?module=$module&id=$id&lang=$lang&version=$version", 'action_edit') .
           tab_tag('history', $id && $exists_in_lang, $active_tag, "@document_history?module=$module&id=$id&lang=$lang", 'action_list') .
           $comment_tag .
           '</ul></nav>';
    }
    else
    {
        $view_route = $slug ? "@document_by_id_lang_slug?module=$module&id=$id&lang=$lang&slug=$slug"
                            : "@document_by_id_lang?module=$module&id=$id&lang=$lang";

        return '<nav id="nav_edit" class="nav_box"><ul>' .
           tab_tag('view', $id, $active_tag, $view_route, 'action_filter') .
           tab_tag('edit', $id, $active_tag, "@document_edit?module=$module&id=$id&lang=$lang", 'action_edit') .
           tab_tag('history', $id && $exists_in_lang, $active_tag, "@document_history?module=$module&id=$id&lang=$lang", 'action_list') .
           $comment_tag .
           '</ul></nav>';
    }
}
