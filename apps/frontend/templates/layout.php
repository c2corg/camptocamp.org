<?php
$lang_code = __('meta_language');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
$id = $sf_params->get('id');

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
$response->addJavascript(sfConfig::get('app_static_url') . '/static/js/fold.js', 'head_last');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang_code ?>" lang="<?php echo $lang_code ?>">
<head>
    <?php
        $debug = (bool)sfConfig::get('app_minify_debug');
        $combine = !$debug;
        echo include_http_metas();
        echo include_metas();
        echo include_title();
        echo auto_discovery_link_tag('rss', $rss);
        minify_include_stylesheets($combine, $debug);
    ?>
    <!--[if !IE]>-->
    <link type="text/css" rel="stylesheet" media="only screen and (max-device-width: 480px)" href="<?php echo $static_base_url; ?>/static/css/handheld.css?<?php echo sfSVN::getHeadRevision('handheld.css') ?>" />
    <!--<![endif]-->
    <?php
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
</head>
<body>
    <div id="holder">
        <div id="page_header">
        <?php include_partial('common/header', array('lang_code' => $lang_code)); ?>
        </div>
        <div id="content_box">
            <?php echo $sf_data->getRaw('sf_content') ?>
            </div> <!-- Fin wrapper_context -->
        </div>
        <?php
        include_partial('common/footer', array('sf_cache_key' => $lang_code));
        ?>
    </div>

    <div id="fields_tooltip" class="ajax_feedback" style="display: none;" onclick="Element.hide(this); return false;"></div>

    <?php minify_include_body_javascripts($combine, $debug); ?>

    <?php include_partial('common/tracker') ?>

    <?php // addthis script must be added after ga tracker for google analytics integration
          if (sfContext::getInstance()->getResponse()->hasParameter('addthis', 'helper/asset/addthis'))
              echo '<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>'; ?>
</body>
</html>
