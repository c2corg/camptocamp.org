<?php
use_helper('Forum','Button', 'ModalBox', 'General');

$c2c_news_forum = PunbbTopics::getC2cNewsForumId($lang);
$sublevel_ie7 = '<!--[if gte IE 7]><!-->';
$sublevel_start = '<!--<![endif]--> <!--[if lte IE 6]><table><tr><td><![endif]-->';
$sublevel_end = '<!--[if lte IE 6]></td></tr></table></a><![endif]-->';
?>

<script type="text/javascript"><!--
hide_select = function() {}; show_select = function() {};
//--></script>
<!--[if lt IE 7]>
<script type="text/javascript" src="<?php echo sfConfig::get('app_static_url') . '/' . sfTimestamp::getTimestamp('/static/js/menus.js') ?>/static/js/menus.js"></script>
<script>
Event.observe(window,'load',startList);var selectList=document.getElementsByTagName('select');hide_select=function()
{if(selectList)
{var len=selectList.length;if(len>0)
{for(i=0;i<len;i++)
{selectList[i].style.display='none';}}}}
show_select=function()
{if(selectList)
{var len=selectList.length;if(len>0)
{for(i=0;i<len;i++)
{selectList[i].style.display='';}}}}
</script>
<![endif]-->

    <div id="menu_content">
        <ul onmouseover="hide_select();" onmouseout="show_select();">
            <li><?php
                echo link_to(__('Home') . $sublevel_ie7, '@homepage')
                   . $sublevel_start ?>
                <ul>
                    <li><?php
                        echo picto_tag('action_informations')
                           . link_to(__('Kesako?'), getMetaArticleRoute('know_more', false)) ?></li>
                    <li><?php
                        echo picto_tag('action_help')
                           . link_to(__('FAQ short'), getMetaArticleRoute('faq', false)) ?></li>
                    <li class="lilast"><?php
                        echo picto_tag('action_help')
                           . link_to(__('Global help'), getMetaArticleRoute('help', false), array('class'=>'ie7m')) ?></li>
                </ul><?php echo $sublevel_end ?>
            </li>
            <li><?php
                echo link_to(__('Guidebook') . $sublevel_ie7, getMetaArticleRoute('help_guide', false))
                   . $sublevel_start ?>
                <ul>
	                <li><?php
                        echo picto_tag('picto_maps')
                           . link_to(__('Map tool'), '@map') ?></li>
                    <li><?php
                        echo picto_tag('picto_outings')
                           . link_to(__('outings') . $sublevel_ie7, '@default_index?module=outings')
                           . $sublevel_start ?>
                        <ul>
                            <li><?php
                                echo picto_tag('action_list')
                                   . link_to(__('cond short'), 'outings/conditions') ?></li>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=outings') ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php
                                echo picto_tag('action_create')
                                   . m_link_to(__('Add'), 'outings/wizard',
                                               array('title'=> __('Create new outing with some help')),
                                               array('width' => 600)) ?></li>
                            <?php endif ?>
                        </ul> <?php echo $sublevel_end ?>
                    </li>
                    <li><?php echo picto_tag('picto_routes')
                                 . link_to(__('routes') . $sublevel_ie7, '@default_index?module=routes')
                                 . $sublevel_start ?>
                        <ul>
                            <li class="lilast"><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=routes') ?></li>
                            <li><?php
                                echo picto_tag('picto_tools')
                                   . m_link_to(__('cotometre'), '@tool?action=cotometre',
                                               array('title'=> __('cotometre long')),
                                               array('width' => 600)) ?></li>
                        </ul><?php echo $sublevel_end ?>
                    </li>
                    <li><?php echo picto_tag('picto_summits')
                                 . link_to(__('summits') . $sublevel_ie7, '@default_index?module=summits', array('class'=>'ie7m'))
                                 . $sublevel_start ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=summits') ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php
                                echo picto_tag('action_create')
                                   . link_to(__('Add'), '@document_edit?module=summits&id=&lang=') ?></li>
                            <?php endif ?>
                        </ul><?php echo $sublevel_end ?>
                    </li>
                    <li><?php
                        echo picto_tag('picto_sites')
                           . link_to(__('sites') . $sublevel_ie7, '@default_index?module=sites', array('class'=>'ie7m'))
                           . $sublevel_start ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=sites') ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php
                                echo picto_tag('action_create')
                                   . link_to(__('Add'), '@document_edit?module=sites&id=&lang=') ?></li>
                            <?php endif ?>
                        </ul><?php echo $sublevel_end ?>
                    </li>
                    <li><?php
                        echo picto_tag('picto_parkings')
                           . link_to(__('parkings') . $sublevel_ie7, '@default_index?module=parkings')
                           . $sublevel_start ?>
                        <ul>
                            <li><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=parkings') ?></li>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php
                                echo picto_tag('picto_portals')
                                ?><a href="http://<?php echo sfConfig::get('app_changerdapproche_host') ?>/" class="ie7m"><?php echo __('changerdapproche') ?></a></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php
                                echo picto_tag('action_create')
                                   . link_to(__('Add'), '@document_edit?module=parkings&id=&lang=') ?></li>
                            <?php endif ?>
                        </ul> <?php echo $sublevel_end ?>
                    </li>
                    <li><?php
                        echo picto_tag('picto_huts')
                           . link_to(__('huts') . $sublevel_ie7, '@default_index?module=huts', array('class'=>'ie7m'))
                           . $sublevel_start ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=huts') ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php
                                echo picto_tag('action_create')
                                   . link_to(__('Add'), '@document_edit?module=huts&id=&lang=') ?></li>
                            <?php endif ?>
                        </ul> <?php echo $sublevel_end ?>
                    </li>
                    <li><?php
                        echo picto_tag('picto_products')
                           . link_to(__('products') . $sublevel_ie7, '@default_index?module=products', array('class'=>'ie7m'))
                           . $sublevel_start ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=products') ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php
                                echo picto_tag('action_create')
                                   . link_to(__('Add'), '@document_edit?module=products&id=&lang=') ?></li>
                            <?php endif ?>
                        </ul><?php echo $sublevel_end ?>
                    </li>
                    <li><?php
                        echo picto_tag('picto_books')
                           . link_to(__('books') . $sublevel_ie7, '@default_index?module=books') ?>
                        <?php echo $sublevel_start ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=books') ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php
                                echo picto_tag('action_create')
                                   . link_to(__('Add'), '@document_edit?module=books&id=&lang=') ?></li>
                            <?php endif ?>
                        </ul> <?php echo $sublevel_end ?>
                    </li>
                    <li class="lilast"><?php
                        echo picto_tag('action_help')
                           . link_to(__('Help'), getMetaArticleRoute('help_guide', false)) ?></li>
                </ul><?php echo $sublevel_end ?>
            </li>
            <li><?php
                echo link_to(__('Articles') . $sublevel_ie7, '@default_index?module=articles')
                   . $sublevel_start ?>
                <ul>
                    <li><?php echo picto_tag('action_list')
                                 . link_to(ucfirst(__('Summary')), getMetaArticleRoute('home_articles', false)) ?></li>
                    <li><?php echo picto_tag('picto_articles')
                                 . link_to(ucfirst(__('mountain environment')), 'articles/list?ccat=1', array('class'=>'ie7m')) ?></li>
                    <li><?php echo picto_tag('picto_articles')
                                 . link_to(ucfirst(__('gear and technique')), 'articles/list?ccat=2', array('class'=>'ie7m')) ?></li>
                    <li><?php echo picto_tag('picto_articles')
                                 . link_to(ucfirst(__('topoguide supplements')), 'articles/list?ccat=4', array('class'=>'ie7m')) ?></li>
                    <li><?php echo picto_tag('picto_articles')
                                 . link_to(ucfirst(__('soft mobility')), 'articles/list?ccat=7', array('class'=>'ie7m')) ?></li>
                    <li><?php echo picto_tag('picto_articles')
                                 . link_to(ucfirst(__('expeditions')), 'articles/list?ccat=8', array('class'=>'ie7m')) ?></li>
                    <li><?php echo picto_tag('picto_articles')
                                 . link_to(ucfirst(__('stories')), 'articles/list?ccat=3', array('class'=>'ie7m')) ?></li>
                    <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php
                        echo picto_tag('action_query')
                           . link_to(__('Search'), '@filter?module=articles') ?></li>
                    <?php if ($is_connected): ?>
                    <li class="lilast"><?php
                        echo picto_tag('action_create')
                           . link_to(__('Add'), '@document_edit?module=articles&id=&lang=') ?></li>
                    <?php endif ?>
                </ul><?php echo $sublevel_end ?>
            </li>
            <li><?php
                echo link_to(__('Gallery') . $sublevel_ie7, '@default_index?module=images')
                   . $sublevel_start ?>
                <ul>
                    <li class="lilast"><?php
                        echo picto_tag('action_query')
                           . link_to(__('Search'), '@filter?module=images') ?></li>
                </ul><?php echo $sublevel_end ?>
            </li>
            <li><?php
                echo link_to(__('Community'), getMetaArticleRoute('association')) ?>
                <ul>
                    <li><?php
                        echo picto_tag('action_people')
                           . link_to(__('Association') . $sublevel_ie7, getMetaArticleRoute('association'))
                           . $sublevel_start ?>
                        <ul>
                            <li class="lilast"><?php
                            echo picto_tag('action_comment')
                               . f_link_to(__('c2corg news'), 'viewforum.php?id=' . $c2c_news_forum, array('class'=>'ie7m')) ?></li>
                        </ul><?php echo $sublevel_end ?>
                    </li>
                    <li><?php
                        echo picto_tag('picto_users')
                           . link_to(ucfirst(__('users')) . $sublevel_ie7, '@default_index?module=users'), $sublevel_start ?>
                        <ul>
                            <li class="lilast"><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=users') ?></li>
                        </ul><?php echo $sublevel_end ?>
                    </li>
                    <li><?php
                        echo picto_tag('action_list')
                           . link_to(__('Shop'), getMetaArticleRoute('shop')) ?></li>
                    <li><?php
                        echo picto_tag('action_list')
                           . link_to(__('c2c website'), getMetaArticleRoute('website_presentation')) ?></li>
                    <li class="lilast"><?php
                        echo picto_tag('action_list')
                           . link_to(__('credits'), getMetaArticleRoute('credits')) ?></li>
                </ul>
            </li>
            <li><?php
                echo link_to(__('My Camptocamp'), $is_connected ? 'users/mypage' : getMetaArticleRoute('create_account')) ?>
                <ul>
                    <?php if ($is_connected): ?>
                    <li><?php
                        echo picto_tag('picto_users')
                           . link_to(__('personal page'), 'users/mypage') ?></li>
                    <?php endif ?>
                    <li><?php
                        echo picto_tag('picto_tools')
                           . m_link_to(__('Customize'), 'users/customize', array('class' => 'ie7m', 'title' => __('Customize the site')), array('width' => 700)) ?></li>
                    <?php if ($is_connected): ?>
                    <li><?php
                        echo picto_tag('picto_tools')
                           . m_link_to(__('Set languages preferences'), 'users/sortPreferedLanguages', array('class' => 'ie7m'), array('width' => 700)) ?></li>
                    <li><?php
                        echo picto_tag('action_edit')
                           . f_link_to(__('User profile'), 'profile.php?section=personality') ?></li>
                    <li><?php
                        echo picto_tag('action_contact')
                           . link_to(__('Mailing lists link'), 'users/mailinglists', array('class'=>'ie7m')) ?></li>
                <?php else: ?>
                    <li><?php
                        echo picto_tag('action_edit')
                           . link_to(__('create an account?'), getMetaArticleRoute('create_account')) ?></li>
                    <?php endif;
                    if ($is_connected): ?>
                    <li class="lilast"><?php
                        echo picto_tag('action_cc')
                           . link_to(__('User image management'), 'users/manageimages', array('class'=>'ie7m')) ?></li>
                    <?php endif ?>
                </ul>
            </li>
            <li id="menulast"><?php
                echo f_link_to(__('Forum') . $sublevel_ie7, '?lang='. $lang), $sublevel_start ?>
                <ul>
                    <li><?php
                        $languages = Language::getAll();
                        echo picto_tag('action_comment'),
                             f_link_to(__('All topics'), ''),
                             $sublevel_start ?>
                        <ul>
                            <?php $last_item = end($languages);
                            reset($languages);
                            foreach ($languages as $key => $value): ?>
                            <li<?php if ($value == $last_item): ?> class="lilast"<?php endif ?>><?php
                                echo picto_tag('action_comment')
                                   . f_link_to(__($value), '?lang=' . $key) ?></li>
                            <?php endforeach ?>
                        </ul><?php echo $sublevel_end ?>
                    </li>
                    <li><?php
                        echo picto_tag('action_query')
                           . f_link_to(__('Search'), 'search.php') ?></li>
                    <li><?php
                        echo picto_tag('action_list');
                        if ($is_connected)
                        {
                            echo f_link_to(__('New posts'), 'search.php?action=show_new&lang='.$lang);
                        }
                        else
                        {
                           echo f_link_to(__('Recent posts'), 'search.php?action=show_24h&lang='.$lang);
                        }
                    ?></li>
                    <?php if ($is_connected): ?>
                    <li><?php
                        echo picto_tag('action_list')
                           . f_link_to(__('My topics'), 'search.php?action=show_user') ?></li>
                    <li><?php
                        echo picto_tag('action_edit')
                           . f_link_to(__('User profile'), 'profile.php?section=personality') ?></li>
                    <?php endif ?>
                    <li><?php
                        echo picto_tag('action_help')
                           . link_to(__('Help'), getMetaArticleRoute('help_forum', false)) ?></li>
                    <li class="lilast"><?php
                        echo picto_tag('action_help')
                           . link_to(__('Charte'), getMetaArticleRoute('charte_forum', false)) ?></li>
                </ul><?php echo $sublevel_end ?>
            </li>
        </ul>
        <br class="clearer" />
    </div>
