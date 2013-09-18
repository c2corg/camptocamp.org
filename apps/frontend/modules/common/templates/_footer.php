<?php 
if (!function_exists('use_helper'))
{
    include_once('symfony/helper/HelperHelper.php'); // needed for use_helper
}
use_helper('Button', 'I18N'); // I18N is required for the inclusion in the forum to work

$mobile_hostname = sfConfig::get('app_mobile_version_host');
$classic_hostname = sfConfig::get('app_classic_version_host');

$is_map = ($footer_type === 'map');
$is_cda = ($footer_type === 'cda');
$display_ac = !$is_map && !$is_cda && (__('meta_language') == 'en');
if ($display_ac)
{
    $class = ' class="ac"';
}
else if (isset($footer_type))
{
    $class = ' class="' . $footer_type . '_content"';
}
else
{
    $class = '';
}
?> 

<footer id="footer"<?php echo $class ?>>
    <div id="footer_border_left">&nbsp;</div>
    <div id="footer_cc">
<?php
if (!$is_map):
?>
        <div id="footer_partners">
            <?php echo __('site supported by:') ?>
            <ul id="partners"><?php
    if ($is_cda):
?>
                <li id="europesengage"><a href="http://europa.eu/" title="Europe"></a></li>
                <li id="paca"><a href="http://www.regionpaca.fr/" title="PACA"></a></li>
                <li id="languedoc"><a href="http://www.laregion.fr/" title="Languedoc-Roussillon"></a></li>
                <li id="alpes_maritimes"><a href="http://www.cg06.fr/" title="Alpes Maritimes"></a></li>
                <li id="aquitaine"><a href="http://aquitaine.fr/" title="Aquitaine"></a></li>
                <li id="rhonealpes"><a href="http://www.rhonealpes.fr/" title="Rhône-Alpes"></a></li>
                <li id="europa"><a href="http://europa.eu/" title="Europe"></a></li>
                <li id="c2csa"><a href="http://www.camptocamp.com/" title="Camptocamp SA"></a></li>
                <li id="partners_tips"><?php echo __('cda partners tips') ?></li><?php
    else:
?>
                <li id="rhonealpes"><a href="http://www.rhonealpes.fr/" title="Rhône-Alpes"></a></li>
                <li id="europa"><a href="http://europa.eu/" title="Europe"></a></li>
                <li id="c2csa"><a href="http://www.camptocamp.com/" title="Camptocamp SA"></a></li>
                <?php if ($display_ac): ?>
                <li id="alpineclub"><a href="http://www.alpine-club.org.uk/" title="Alpine Club"></a></li>
                <?php endif ?>
<?php
    endif;
?>
            </ul>
        </div>
<?php
endif;
?>
        <div id="footer_cc_text">
            <p>&copy; 1997-<?php echo date('Y') ?>
            <?php echo link_to('Camptocamp-Association', getMetaArticleRoute('association')) ?> |
            <?php echo link_to(__('contact'), getMetaArticleRoute('contact')) ?> |
            <?php echo link_to(__('terms of use'), getMetaArticleRoute('conditions')) ?> |
            <?php echo link_to(__('content license'), getMetaArticleRoute('licenses')) ?> |
            <?php echo link_to(__('Developers'), 'https://trac.dev.camptocamp.org/') ?> |
            <?php echo link_to(__('credits'), getMetaArticleRoute('credits'));
            if (!empty($mobile_hostname) && !$is_map)
            {
                echo ' | ' . link_to(__('mobile version'), 'http://'.$mobile_hostname,
                                     array('id' => 'm-link',
                                           'onclick' => "document.cookie='nomobile=; expires=Thu, 01-Jan-70 00:00:01 GMT;';"));
            }
            ?>
            <script type="text/javascript">
              document.getElementById('m-link').href = document.location.href.replace('<?php echo $classic_hostname ?>', '<?php echo $mobile_hostname ?>');
            </script>
            </p>
<?php
if (!$is_map):
?>
            <p><?php echo __('CNIL declaration #') ?>1175560</p>
            <p id="disclaimer"><?php echo __('disclaimer notice') ?></p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <p><?php echo __('Camptocamp.org version 5 revision %1%', array('%1%' => '<a href="https://trac.dev.camptocamp.org/changeset/' . sfConfig::get('app_versions_head'). '/camptocamp.org" class="footer_rev">' . date('YmdHi', sfConfig::get('app_versions_date')) . '</a>')) ?>
            <input name="cmd" value="_xclick" type="hidden" />
            <input name="business" value="registration@camptocamp.org" type="hidden" />
            <input name="currency_code" value="EUR" type="hidden" />
            <input type="hidden" name="item_name" value="Soutenir/supporting Camptocamp Association" />
            <input type="hidden" name="return" value="http://camptocamp.org/" />
            <input type="submit" id="pp_button_mini" title="<?php echo __('Donate to Camptocamp Association'); ?>" value="" />
            </p></form>
<?php
endif;
// following is needed for lightbox.js to load images from static host
echo javascript_tag('var _static_url = \'' . sfConfig::get('app_static_url') . '\';');
?>
        </div>
    </div>
    <div id="footer_border_right">&nbsp;</div>
</footer>
