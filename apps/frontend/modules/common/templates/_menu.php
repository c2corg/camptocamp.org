<?php
use_helper('Forum','Button', 'ModalBox');

$is_connected = $sf_user->isConnected();
$sublevel_ie7 = '<!--[if IE 7]><!-->';
$sublevel_start = '<!--<![endif]--> <!--[if lte IE 6]><table><tr><td><![endif]-->';
$sublevel_end = '<!--[if lte IE 6]></td></tr></table></a><![endif]-->';
?>

<script type="text/javascript"><!--
    hide_select = function() {}
    show_select = function() {}
//--></script>
<!--[if lt IE 7]><script>
    /* FIXME The worst hack for the IE select z-index bug !!!
    window.onload = startList;
    preferably replaced by :
    */
    Event.observe(window, 'load', startList);
    
    var selectList = document.getElementsByTagName('select');
    hide_select = function()
    {
        if(selectList)
        {
            var len = selectList.length;
            if(len>0)
            {
                for(i=0; i<len; i++)
                {
                    selectList[i].style.display = 'none';
                }
            }
        }
    }

    show_select = function()
    {
        if(selectList)
        {
            var len = selectList.length;
            if(len>0)
            {
                for(i=0; i<len; i++)
                {
                    selectList[i].style.display = '';
                }
            }
        }
    }
</script><![endif]-->
<script type="text/javascript"><!--
// if (document.all && document.getElementById && navigator.userAgent.indexOf('Mac')!=-1) {
//     window.onload = startList;
// }
//--></script>

<div id="menu_up">
    <div id="quick_switch">
        <?php
        $act_filter = c2cPersonalization::getActivitiesFilter();
        $light = array(1 => '_light', 2 => '_light', 3 => '_light', 4 => '_light', 5 => '_light', 6 => '_light');
        if (c2cPersonalization::isMainFilterSwitchOn())
        {
            foreach ($act_filter as $act_id)
            {
                $light[$act_id] = '';
            }
        }
        $alist = sfConfig::get('app_activities_list');
        array_shift($alist);
        foreach ($alist as $id => $activity)
        {
            $alt = ($act_filter == array($id + 1)) ? __('switch_off_activity_personalisation') : __('switch_to_' . $activity) ;
            $image_tag = image_tag('/static/images/picto/' . $activity . $light[$id + 1] . '_mini.png',
                                   array('alt' => $activity, 'title' => $alt));
                          
            echo link_to($image_tag, '@quick_activity?activity=' . ($id + 1), array('class' => 'qck_sw'));
        }
        ?>
    </div>
    <div id="menu_content">
        <ul onmouseover="hide_select();" onmouseout="show_select();">
            <li><?php echo link_to(__('Home'), '@homepage') ?></li>
            <li>
                <?php echo link_to(__('Guidebook') . $sublevel_ie7, '@default_index?module=outings'); ?><?php echo $sublevel_start ?>
                <ul>
                    <li>
                        <?php echo link_to(__('outings') . $sublevel_ie7, '@default_index?module=outings', array('class' => 'img_module_outings')) ?>
                        <?php echo $sublevel_start ?>
                        <ul>
                            <li><?php echo link_to(__('cond short'), 'outings/conditions', array('class' => 'img_action_list')) ?></li>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php echo link_to(__('Search'), '@filter?module=outings', array('class' => 'img_action_search')) ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php echo m_link_to(__('Add'), 'outings/wizard', array('class' => 'img_action_create')) ?></li>
                            <?php endif ?>
                        </ul> <?php echo $sublevel_end ?>
                    </li>
                    <li>
                        <?php echo link_to(__('routes') . $sublevel_ie7, '@default_index?module=routes',  array('class' => 'img_module_routes')) ?>
                        <?php echo $sublevel_start ?>
                        <ul>
                            <li class="lilast"><?php echo link_to(__('Search'), '@filter?module=routes', array('class' => 'img_action_search')) ?></li>
                        </ul><?php echo $sublevel_end ?>
                    </li>
                    <li>
                        <?php echo link_to(__('summits') . $sublevel_ie7, '@default_index?module=summits', array('class' => 'img_module_summits')) ?>
                        <?php echo $sublevel_start ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php echo link_to(__('Search'), '@filter?module=summits', array('class' => 'img_action_search')) ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php echo link_to(__('Add'), '@document_edit?module=summits&id=&lang=', array('class' => 'img_action_create')) ?></li>
                            <?php endif ?>
                        </ul><?php echo $sublevel_end ?>
                    </li>
                    <li>
                        <?php echo link_to(__('sites') . $sublevel_ie7, '@default_index?module=sites', array('class' => 'img_module_sites')) ?>
                        <?php echo $sublevel_start ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php echo link_to(__('Search'), '@filter?module=sites', array('class' => 'img_action_search')) ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php echo link_to(__('Add'), '@document_edit?module=sites&id=&lang=', array('class' => 'img_action_create')) ?></li>
                            <?php endif ?>
                        </ul><?php echo $sublevel_end ?>
                    </li>
                    <li>
                        <?php echo link_to(__('parkings') . $sublevel_ie7, '@default_index?module=parkings', array('class' => 'img_module_parkings')) ?>
                        <?php echo $sublevel_start ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php echo link_to(__('Search'), '@filter?module=parkings', array('class' => 'img_action_search')) ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php echo link_to(__('Add'), '@document_edit?module=parkings&id=&lang=', array('class' => 'img_action_create')) ?></li>
                            <?php endif ?>
                        </ul> <?php echo $sublevel_end ?>
                    </li>
                    <li>
                        <?php echo link_to(__('huts') . $sublevel_ie7, '@default_index?module=huts', array('class' => 'img_module_huts')) ?>
                        <?php echo $sublevel_start ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php echo link_to(__('Search'), '@filter?module=huts', array('class' => 'img_action_search')) ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php echo link_to(__('Add'), '@document_edit?module=huts&id=&lang=', array('class' => 'img_action_create')) ?></li>
                            <?php endif ?>
                        </ul> <?php echo $sublevel_end ?>
                    </li>
                    <li>
                        <?php echo link_to(__('areas') . $sublevel_ie7, '@default_index?module=areas', array('class' => 'img_module_areas')) ?>
                        <?php echo $sublevel_start ?>
                        <ul>
                            <li class="lilast"><?php echo link_to(__('Search'), '@filter?module=areas', array('class' => 'img_action_search')) ?></li>
                        </ul><?php echo $sublevel_end ?>
                    </li>
                    <li>
                        <?php echo link_to(__('maps') . $sublevel_ie7, '@default_index?module=maps', array('class' => 'img_module_maps')) ?>
                        <?php echo $sublevel_start ?>
                        <ul>
                            <li class="lilast"><?php echo link_to(__('Search'), '@filter?module=maps', array('class' => 'img_action_search')) ?></li>
                        </ul> <?php echo $sublevel_end ?>
                    </li>
                    <li class="lilast">
                        <?php echo link_to(__('books') . $sublevel_ie7, '@default_index?module=books', array('class' => 'img_module_books')) ?>
                        <?php echo $sublevel_start ?>
                        <ul>
                            <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php echo link_to(__('Search'), '@filter?module=books', array('class' => 'img_action_search')) ?></li>
                            <?php if ($is_connected): ?>
                            <li class="lilast"><?php echo link_to(__('Add'), '@document_edit?module=books&id=&lang=', array('class' => 'img_action_create')) ?></li>
                            <?php endif ?>
                        </ul> <?php echo $sublevel_end ?>
                    </li>
                </ul><?php echo $sublevel_end ?>
            </li>
            <li>
                <?php echo f_link_to(__('Forum') . $sublevel_ie7, '?lang='. $sf_user->getCulture()); ?><?php echo $sublevel_start ?>
                <ul>
                    <?php foreach (Language::getAll() as $key => $value): ?>
                    <li><?php echo f_link_to(__($value), '?lang=' . $key,  array('class' => 'img_action_comment')) ?></li>
                    <?php endforeach ?>
                    <li><?php echo f_link_to(__('Search'), 'search.php',  array('class' => 'img_action_search')) ?></li>
                    <?php if ($is_connected): ?>
                        <li><?php echo f_link_to(__('User profile'), 'profile.php?section=personality',  array('class' => 'img_action_edit')) ?></li>
                    <?php endif ?>
                    <li><?php echo link_to(__('Help'), getMetaArticleRoute('help_forum'), array('class' => 'img_action_help')) ?></li>
                    <li class="lilast"><?php echo link_to(__('Charte'), getMetaArticleRoute('charte_forum'), array('class' => 'img_action_help')) ?></li>
                </ul><?php echo $sublevel_end ?>
            </li>
            <li>
                <?php echo link_to(__('Articles') . $sublevel_ie7, '@default_index?module=articles'); ?><?php echo $sublevel_start ?>
                <ul>
                    <li><?php echo link_to(__('Summary'), getMetaArticleRoute('home_articles'), array('class' => 'img_action_list')) ?></li>
                    <li<?php if (!$is_connected): ?> class="lilast"<?php endif ?>><?php echo link_to(__('Search'), '@filter?module=articles', array('class' => 'img_action_search')) ?></li>
                    <?php if ($is_connected): ?>
                    <li class="lilast"><?php echo link_to(__('Add'), '@document_edit?module=articles&id=&lang=', array('class' => 'img_action_create')) ?></li>
                    <?php endif ?>
                </ul><?php echo $sublevel_end ?>
            </li>
            <li>
                <?php echo link_to(__('Gallery') . $sublevel_ie7, '@default_index?module=images'); ?><?php echo $sublevel_start ?>
                <ul>
                    <li class="lilast"><?php echo link_to(__('Search'), '@filter?module=images', array('class' => 'img_action_search')) ?></li>
                </ul><?php echo $sublevel_end ?>
            </li>
            <li<?php if (!$is_connected): ?> id="menulast"<?php endif ?>>
                <?php echo link_to(__('Association'), getMetaArticleRoute('association')); ?>
                <ul>
                    <li class="lilast"><?php echo link_to(__('Shop'), getMetaArticleRoute('shop'), array('class' => 'img_action_list')) ?></li>
                </ul>
            </li>
            <?php if ($is_connected): ?>
            <li id="menulast">
                <?php echo link_to(ucfirst(__('users')) . $sublevel_ie7, '@default_index?module=users') ?><?php echo $sublevel_start ?>
                <ul>
                    <li><?php echo link_to(__('Search'), '@filter?module=users', array('class' => 'img_action_search')) ?></li>
                        <li class="lilast"><?php echo link_to(__('Mailing lists link'), 'users/mailinglists', array('class' => 'img_action_mail')) ?></li>
                </ul><?php echo $sublevel_end ?>
            </li>
            <?php endif ?>
        </ul>
        <br class="clear" />
    </div>
</div>

