<?php 
use_helper('Language', 'Viewer', 'WikiTabs', 'Forum');

// define some PunBB constants and variable, and call some of it tools
if (!defined('PUN'))
    define('PUN', 1);
if (!defined('PUN_ROOT'))
    define('PUN_ROOT', sfConfig::get('sf_root_dir') . '/web/forums/');
if (!defined('PUN_STATIC_URL'))
    define('PUN_STATIC_URL', sfConfig::get('app_static_url'));


global $pun_config, $pun_user, $smiley_text, $smiley_img, $lang_common;
$pun_config = $pun_user = array();

$pun_config['o_indent_num_spaces'] = 4;
$pun_config['o_censoring'] = '0';
$pun_config['o_make_links'] = '1';
$pun_config['o_smilies'] = '1';
$pun_config['p_message_bbcode'] = '1';
$pun_config['p_message_img_tag'] = '1';
$pun_config['p_sig_img_tag'] = '0';

$pun_user['show_smilies'] = '1';
$pun_user['show_img'] = '1';
$pun_user['show_img_sig'] = '0';

$lang_common['Image link'] = 'image';

$module = $sf_context->getModuleName();
$lang = $sf_params->get('lang');
$id = $sf_params->get('id');

require_once PUN_ROOT . 'include/parser.php';

$nb_comments = $comments->count();

echo display_title(isset($title_prefix) ? $title_prefix.__('&nbsp;:').' '.$document_name : $document_name, $module);

echo '<div id="nav_space">&nbsp;</div>';
echo tabs_list_tag($id, $lang, $exists_in_lang, 'comments', NULL, formate_slug($search_name), $nb_comments);

echo display_content_top('doc_content');
echo start_content_tag($module . '_content');

if($nb_comments > 0):
$topic_id = $comments->getFirst()->topic_id;
$uri_anchor = explode('#', $_SERVER['REQUEST_URI'], 2);
$post_id = 0;
if (count($uri_anchor) > 1)
{
    $post_anchor =  $uri_anchor[1];
    if (strpos($post_anchor, 'p') === 0)
    {
        if (preg_match('#p([0-9]+)#si', $post_anchor, $post_id_match))
        {
            $post_id = intval($post_id_match[1]);
        }
    }
}
$is_new = ($post_id > 0) && isset($_GET['new']);
$bg_switch = true;	// Used for switching background color in posts
$counter = 1;
use_stylesheet(sfConfig::get('app_static_url') . '/static/css/forums.css');
?>

<div class="linkst">
  <div class="inbox">
    <p class="pagelink conl"><?php echo __('Number of comments: ') . $nb_comments; ?></p>
    <p class="postlink conr"><?php echo f_link_to(__('add a comment'), 'post.php?tid=' . $topic_id, array('rel' => 'nofollow')); ?></p>
  </div>
</div>

<?php
$post_id_list = array();
foreach ($comments as $comment)
{
    $post_id_list[] = $comment['id'];
}

foreach ($comments as $comment):
    // Switch the background color for every message.
    $bg_switch = ($bg_switch) ? $bg_switch = false : $bg_switch = true;
    $vtbg = ($bg_switch) ? ' roweven' : ' rowodd';
?>
<p>
<div id="p<?php echo $comment['id']; ?>" class="blockpost<?php
    echo $vtbg;
    if (($comment['id'] >= $post_id) && $is_new) echo ' new';
    if ($counter == 1) echo ' firstpost'; ?>">
        <h2><span><span class="conr">#<?php echo $counter ?>&nbsp;</span><a href="#p<?php echo $comment['id'].'">'.date('Y-m-d H:i:s',$comment['posted']) ?></a></span></h2>
        <div class="box">
            <div class="inbox"> 
                <div class="postleft">
                    <dl>
                        <dt>
                            <strong>
                            <?php
                                if ($comment['poster_id'] > 1) 
                                {
                                    echo link_to($comment['poster'],'users/'.$comment['poster_id']);
                                }
                                else
                                {
                                    echo $comment['poster'];
                                }
                            ?>
                            </strong>
                        </dt>
                        <dd class="usercontacts">
                            <?php
                                if ($comment['poster_id'] > 1) 
                                {
                                    echo f_link_to(__('Email short'),'misc.php?email='.$comment['poster_id'], array('rel' => 'nofollow'));
                                }
                                else if ($comment['poster_email'] != '')
                                {
                                    if ($sf_user->getId() > 1)
                                    {
                                        echo '<a href="mailto:'.$comment['poster_email'].'">'.__('Email short').'</a>';
                                    }
                                    else
                                    {
                                        echo '<span class="inactive" title="'.__('Reserved to logged users').'">'.__('Email short').'</span>';
                                    }
                                }
                                if ($sf_user->getId() > 1 && $comment['poster_id'] > 1)
                                {
                                    echo '&nbsp; '.f_link_to(__('PM'),'message_send.php?id='.$comment['poster_id'].'&amp;pid='.$comment['id']);
                                }
                            ?>
                        </dd>
                    </dl>
                </div>
                <div class="postright">
                    <div class="postmsg">
                        <p>
                        <?php
                            $text = $comment->message;
                            $text = parse_message($text, false, $post_id_list);
                            $text = htmlspecialchars_decode($text); // parse_message always use html_special_chars, and so does retrieval of the text
                            echo $text;
                            ?>
                        </p>
                    </div>
                </div>
                <div class="clearer"></div>
                <div class="postfootright">
                    <ul><?php
    if ($sf_user->getId() > 1 || $sf_user->hasCredential('moderator'))
    {
        echo '<li class="postreport">' . f_link_to(__('Report'),'misc.php?report='.$comment->id).' | ';
    }
    else
    {
        echo '<li class="postreport">' . f_link_to(__('Report'),'misc.php?email='.sfConfig::get('app_moderator_forum_user_id')
             .'&doc='.urlencode('/forums/viewtopic.php?pid='.$comment->id.'#p'.$comment->id)).' | ';
    }

    if ($sf_user->hasCredential('moderator'))
    {
        echo '</li><li class="movepost">' . f_link_to(__('Move'),'movepost.php?id='.$comment->id).' | ';
    }
    if ($comment['poster_id'] == $sf_user->getId() || $sf_user->hasCredential('moderator'))
    {
        echo '</li><li class="postedit">' . f_link_to(__('Edit'),'edit.php?id='.$comment->id).' | ';
    }
    echo '</li><li class="postquote">' . f_link_to(__('Quoted reply'),'post.php?tid='.$topic_id.'&amp;'.'qid='.$comment->id, array('rel' => 'nofollow')).'</li>';
    ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </p>
<?php
    $counter++;
endforeach;
?>
<div class="linkst">
  <div class="inbox">
    <p class="pagelink conl"><?php echo __('Number of comments: ') . $nb_comments; ?></p>
    <p class="postlink conr"><?php echo f_link_to(__('add a comment'), 'post.php?tid=' . $topic_id, array('rel' => 'nofollow')); ?></p>
<?php
if ($sf_user->getId() > 1):
?>
    <p class="subscribelink clearb"><?php echo f_link_to(__('Subscribe to this document comments'), 'misc.php?subscribe=' . $topic_id); ?></p>
    <p class="subscribelink clearb"><?php echo f_link_to(__('Unsubscribe to this document comments'), 'misc.php?unsubscribe=' . $topic_id); ?></p>
<?php
endif;
?>
    <div class="clearer"></div>
  </div>
</div>

<?php else :
          echo f_link_to(__('add a comment'), 'post.php?fid=1&subject=' . $id . '_' . $lang, array('class' => 'add_content', 'rel' => 'nofollow'));
          echo '<br /><br /><br /><br /><br />';
      endif;

echo end_content_tag();

include_partial('common/content_bottom') ?>
