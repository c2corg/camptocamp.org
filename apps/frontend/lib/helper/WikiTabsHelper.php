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
    $commCount = ($commCount != 0) ? ' (' . $commCount . ')' : '';
    use_helper('Forum');
    $link = ($forum_link) ? f_link_to('<span>' . __(ucfirst($tab_name)) . $commCount . '</span>', $url,
                                           array('class' => $tab_class)):
                            link_to_if($active_link, '<span>' . __(ucfirst($tab_name)) . $commCount . '</span>', $url,
                                           array('class' => $tab_class, 'tag' => 'div'));

    return '<li' . setActiveIf($tab_name, $active_tab) . '>' . $link . '</li>';
}

function tabs_list_tag($id, $lang, $exists_in_lang, $active_tag, $version = null)
{
    $instance = sfContext::getInstance();
    $module = $instance->getModuleName();
    
    $nbComm = ($id && $active_tag != 'comment') ? PunbbComm::GetNbComments($id.'_'.$lang) : 0 ;
    
    $comment_tag = ($nbComm == 0) ? tab_tag('comments', $id, $active_tag, 'post.php?fid=1&subject=' . $id . '_' . $lang, 'action_comment', $nbComm, true) :
                                    tab_tag('comments', $id, $active_tag, "@document_comment?module=$module&id=$id&lang=$lang", 'action_comment', $nbComm) ;
    
    // check if it is an old version
    if (!is_null($version))
    {
        return '<div id="nav_edit"><ul>' .
           tab_tag('view', $id, $active_tag, "@document_by_id_lang_version?module=$module&id=$id&lang=$lang&version=$version", 'action_filter') . 
           tab_tag('edit', $id, $active_tag, "@document_edit_archive?module=$module&id=$id&lang=$lang&version=$version", 'action_edit') .
           tab_tag('history', $id && $exists_in_lang, $active_tag, "@document_history?module=$module&id=$id&lang=$lang", 'action_list') .
           $comment_tag .
           '</ul></div>';
    }
    else
    {
        return '<div id="nav_edit"><ul>' .
           tab_tag('view', $id, $active_tag, "@document_by_id_lang?module=$module&id=$id&lang=$lang", 'action_filter') .
           tab_tag('edit', $id, $active_tag, "@document_edit?module=$module&id=$id&lang=$lang", 'action_edit') .
           tab_tag('history', $id && $exists_in_lang, $active_tag, "@document_history?module=$module&id=$id&lang=$lang", 'action_list') .
           $comment_tag .
           '</ul></div>';
    }
}
