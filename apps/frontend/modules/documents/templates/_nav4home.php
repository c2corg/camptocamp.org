<?php use_helper('Button') ?>
<?php $lang = $sf_user->getCulture() ?>

<div id="nav_tools">
    <div id="nav_tools_top"></div>
    <div id="nav_tools_content">
        <ul>
            <li><?php echo link_to(__('Recent changes'), '@whatsnew', array('class' => 'action_list nav_edit')) ?></li>
            <li><?php echo link_to(__('Recent associations'), '@latestassociations', array('class' => 'action_list nav_edit')) ?></li>
            <li><?php echo button_rss('documents', $lang) ?></li>
            <li><?php echo button_rss('documents', $lang, null, 'creations') ?></li>
            <li><?php echo button_report() ?></li>
            <li><?php echo button_help('help') ?></li>
        </ul>
    </div>
    <div id="nav_tools_down"></div>
</div>
