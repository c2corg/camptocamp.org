<?php 
if (!function_exists('use_helper'))
{
    include_once('symfony/helper/HelperHelper.php'); // needed for use_helper
}
use_helper('Button', 'I18N'); // I18N is required for the inclusion in the forum to work

$is_map = ($footer_type == 'map');
$is_cda = ($footer_type == 'cda');
if ($footer_type != 'normal')
{
    $class = ' class="' . $footer_type . '_content"';
}
else
{
    $class = '';
}
?> 
<?php include_partial('common/mobile_banner') ?>
<div id="footer"<?php echo $class ?>>
    <div id="footer_cc">
        <div id="footer_cc_text">
            <p><?php echo link_to(__('web version of the site'), 'http://www.camptocamp.org') ?></p>
            <p>&copy; 1997-2010 <?php echo link_to('Camptocamp-Association', getMetaArticleRoute('association')) ?></p>
            <p><?php echo __('CNIL declaration #') ?>1175560 - <?php echo __('disclaimer notice') ?></p>
        </div>
        <div id="footer_partners">
            <ul id="partners">
                <li id="rhonealpes"><a href="http://www.rhonealpes.fr/" title="Rhône-Alpes"></a></li>
                <li id="europa"><a href="http://europa.eu/" title="Europe"></a></li>
                <li id="c2csa"><a href="http://www.camptocamp.com/" title="Camptocamp SA"></a></li>
            </ul>
        </div>
    </div>
</div>
