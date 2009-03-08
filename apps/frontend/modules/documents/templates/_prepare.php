<?php
if (!isset($open))
{
    $open = true;
}
?>
<div id="nav_prepare">
    <div class="nav_box_top"></div>
    <div class="nav_box_content">
        <?php echo nav_title('prepare', __('Prepare outing'), 'outings', $open); ?>
        <div class="nav_box_text" id="nav_prepare_section_container" <?php if (!$open) echo 'style="display: none;"'; ?>>
            <p>Météo (TODO)</p>
            <ul>
                <li><a href="http://www.meteo.fr/">MéteoFrance</a></li>
                <li><a href="http://www.meteosuisse.ch/">MéteoSuisse</a></li>
                <li><a href="http://meteolive.leonardo.it/">Metolive.it</a></li>
            </ul>
            <p>Avalanche (TODO)</p>
            <ul>
                <li><a href="http://www.meteofrance.com/FR/montagne/index.jsp">BRA MeteoFrance (TODO)</a></li>
                <li><a href="http://www.slf.ch/lawineninfo/lawinenbulletin/nationale_lawinenbulletins/index_FR">SLF (Suisse) (TODO)</a></li>
                <li><a href="http://www.aineva.it/bolletti/bollet6.html">AINEVA (Italie) (TODO)</a></li>
            </ul>
            <p>Cartographie (TODO)</p>
            <ul>
                <li><a href="http://www.meteofrance.com/FR/montagne/index.jsp">Geoportail (TODO)</a></li>
                <li><a href="http://www.slf.ch/lawineninfo/lawinenbulletin/nationale_lawinenbulletins/index_FR">Swisstopo (TODO)</a></li>
                <li><a href="http://www.aineva.it/bolletti/bollet6.html">Portale carografico (TODO)</a></li>
                <li><a href="http://www.aineva.it/bolletti/bollet6.html">Viamichelin (TODO)</a></li>
                <li><a href="http://www.aineva.it/bolletti/bollet6.html">Googlemap (TODO)</a></li>
            </ul>
            <p>Refuges (TODO)</p>
            <ul>
                <li><a href="http://www.meteofrance.com/FR/montagne/index.jsp">Refuges CAF (TODO)</a></li>
                <li><a href="http://www.slf.ch/lawineninfo/lawinenbulletin/nationale_lawinenbulletins/index_FR">Refuges CAS (TODO)</a></li>
                <li><a href="http://www.aineva.it/bolletti/bollet6.html">Refuges CAI (TODO)</a></li>
                <li><a href="http://www.aineva.it/bolletti/bollet6.html">Refuges At (TODO)</a></li>
            </ul>
            <p class="nav_box_bottom_link"><?php echo link_to(__('More links'), getMetaArticleRoute('prepare_outings')) ?></p>
        </div>
    </div>
    <div class="nav_box_down"></div>
</div>