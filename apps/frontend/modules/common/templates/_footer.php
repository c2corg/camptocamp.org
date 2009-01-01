<?php 
if (!function_exists('use_helper'))
{
    include_once('symfony/helper/HelperHelper.php'); // needed for use_helper
}
use_helper('C2CVersion', 'Button', 'I18N'); // I18N is required for the inclusion in the forum to work ?> 

<div id="footer">
    <div id="footer_border_left">&nbsp;</div>
    <div id="footer_cc">
        <div id="footer_partners">
            <?php echo __('site supported by:') ?>
            <ul id="partners">
                <li id="rhonealpes"><a href="http://www.rhonealpes.fr/" title="Rhône-Alpes"></a></li>
                <li id="europa"><a href="http://europa.eu/" title="Europe"></a></li>
                <li id="c2csa"><a href="http://www.camptocamp.com/" title="Camptocamp SA"></a></li>
            </ul>
        </div>
        <div id="footer_cc_text">
            &copy; 1997-2009
            <?php echo link_to('Camptocamp-Association', getMetaArticleRoute('association')) ?> |
            <?php echo link_to(__('contact'), getMetaArticleRoute('contact')) ?> |
            <?php echo link_to(__('terms of use'), getMetaArticleRoute('conditions')) ?> |
            <?php echo link_to(__('content license'), getMetaArticleRoute('licenses')) ?> |
            <a href="http://dev.camptocamp.org/"><?php echo __('Developers') ?></a>
	    <br />
            <?php echo __('CNIL declaration #') ?>1175560<br />
            <?php echo __('disclaimer notice') ?><br />
            <?php echo __('Camptocamp.org version 5 revision %1%', array('%1%' => c2c_revision())) ?>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input name="cmd" value="_xclick" type="hidden" />
            <input name="business" value="registration@camptocamp.org" type="hidden" />
            <input name="currency_code" value="EUR" type="hidden" />
            <input type="hidden" name="item_name" value="Soutenir/supporting Camptocamp Association" />
            <input type="hidden" name="return" value="http://camptocamp.org/" />
            <input src="<?php echo sfConfig::get('app_static_url'); ?>/static/images/paypal.png" name="submit" 
                   alt="Donate" title="<?php echo __('Donate to Camptocamp Association'); ?>" type="image" />
            </form>
        </div>
    </div>
    <div id="footer_border_right">&nbsp;</div>
</div>

<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
<script type="text/javascript">
    _uacct = "<?php echo sfConfig::get('app_ganalytics_key') ?>";
    urchinTracker();
</script>
