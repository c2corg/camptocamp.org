<?php
use_helper('Link');

echo start_section_tag('Comments', 'comments');

$module = sfContext::getInstance()->getModuleName();

echo '<p>', picto_tag('action_comment'), ' ',
     format_number_choice('[0]No comment|[1]1 comment|(1,+Inf]%1% comments', array('%1%' => $nb_comments), $nb_comments).'</p>';
if ($nb_comments)
{
  $link = '<p>'.link_to(__('comments_tab_help'), "@document_comment?module=$module&id=$id&lang=$lang").'</p>';
}
else
{
    // check if anonymous users can create comments
    if (!sfContext::getInstance()->getUser()->isConnected() && !in_array($lang, sfConfig::get('app_anonymous_comments_allowed_list')))
    {
        $link = '';
    }
    else
    {
        $link = '<p>'.content_tag('a', __('comments_tab_help'), array('href' => '/forums/post.php?fid=1&subject=' . $id . '_' . $lang)).'</p>';
    }
}

echo $link;

echo end_section_tag();
