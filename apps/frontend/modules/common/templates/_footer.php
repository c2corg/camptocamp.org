<?php 
if (!function_exists('use_helper')) include_once('symfony/helper/HelperHelper.php'); // needed for use_helper
use_helper('C2CVersion', 'Button', 'I18N'); // I18N is required for the inclusion in the forum to work ?> 

<div id="footer">
    <div id="footer_border_left">&nbsp;</div>
    <div id="footer_cc">
        <div id="footer_partners">
            <?php echo __('site supported by:') ?>
            <a href="http://www.rhonealpes.fr"><img src="/static/images/rhonealpes.gif" alt="Rhône-Alpes" title="Rhône-Alpes" /></a>
            <a href="http://europa.eu/"><img src="/static/images/europe.gif" alt="Europe" title="Europe" /></a>
            <a href="http://www.camptocamp.com/"><img src="/static/images/c2csa.gif" alt="C2C SA" title="Camptocamp SA" /></a>
        </div>
        <div id="footer_cc_text">
            &copy; 1997-2007
            <?php echo link_to('camptocamp.org', '@homepage') ?> |
            <?php echo link_to(__('contact'), getMetaArticleRoute('contact')) ?> |
            <?php echo link_to(__('terms of use'), getMetaArticleRoute('conditions')) ?> |
            <?php echo link_to(__('content license'), getMetaArticleRoute('licenses')) ?> |
            <a href="http://dev.camptocamp.org/"><?php echo __('Developers') ?></a><br />
            <?php echo __('CNIL declaration #') ?>1175560<br />
            <?php echo __('disclaimer notice') ?><br />
            <?php echo __('Camptocamp.org version 5 revision %1%', array('%1%' => c2c_revision())) ?>
        </div>
    </div>
    <div id="footer_border_right">&nbsp;</div>
</div>

<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
<script type="text/javascript">
    _uacct = "<?php echo sfConfig::get('app_ganalytics_key') ?>";
    urchinTracker();
</script>
