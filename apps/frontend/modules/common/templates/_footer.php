<?php 
if (!function_exists('use_helper'))
{
    include_once('symfony/helper/HelperHelper.php'); // needed for use_helper
}
use_helper('Button', 'I18N'); // I18N is required for the inclusion in the forum to work

$action = sfContext::getInstance()->getActionName();
$is_map = ($action == 'map');
$class = ($is_map) ? ' class="map_content"' : '';
?> 

<div id="footer"<?php echo $class ?>>
    <div id="footer_border_left">&nbsp;</div>
    <div id="footer_cc">
<?php
if (!$is_map):
?>
        <div id="footer_partners">
            <?php echo __('site supported by:') ?>
            <ul id="partners">
                <li id="rhonealpes"><a href="http://www.rhonealpes.fr/" title="Rhône-Alpes"></a></li>
                <li id="europa"><a href="http://europa.eu/" title="Europe"></a></li>
                <li id="c2csa"><a href="http://www.camptocamp.com/" title="Camptocamp SA"></a></li>
            </ul>
        </div>
<?php
endif;
?>
        <div id="footer_cc_text">
            <p>&copy; 1997-2010
            <?php echo link_to('Camptocamp-Association', getMetaArticleRoute('association')) ?> |
            <?php echo link_to(__('contact'), getMetaArticleRoute('contact')) ?> |
            <?php echo link_to(__('terms of use'), getMetaArticleRoute('conditions')) ?> |
            <?php echo link_to(__('content license'), getMetaArticleRoute('licenses')) ?> |
            <a href="http://dev.camptocamp.org/"><?php echo __('Developers') ?></a></p>
<?php
if (!$is_map):
?>
            <p><?php echo __('CNIL declaration #') ?>1175560</p>
            <p id="disclaimer"><?php echo __('disclaimer notice') ?></p>
            <p><?php echo __('Camptocamp.org version 5 revision %1%', array('%1%' => sfSVN::getHeadRevision('head'))) ?></p>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post"><div>
            <input name="cmd" value="_xclick" type="hidden" />
            <input name="business" value="registration@camptocamp.org" type="hidden" />
            <input name="currency_code" value="EUR" type="hidden" />
            <input type="hidden" name="item_name" value="Soutenir/supporting Camptocamp Association" />
            <input type="hidden" name="return" value="http://camptocamp.org/" />
            <input type="submit" class="pp_button_mini" title="<?php echo __('Donate to Camptocamp Association'); ?>" value="" />
            </div></form>
<?php
endif;
?>
        </div>
    </div>
    <div id="footer_border_right">&nbsp;</div>
</div>
