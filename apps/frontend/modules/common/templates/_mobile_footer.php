<?php 
if (!function_exists('use_helper'))
{
    include_once('symfony/helper/HelperHelper.php'); // needed for use_helper
}
use_helper('Button', 'I18N'); // I18N is required for the inclusion in the forum to work

$is_cda = ($footer_type === 'cda');
$display_ac = !$is_cda && (__('meta_language') == 'en');

if ((bool)sfConfig::get('app_mobile_version_ads'))
{
    include_partial('common/mobile_banner');
}
?>
<footer id="footer">
    <div id="footer_cc">
        <div id="footer_cc_text">
            <p><?php echo link_to(__('Home'), '@homepage'), ' | ', 
               link_to(__('web version of the site'), '@default?module=common&action=switchformfactor') ?></p>
            <p>&copy; 1997-<?php echo date('Y') . ' ' . link_to('Camptocamp-Association', getMetaArticleRoute('association')) ?></p>
            <p><?php echo __('CNIL declaration #') ?>1175560 - <?php echo __('disclaimer notice') ?></p>
        </div>
        <div id="footer_partners"><?php
    if ($is_cda):
?>
            <?php echo __('site supported by:') ?>
            <ul id="partners">
                <li><a href="http://www.mountainwilderness.fr/" title="Mountain Wilderness"><span id="mw_logo"></span></a></li>
                <li id="europesengage"><a href="http://europa.eu/" title="Europe"></a></li>
                <li id="europa"><a href="http://europa.eu/" title="Europe"></a></li>
                <li id="rhonealpes"><a href="http://www.rhonealpes.fr/" title="Rhône-Alpes"></a></li>
                <li id="c2csa"><a href="http://www.camptocamp.com/" title="Camptocamp SA"></a></li>
                <li id="paca"><a href="http://www.regionpaca.fr/" title="PACA"></a></li>
                <li id="languedoc"><a href="http://www.laregion.fr/" title="Languedoc-Roussillon"></a></li>
                <li id="alpes_maritimes"><a href="http://www.cg06.fr/" title="Alpes Maritimes"></a></li>
                <li id="aquitaine"><a href="http://aquitaine.fr/" title="Aquitaine"></a></li>
                <li id="partners_tips"><?php echo __('cda partners tips') ?></li>
            </ul><?php
    else:
?>
            <ul id="partners">
                <li id="rhonealpes"><a href="http://www.rhonealpes.fr/" title="Rhône-Alpes"></a></li>
                <li id="europa"><a href="http://europa.eu/" title="Europe"></a></li>
                <li id="c2csa"><a href="http://www.camptocamp.com/" title="Camptocamp SA"></a></li>
                <?php if ($display_ac): ?>
                <li id="alpineclub"><a href="http://www.alpine-club.org.uk/" title="Alpine Club"></a></li>
                <?php endif ?>
            </ul><?php
    endif;
?>
        </div>
    </div>
</footer>
