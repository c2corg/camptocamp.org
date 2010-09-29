<?php
$lang_code = __('meta_language');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
$footer_type = 'normal';
$action = sfContext::getInstance()->getActionName();
$id = $sf_params->get('id');
if ($action == 'map')
{
    $footer_type = 'map';
}
elseif ($id == sfConfig::get('app_changerdapproche_id'))
{
    $footer_type = 'cda';
}

if ($sf_context->getActionName() == 'list')
{
    use_helper('Button');
    $rss = get_rsslist_path($module);
}
else
{
    $rss = ($id) ? "@document_feed?module=$module&id=$id&lang=$lang" : "@feed?module=$module&lang=$lang";
}
use_helper('MyMinify', 'MetaLink');

$static_base_url = sfConfig::get('app_static_url');
$response = sfContext::getInstance()->getResponse();
$response->addJavascript('/static/js/fold.js', 'head_last');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang_code ?>">
<head>
    <?php
        $debug = (bool)sfConfig::get('app_minify_debug');
        $combine = !$debug;
        echo include_http_metas();
        echo include_metas();
        echo include_title();
        echo auto_discovery_link_tag('rss', $rss);
        minify_include_main_stylesheets($combine, $debug);
        minify_include_custom_stylesheets($combine, $debug);
        minify_include_head_javascripts($combine, $debug);
        echo include_meta_links();
    ?>
    <!--[if IE 6]>
        <link rel="stylesheet" type="text/css" media="all" href="<?php echo $static_base_url; ?>/static/css/ie.css?<?php echo sfSVN::getHeadRevision('ie.css') ?>" />
    <![endif]-->
    <!--[if IE 7]>
        <link rel="stylesheet" type="text/css" media="all" href="<?php echo $static_base_url; ?>/static/css/ie7.css?<?php echo sfSVN::getHeadRevision('ie7.css') ?>" />
    <![endif]-->
    <link rel="search" type="application/opensearchdescription+xml" href="<?php echo $static_base_url; ?>/static/opensearch/description.xml" 
          title="Camptocamp.org" />
    <link rel="shortcut icon" href="<?php echo $static_base_url; ?>/static/images/favicon.ico" />
    <?php include_partial('common/tracker') ?>
</head>
<body>
    <div id="holder">
        <div id="page_header">
        <?php
        if ($action == 'view' && $footer_type == 'cda')
        {
            $header_partial = 'portals/cda_header';
        }
        else
        {
            $header_partial = 'common/header';
        }
        include_partial($header_partial, array('lang_code' => $lang_code));
        ?>
        </div>
<?php //////////////////////////////// TRANSITION CODE FOR MAKING MOBILE VERSION OF THE SITE OBVIOUS ////////////////////////
      //////////////////////////////// TO USERS USING A MOBILE PHONE ////////////////////////////////////////////////////////
      //////////////////////////////// TO BE REMOVED AFTER SOME WEEKS ///////////////////////////////////////////////////////
      if ($_SERVER['REQUEST_URI'] == '/' &&
          preg_match('/(Mobi|DoCoMo|NetFront|Symbian|Nokia|SAMSUNG|BlackBerry|J-PHONE|KDDI|UP.Browser|DDIPOCKET|Mini)/i',
                     $_SERVER['HTTP_USER_AGENT'])): ?> 
        <div style="text-align:center; margin: 0 15px 15px 15px; padding: 10px 0; border-radius: 8px;
                    -moz-border-radius: 8px; -khtml-border-radius: 8px;-webkit-border-radius: 8px;
                    border: 4px solid #ff0016">
        <?php echo link_to(__('Discover mobile version'), 'http://'.sfConfig::get('app_mobile_version_host')) ?>
        </div>
<?php endif ///////////////////////// END OF TRANSITION CODE //////////////////////// ?>
        <div id="content_box">
            <?php echo $sf_data->getRaw('sf_content') ?>
            </div> <!-- Fin wrapper_context -->
        </div>
        <?php
        include_partial('common/footer', array('lang_code' => $lang_code,
                                               'footer_type' => $footer_type));
        ?>
    </div>

    <div id="fields_tooltip" class="ajax_feedback" style="display: none;" onclick="Element.hide(this); return false;"></div>

    <?php
    minify_include_body_javascripts($combine, $debug);
    minify_include_unminified_javascripts();
    // addthis script must be added after ga tracker for google analytics integration
    if (sfContext::getInstance()->getResponse()->hasParameter('addthis', 'helper/asset/addthis'))
    {
        echo '<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>';
    }
    ?>
</body>
</html>
