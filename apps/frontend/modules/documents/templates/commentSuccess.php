<?php 
use_helper('Language', 'Viewer', 'WikiTabs', 'Forum');

// define some PunBB constants and call some of it tools
if (!defined('PUN'))
    define('PUN', 1);
if (!defined('PUN_ROOT'))
    define('PUN_ROOT', sfConfig::get('sf_root_dir') . '/web/forums/');
require_once 'web/forums/include/parser.php';

$module = $sf_context->getModuleName();
$lang = $sf_params->get('lang');
$id = $sf_params->get('id');


$nb_comments = $comments->count();

echo display_title($document_name, $module);
echo '<div id="nav_space">&nbsp;</div>';
echo tabs_list_tag($id, $lang, $exists_in_lang, 'comments'); ?>

<div id="wrapper_context">
<div id="ombre_haut">
    <div id="ombre_haut_corner_right"></div>
    <div id="ombre_haut_corner_left"></div>
</div>

<div id="content_article">
<div id="article">

<?php
if($nb_comments > 0):
$topic_id = $comments->getFirst()->topic_id;
$counter = 1;
use_stylesheet('/forums/style/Oxygen');
?>

<div class="linkst">
  <div class="inbox">
    <p class="pagelink conl"><?php echo __('Number of comments: ') . $nb_comments; ?></p>
    <p class="postlink conr"><?php echo f_link_to(__('add a comment'), 'post.php?tid=' . $topic_id); ?></p>
  </div>
</div>

<?php
    foreach ($comments as $comment):
?>
<p>
<div id="p<?php echo $comment['id']; ?>" class="blockpost rowodd firstpost">
        <h2><span><span class="conr">#<?php echo $counter ?>&nbsp;</span><?php echo date('Y-m-d H:i:s',$comment['posted']) ?></span></h2>
        <div class="box">
            <div class="inbox"> 
                <div class="postleft">
                    <dl>
                        <dl>
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
                        </dl>
                        <dl class="usercontacts" style="display:block;">
                            <?php
                                if ($comment['poster_id'] > 1) 
                                {
                                    echo f_link_to(__('Send mail'),'misc.php?email='.$comment['poster_id']);
                                }
                                else if ($comment['poster_email'] != '')
                                {
                                    echo '<a href="mailto:'.$comment['poster_email'].'">'.__('Send mail').'</a>';
                                }
                                if ($sf_user->getId() > 1 && $comment['poster_id'] > 1)
                                {
                                    echo '&nbsp; '.f_link_to(__('Private message'),'message_send.php?id='.$comment['poster_id']);
                                }
                            ?>
                        </dl>
                    </dl>
                </div>
                <div class="postright">
                    <div class="postmsg">
                        <p>
                        <?php
                            $text = ' ' . $comment->message . ' ';
                            $num_smilies = count($smiley_text);

                            $text = do_bbcode(parse_message(preparse_bbcode($text, $error), false));
                            $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
                            for ($i = 0; $i < $num_smilies; ++$i)
                            {
                                $text = preg_replace("#(?<=.\W|\W.|^\W)" 
                                                     . preg_quote($smiley_text[$i], '#')
                                                     . "(?=.\W|\W.|\W$)#m",
                                                     '$1<img src="/forums/img/smilies/'.$smiley_img[$i].'"
                                                             width="15"
                                                             height="15"
                                                             alt="'.substr($smiley_img[$i],
                                                                           0,
                                                                           strrpos($smiley_img[$i],
                                                                           '.')).'"
                                                        />$2', $text);
                            }
                            echo $text;
                            ?>
                        </p>
                    </div>
                </div>
                <div class="clearer"></div>
                <div class="postfootright">
                    <ul><li class="postreport"><?php
    echo f_link_to(__('Report'),'misc.php?report='.$comment->id);
    if ($sf_user->hasCredential('moderator'))
    {
        echo ' | </li><li class="postdelete">' . f_link_to(__('Delete'),'delete.php?id='.$comment->id);
    }
    if ($comment['poster_id'] == $sf_user->getId() || $sf_user->hasCredential('moderator'))
    {
        echo ' | </li><li class="postedit">' . f_link_to(__('Edit'),'edit.php?id='.$comment->id);
    }
    ?></li>
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
    <p class="postlink conr"><?php echo f_link_to(__('add a comment'), 'post.php?tid=' . $topic_id); ?></p>
    <div class="clearer"></div>
  </div>
</div>

<?php else :
          echo f_link_to(__('add a comment'), 'post.php?fid=1&subject=' . $id . '_' . $lang, array('class' => 'add_content'));
          echo '<br /><br /><br /><br /><br />';
      endif;
?>
</div>
</div>

<?php include_partial('common/content_bottom') ?>
