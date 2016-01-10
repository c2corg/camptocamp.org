<?php
use_helper('Forum','Button', 'Link', 'ModalBox', 'General');

$c2c_news_forum = PunbbTopics::getC2cNewsForumId($lang);
?>
    <div id="menu_content">
        <ul>
            <li><?php echo link_to(__('Home'), '@homepage') ?>
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
                </ul>
            </li>
            <li><?php echo link_to(__('Guidebook / Map'), getMetaArticleRoute('help_guide')) ?>
                <ul>
	            <li><?php
                        echo picto_tag('picto_maps')
                           . link_to(__('Map tool'), '@map') ?></li>
                    <li><?php
                        echo picto_tag('picto_outings')
                           . link_to(ucfirst(__('outings')), '@default_index?module=outings') ?>
                        <ul>
                            <li><?php
                                echo picto_tag('action_create');
                                if ($is_connected)
                                {
                                    echo m_link_to(__('Add'), 'outings/wizard',
                                                   array('title'=> __('Create new outing with some help')),
                                                   array('width' => 600));
                                }
                                else
                                {
                                    echo m_link_to(__('Add'), '@login',
                                                   array('title' => __('Create new outing unconnected'),
                                                         'query_string' => 'redirect=outings/wizard'),
                                                   array('width' => 600));
                                }
                            ?></li>
                            <li><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=outings') ?></li>
                            <li><?php
                                echo picto_tag('action_list')
                                   . link_to(__('cond short'), '@default?module=outings&action=conditions&orderby=date&order=desc') ?></li>
                            <li class="lilast"><?php
                                echo picto_tag('action_list')
                                   . link_to(__('avalanche_infos_short'), '@default?module=outings&action=conditions&avdate=2-3-4-5&date=2W&perso=areas-cult-ifon&orderby=date&order=desc') ?></li>
                        </ul>
                    </li>
                    <li><?php echo picto_tag('picto_routes')
                                 . link_to(ucfirst(__('routes')), '@default_index?module=routes') ?>
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
                        </ul>
                    </li>
                    <li><?php echo picto_tag('picto_summits')
                                 . link_to(ucfirst(__('summits')), '@default_index?module=summits') ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=summits') ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php
                                echo picto_tag('action_create')
                                   . link_to(__('Add'), '@document_edit?module=summits&id=&lang=') ?></li>
                            <?php endif ?>
                        </ul>
                    </li>
                    <li><?php
                        echo picto_tag('picto_sites')
                           . link_to(ucfirst(__('sites')), '@default_index?module=sites') ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=sites') ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php
                                echo picto_tag('action_create')
                                   . link_to(__('Add'), '@document_edit?module=sites&id=&lang=') ?></li>
                            <?php endif ?>
                        </ul>
                    </li>
                    <li><?php
                        echo picto_tag('picto_xreports')
                           . link_to(ucfirst(__('xreports')), '@default_index?module=xreports') ?>
                        <ul>
                            <li><?php
                                echo picto_tag('action_create');
                                if ($is_connected)
                                {
                                    echo link_to(__('Add'), '@document_edit?module=xreports&id=&lang=');
                                }
                                else
                                {
                                    echo m_link_to(__('Add'), '@login',
                                                   array('title' => __('Create new xreport unconnected'),
                                                         'query_string' => 'redirect=xreports/edit'),
                                                   array('width' => 600));
                                }
                            ?></li>
                            <li><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=xreports') ?></li>
                        </ul>
                    </li>
                    <li><?php
                        echo picto_tag('picto_parkings')
                           . link_to(ucfirst(__('parkings')), '@default_index?module=parkings') ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=parkings') ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php
                                echo picto_tag('action_create')
                                   . link_to(__('Add'), '@document_edit?module=parkings&id=&lang=') ?></li>
                            <?php endif ?>
                        </ul>
                    </li>
                    <li><?php
                        echo picto_tag('picto_huts')
                           . link_to(ucfirst(__('huts')), '@default_index?module=huts') ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=huts') ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php
                                echo picto_tag('action_create')
                                   . link_to(__('Add'), '@document_edit?module=huts&id=&lang=') ?></li>
                            <?php endif ?>
                        </ul>
                    </li>
                    <li><?php
                        echo picto_tag('picto_products')
                           . link_to(ucfirst(__('products')), '@default_index?module=products') ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=products') ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php
                                echo picto_tag('action_create')
                                   . link_to(__('Add'), '@document_edit?module=products&id=&lang=') ?></li>
                            <?php endif ?>
                        </ul>
                    </li>
                    <li><?php
                        echo picto_tag('picto_books')
                           . link_to(ucfirst(__('books')), '@default_index?module=books') ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=books') ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php
                                echo picto_tag('action_create')
                                   . link_to(__('Add'), '@document_edit?module=books&id=&lang=') ?></li>
                            <?php endif ?>
                        </ul>
                    </li>
                    <li><?php
                        echo picto_tag('action_filter')
                           . link_to(ucfirst(__('tags')), 'articles/list?ccat=10') ?></li>
                    <li class="lilast"><?php
                        echo picto_tag('action_help')
                           . link_to(__('Help'), getMetaArticleRoute('help_guide')) ?></li>
                </ul>
            </li>
            <li><?php
                echo link_to(__('Articles'), '@default_index?module=articles') ?>
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
                </ul>
            </li>
            <li><?php
                echo link_to(__('Gallery'), '@default_index?module=images') ?>
                <ul>
                    <li><?php
                        echo picto_tag('action_query')
                           . link_to(__('Search'), '@filter?module=images') ?></li>
                    <li><?php
                        echo picto_tag('picto_images')
                           . link_to(__('Collaborative images'), 'images/list?ityp=1') ?></li>
                    <li><?php
                        $video_tag_article = sfConfig::get('app_tags_video');
                        echo picto_tag('picto_movie')
                           . link_to(__('Outings with video'), 'outings/list?otags='.
                           $video_tag_article['id'].'&orderby=date&order=desc')  ?></li>
                    <li class="lilast"><?php
                        echo picto_tag('action_help')
                           . link_to(__('Help'), getMetaArticleRoute('help_images')) ?></li>
                </ul>
            </li>
            <li><?php
                echo link_to(ucfirst(__('portals')), '@default_index?module=portals') ?>
                <ul><?php
                    $portal_list = sfConfig::get('app_portals_id');
                    foreach ($portal_list as $portal_id)
                    {
                        $portal_config = sfConfig::get('app_portals_' . $portal_id);
                        if (isset($portal_config['menu']) && $portal_config['menu'])
                        {
                            if (isset($portal_config['url']))
                            {
                                $portal_url = 'http://' . $portal_config['url'];
                            }
                            else
                            {
                                $portal_url = '@document_by_id?module=portals&id=' . $portal_config['id'];
                            }
                            echo '<li>'
                               , picto_tag('picto_portals')
                               , link_to(__($portal_config['name']), $portal_url)
                               , '</li>';
                        }
                    } ?>
                </ul>
            </li>
            <li><?php
                echo f_link_to(__('Forum'), '?lang='. $lang) ?>
                <ul>
                    <li><?php
                        $languages = Language::getAll();
                        echo picto_tag('action_comment'),
                             f_link_to(__('All topics'), '') ?>
                        <ul>
                            <?php $last_item = end($languages);
                            reset($languages);
                            foreach ($languages as $key => $value): ?>
                            <li<?php if ($value == $last_item): ?> class="lilast"<?php endif ?>><?php
                                echo picto_tag('action_comment')
                                   . f_link_to(__($value), '?lang=' . $key) ?></li>
                            <?php endforeach ?>
                        </ul>
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
                </ul>
            </li>
            <li><?php
                echo link_to(__('Community'), getMetaArticleRoute('association')) ?>
                <ul>
                    <li><?php
                        echo picto_tag('action_people')
                           . link_to(__('Association'), getMetaArticleRoute('association')) ?>
                        <ul>
                            <li class="lilast"><?php
                            echo picto_tag('action_comment')
                               . f_link_to(__('c2corg news'), 'viewforum.php?id=' . $c2c_news_forum) ?></li>
                        </ul>
                    </li>
                    <li><?php
                        echo picto_tag('picto_users')
                           . link_to(ucfirst(__('users')), '@default_index?module=users') ?>
                        <ul>
                            <li class="lilast"><?php
                                echo picto_tag('action_query')
                                   . link_to(__('Search'), '@filter?module=users') ?></li>
                        </ul>
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
            <li id="menulast"><?php
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
                        echo picto_tag('picto_xreports')
                           . link_to(__('My xreports'), 'xreports/myxreports') ?></li>
                    <li><?php
                        echo picto_tag('picto_images')
                           . link_to(__('My images'), 'images/myimages') ?></li>
                    <li><?php
                        echo picto_tag('action_contact')
                           . f_link_to(__('mailbox'), 'message_list.php') ?></li>
                    <li><?php
                        echo picto_tag('picto_outings')
                           . link_to(__('My statistics'), 'outings/mystats') ?></li>
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
        </ul>
        <br class="clearer" />
    </div>
