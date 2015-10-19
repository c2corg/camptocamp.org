<?php
if (!isset($default_open))
{
    $default_open = true;
}

?>
<div id="nav_toolbox" class="nav_box">
    <div class="nav_box_top"></div>
    <div class="nav_box_content">
        <?php echo nav_title('toolbox', __('Toolbox'), 'tools'); ?>
        <div class="nav_box_text" id="nav_toolbox_section_container">
            <ul>
                <li><?php echo link_to(__('recent conditions'), 'outings/conditions') ?></li>
                <li><?php echo link_to(__('Latest outings'), '@ordered_list?module=outings&orderby=date&order=desc') ?></li>
                <li><?php echo link_to(__('Search a routes'), '@filter?module=routes') ?></li>
                <li><?php echo link_to(__('Map tool'), '@map') ?></li>
                <li><?php echo m_link_to(__('cotometre'), '@tool?action=cotometre',
                                         array('title'=> __('cotometre long')),
                                         array('width' => 600)) ?></li>
                <li><?php echo link_to(ucfirst(__('xreports')), '@xreports') ?></li>
                <li><?php
                    $portal_config = sfConfig::get('app_portals_firstascent');
                    $text = __($portal_config['name']);
                    $portal_url = '@document_by_id?module=portals&id=' . $portal_config['id'];
                    echo link_to($text, $portal_url);
                ?></li>
            </ul>
            <p><?php echo ucfirst(__('portals')) ?></p>
            <ul><?php
                $portal_list = sfConfig::get('app_portals_id');
                foreach ($portal_list as $portal_id)
                {
                    $portal_config = sfConfig::get('app_portals_' . $portal_id);
                    if (isset($portal_config['home']) && $portal_config['home'])
                    {
                        $text = __($portal_config['name']);
                        if (isset($portal_config['url']) || isset($portal_config['annex_url']))
                        {
                            if (isset($portal_config['url']))
                            {
                                $portal_url = $portal_config['url'];
                            }
                            else
                            {
                                $portal_url = $portal_config['annex_url'];
                            }
                            $portal_url = 'http://' . $portal_url;
                        }
                        else
                        {
                            $portal_url = '@document_by_id?module=portals&id=' . $portal_config['id'];
                        }
                        $html = link_to($text, $portal_url);
                        echo '<li>' . $html . '</li>';
                    }
                }
            ?></ul>
            <p><?php echo __('Help') ?></p>
            <ul>
                <li><?php echo link_to(__('How to customize'), getMetaArticleRoute('customize', false)) ?></li>
                <li><?php echo link_to(__('Global help'), getMetaArticleRoute('help', false)) ?></li>
                <li><?php echo link_to(__('Guidebook help'), getMetaArticleRoute('help_guide', false)) ?></li>
                <li><?php echo link_to(__('FAQ'), getMetaArticleRoute('faq', false)) ?></li>
                <li><?php echo link_to(__('Camptocamp-Association'), getMetaArticleRoute('association', false)) ?></li>
                <li><?php echo link_to(__('Shop'), getMetaArticleRoute('shop', false)) ?></li>
            </ul>
        </div>
        <?php
        $cookie_position = array_search('nav_toolbox', sfConfig::get('app_personalization_cookie_fold_positions'));
        echo javascript_tag('C2C.setSectionStatus(\'nav_toolbox\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').");");
        ?>
    </div>
    <div class="nav_box_down"></div>
</div>
