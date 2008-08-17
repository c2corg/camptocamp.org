<div id="nav_figures">
    <div id="nav_figures_top"></div>
    <div id="nav_figures_content">
        <div class="link_nav_news"><?php echo __('Camptocamp.org is about:') ?></div>
        <ul>
        <?php foreach ($figures as $type => $nb): ?>
            <li><?php echo $nb . ' ' . __($type) ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
    <div id="nav_figures_down"></div>
</div>
