<?php
$lang_code = __('meta_language');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
$action = $sf_context->getActionName();
$id = $sf_params->get('id');
$cda_config = sfConfig::get('app_portals_cda');
$cda_id = isset($cda_config['id']) ? $cda_config['id'] : -1;
if ($action == 'map')
{
    $footer_type = 'map';
}
elseif ($id == $cda_id)
{
    $footer_type = 'cda';
}
else
{
    $footer_type = 'normal';
    // alpine club logo is included by css, but only in en
    if ($lang === 'en') use_stylesheet('/static/css/ac');
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
$response->addJavascript('/static/js/fold.js'); ?>
<!doctype html>
<html lang="<?php echo $lang_code ?>">
<head>
    <meta charset="utf-8">
    <?php
        $debug = (bool) sfConfig::get('app_minify_debug');
        $combine = !$debug;
        echo include_http_metas();
        echo include_title();
        // we remove title from metas, because we don't want a <meta name=title>
        $response->getParameterHolder()->remove('title', 'helper/asset/auto/meta');
        echo include_metas();
        minify_include_main_stylesheets($combine, $debug);
        minify_include_custom_stylesheets($combine, $debug); /* here go portal specific css, and maps css (which are not present on every page) */
    ?>
    <!--[if IE 7]><link rel="stylesheet" type="text/css" media="all" href="<?php echo $static_base_url. '/' . sfTimestamp::getTimestamp('/static/css/ie7.css'); ?>/static/css/ie7.css" /><![endif]-->
    <!--[if lt IE 9]><script src="<?php echo $static_base_url. '/' . sfTimestamp::getTimestamp(array('/static/js/html5shiv.js','/static/js/autofocus.js', '/static/js/indexof.js')); ?>/static/js/html5shiv.js,/static/js/autofocus.js,/static/js/indexof.js"></script><![endif]-->
    <?php
        minify_include_head_javascripts($combine, $debug);
        echo auto_discovery_link_tag('rss', $rss);
        echo include_meta_links();
    ?>
    <link rel="search" type="application/opensearchdescription+xml" href="<?php echo $static_base_url; ?>/static/opensearch/description.xml" title="Camptocamp.org" />
    <link rel="shortcut icon" href="<?php
    $favicon = ($footer_type == 'cda') ? 'portals/cda_favicon.ico' : 'favicon.ico';
    echo $static_base_url . '/static/images/' . $favicon;
    ?>" />
    <link rel="alternate" media="only screen and (max-width: 640px)" href="http://<?php echo sfConfig::get('app_mobile_version_host').
    ($_SERVER['REQUEST_URI'] != '/' ? $_SERVER['REQUEST_URI'] : ''); ?>" />
</head>
<body itemscope itemtype="http://schema.org/WebPage">
    <?php include_partial('common/section_close'); ?>
    <div id="holder">
        <header id="page_header">
        <?php
        $header_partial = ($action == 'view' && $footer_type == 'cda') ? 'portals/cda_header' : 'common/header';
        include_partial($header_partial, array('lang_code' => $lang_code));

        if (sfConfig::get('app_production') != 1)
        {
            include_partial('common/dev_env');
        }
        ?>
        </header>
        <div id="content_box">
            <?php echo $sf_data->getRaw('sf_content') ?>
            </div>
        </div>
        <?php
        include_partial('common/footer', array('sf_cache_key' => $footer_type . '_' . $lang,
                                               'lang_code' => $lang_code,
                                               'footer_type' => $footer_type));
        ?>
    </div>
    <div id="fields_tooltip" class="ajax_feedback" style="display: none;" onclick="Element.hide(this); return false;"></div>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>jQuery.noConflict();</script>
    <?php
    minify_include_body_javascripts($combine, $debug);
    minify_include_maps_javascripts($combine, $debug);
    include_partial('common/tracker', array('addthis' => sfContext::getInstance()->getResponse()->hasParameter('addthis', 'helper/asset/addthis')));
    // Prompt ie6 users to install Chrome Frame - no adm rights required. chromium.org/developers/how-tos/chrome-frame-getting-started ?>
    <!--[if lt IE 7 ]><script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js"></script><script>window.attachEvent("onload",function(){CFInstall.check({mode:"overlay"})})</script><![endif]-->
</body>
</html>
