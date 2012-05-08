<?php
use_helper('Forum','Button', 'Link', 'ModalBox', 'General');

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
                           . link_to(__('Kesako?'), getMetaArticleRoute('know_more')) ?></li>
                    <li><?php
                        echo picto_tag('action_help')
                           . link_to(__('FAQ short'), getMetaArticleRoute('faq')) ?></li>
                    <li class="lilast"><?php
                        echo picto_tag('action_help')
                           . link_to(__('Global help'), getMetaArticleRoute('help')) ?></li>
                </ul><?php echo $sublevel_end ?>
            </li>
            <li><?php
                echo link_to(__('Guidebook / Map') . $sublevel_ie7, getMetaArticleRoute('help_guide'))
                   . $sublevel_start ?>
                <ul>
	                <li><?php
                        echo picto_tag('picto_maps')
                           . link_to(__('Map tool'), '@map') ?></li>
                    <li><?php
                        echo picto_tag('picto_outings')
                           . link_to(ucfirst(__('outings')) . $sublevel_ie7, '@default_index?module=outings')
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
                                echo picto_tag('action_create');
                                if ($is_connected)
                                {
                                    echo m_link_to(__('Add'), 'outings/wizard',
                                                   array('title'=> __('Create new outing with some help')),
                                                   array('width' => 600));
                                }
                                else
                                {
                                    echo m_link_to(__('Add'), 'outings/wizard',
                                                   array('title'=> __('Create new outing unconnected'),
                                                         'url' => '@login_redirect?outings_wizard'),
                                                   array('width' => 600));
                                }
                            ?></li>
                            <?php endif ?>
                        </ul> <?php echo $sublevel_end ?>
                    </li>
                    <li><?php echo picto_tag('picto_routes')
                                 . link_to(ucfirst(__('routes')) . $sublevel_ie7, '@default_index?module=routes')
                                 . $sublevel_start ?>
                        <ul>
                            <li><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=routes') ?></li>
                            <?php if ($is_connected): ?>
                            <li><?php
                                echo picto_tag('action_create')
                                   . link_to(__('Add'), getMetaArticleRoute('help_guide', false, 'route')) ?></li>
                            <?php endif ?>
                            <li class="lilast"><?php
                                echo picto_tag('picto_tools')
                                   . m_link_to(__('cotometre'), '@tool?action=cotometre',
                                               array('title'=> __('cotometre long')),
                                               array('width' => 600)) ?></li>
                        </ul><?php echo $sublevel_end ?>
                    </li>
                    <li><?php echo picto_tag('picto_summits')
                                 . link_to(ucfirst(__('summits')) . $sublevel_ie7, '@default_index?module=summits')
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
                           . link_to(ucfirst(__('sites')) . $sublevel_ie7, '@default_index?module=sites')
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
                           . link_to(ucfirst(__('parkings')) . $sublevel_ie7, '@default_index?module=parkings')
                           . $sublevel_start ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=parkings') ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php
                                echo picto_tag('action_create')
                                   . link_to(__('Add'), '@document_edit?module=parkings&id=&lang=') ?></li>
                            <?php endif ?>
                        </ul> <?php echo $sublevel_end ?>
                    </li>
                    <li><?php
                        echo picto_tag('picto_huts')
                           . link_to(ucfirst(__('huts')) . $sublevel_ie7, '@default_index?module=huts')
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
                           . link_to(ucfirst(__('products')) . $sublevel_ie7, '@default_index?module=products')
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
                           . link_to(ucfirst(__('books')) . $sublevel_ie7, '@default_index?module=books') ?>
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
                    <li><?php
                        echo picto_tag('picto_portals')
                           . link_to(ucfirst(__('portals')) . $sublevel_ie7, '@default_index?module=portals') ?>
                        <?php echo $sublevel_start ?>
                        <ul>
                            <li><?php
                                $cda_config = sfConfig::get('app_portals_cda');
                                echo picto_tag('picto_portals')
                                   . link_to(__($cda_config['name']), '@document_by_id?module=portals&id=' . $cda_config['id']) ?></li>
                            <li><?php
                                $ice_config = sfConfig::get('app_portals_ice');
                                echo picto_tag('picto_portals')
                                   . link_to(__($ice_config['name']), '@document_by_id?module=portals&id=' . $ice_config['id']) ?></li>
                            <li><?php
                                $steep_config = sfConfig::get('app_portals_steep');
                                echo picto_tag('picto_portals')
                                   . link_to(__($steep_config['name']), '@document_by_id?module=portals&id=' . $steep_config['id']) ?></li>
                            <li><?php
                                $raid_config = sfConfig::get('app_portals_raid');
                                echo picto_tag('picto_portals')
                                   . link_to(__($raid_config['name']), '@document_by_id?module=portals&id=' . $raid_config['id']) ?></li>
                            <li class="lilast"><?php
                                $pyrenees_config = sfConfig::get('app_portals_pyrenees');
                                echo picto_tag('picto_portals')
                                   . link_to(__($pyrenees_config['name']), '@document_by_id?module=portals&id=' . $pyrenees_config['id']) ?></li>
                        </ul> <?php echo $sublevel_end ?>
                    </li>
                    <li class="lilast"><?php
                        echo picto_tag('action_help')
                           . link_to(__('Help'), getMetaArticleRoute('help_guide')) ?></li>
                </ul><?php echo $sublevel_end ?>
            </li>
            <li><?php
                echo link_to(__('Articles') . $sublevel_ie7, '@default_index?module=articles')
                   . $sublevel_start ?>
                <ul>
                    <li><?php echo picto_tag('action_list')
                                 . link_to(ucfirst(__('Summary')), getMetaArticleRoute('home_articles')) ?></li>
                    <li><?php echo picto_tag('picto_articles')
                                 . link_to(ucfirst(__('mountain environment')), 'articles/list?ccat=1') ?></li>
                    <li><?php echo picto_tag('picto_articles')
                                 . link_to(ucfirst(__('gear and technique')), 'articles/list?ccat=2') ?></li>
                    <li><?php echo picto_tag('picto_articles')
                                 . link_to(ucfirst(__('topoguide supplements')), 'articles/list?ccat=4') ?></li>
                    <li><?php echo picto_tag('picto_articles')
                                 . link_to(ucfirst(__('soft mobility')), 'articles/list?ccat=7') ?></li>
                    <li><?php echo picto_tag('picto_articles')
                                 . link_to(ucfirst(__('expeditions')), 'articles/list?ccat=8') ?></li>
                    <li><?php echo picto_tag('picto_articles')
                                 . link_to(ucfirst(__('stories')), 'articles/list?ccat=3') ?></li>
                    <li><?php
                        echo picto_tag('action_query')
                           . link_to(__('Search'), '@filter?module=articles') ?></li>
                    <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php
                        echo picto_tag('action_help')
                           . link_to(__('Help'), getMetaArticleRoute('help_articles')) ?></li>
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
                    <li><?php
                        echo picto_tag('picto_images')
                           . link_to(__('Collaborative images'), 'images/list?ityp=1') ?></li>
                    <li><?php
                        echo picto_tag('action_query')
                           . link_to(__('Search'), '@filter?module=images') ?></li>
                    <li class="lilast"><?php
                        echo picto_tag('action_help')
                           . link_to(__('Help'), getMetaArticleRoute('help_images')) ?></li>
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
                               . f_link_to(__('c2corg news'), 'viewforum.php?id=' . $c2c_news_forum) ?></li>
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
                    <li><?php
                        echo picto_tag('picto_outings')
                           . link_to(__('My outings'), 'outings/myoutings') ?></li>
                    <li><?php
                        echo picto_tag('picto_images')
                           . link_to(__('My images'), 'images/myimages') ?></li>
                    <?php endif ?>
                    <li><?php
                        echo picto_tag('picto_tools')
                           . customize_link_to() ?></li>
                    <?php if ($is_connected): ?>
                    <li><?php
                        echo picto_tag('picto_tools')
                           . personal_preferences_link_to() ?></li>
                    <li><?php
                        echo picto_tag('picto_tools')
                           . language_preferences_link_to() ?></li>
                    <li><?php
                        echo picto_tag('action_edit')
                           . f_link_to(__('User profile'), 'profile.php?section=personality') ?></li>
                    <li><?php
                        echo picto_tag('action_contact')
                           . link_to(__('Mailing lists link'), 'users/mailinglists') ?></li>
                <?php else: ?>
                    <li><?php
                        echo picto_tag('action_edit')
                           . link_to(__('create an account?'), getMetaArticleRoute('create_account')) ?></li>
                    <?php endif;
                    if ($is_connected): ?>
                    <li class="lilast"><?php
                        echo picto_tag('action_cc')
                           . link_to(__('User image management'), 'users/manageimages') ?></li>
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
                        echo picto_tag('action_create')
                           . link_to(__('Add a post'), getMetaArticleRoute('help_forum', false, 'create-topic')) ?></li>
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
                           . link_to(__('Help'), getMetaArticleRoute('help_forum')) ?></li>
                    <li class="lilast"><?php
                        echo picto_tag('action_help')
                           . link_to(__('Charte'), getMetaArticleRoute('charte_forum')) ?></li>
                </ul><?php echo $sublevel_end ?>
            </li>
        </ul>
        <br class="clearer" />
    </div>
