<?php
if (!isset($open))
{
    $open = true;
}
?>
<div id="nav_news">
    <div class="nav_box_top"></div>
    <div class="nav_box_content">
        <?php echo nav_title('news', __('c2corg news'), 'list', $open); ?>
        <div class="nav_box_text" id="nav_news_section_container" <?php if (!$open) echo 'style="display: none;"'; ?>>
            <!-- FIXME: content should be editable -->
            <ul>
                <li><a href="http://www.camptocamp.org/articles/144188/fr">Rassemblement et AG</a> Belledone, 14-15 mars 2009 </li>
                <li><a href="http://www.camptocamp.org/forums/viewtopic.php?id=134880">Rencontres Pyrénées</a>C2C G2G - hiver 2009</li>
                <li>(Re)découvrez comment <a href="http://www.camptocamp.org/articles/108776">personnaliser</a> le site.</li>
                <li><a href="http:///">Mise à jour du site</a> R608 le 02/12/2008</li>			
            </ul>
        </div>
    </div>
    <div class="nav_box_down"></div>
</div>