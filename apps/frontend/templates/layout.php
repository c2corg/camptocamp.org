<?php
$lang_code = __('meta_language');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
$action = $sf_context->getActionName();
$response = sfContext::getInstance()->getResponse();
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

if ($action == 'list')
{
    use_helper('Button');
    $rss = get_rsslist_path($module);
}
else
{
    $rss = ($id) ? "@document_feed?module=$module&id=$id&lang=$lang" : "@feed?module=$module&lang=$lang";
}

if ((in_array($action, array('list', 'home')) || $module == 'portals') && $module != 'summits')
{
    $holder_class = '';
}
elseif ($action == 'map')
{
    $holder_class = ' class="full_screen"';
}
else
{
    $holder_class = ' class="max_width"';
}

if ($sf_user->getCulture() == 'fr')
{
    $response->addJavascript('/static/js/donate.js', 'last');
    $response->addStylesheet('/static/css/donate.css', 'last'); 
}

use_helper('MyMinify', 'MetaLink');

$static_base_url = sfConfig::get('app_static_url');
?>
<!doctype html>
<html lang="<?php echo $lang_code ?>" data-static-url="<?php echo sfConfig::get('app_static_url') ?>">
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
    <!--[if IE 7]><link rel="stylesheet" type="text/css" media="all" href="<?php echo  minify_get_combined_files_url('/static/css/ie7.css', $debug) ?>" /><![endif]-->
    <!--[if lt IE 9]><script src="<?php echo minify_get_combined_files_url(array('/static/js/html5shiv.js','/static/js/autofocus.js', '/static/js/indexof.js'), $debug) ?>"></script><![endif]-->
    <?php
        echo auto_discovery_link_tag('rss', $rss);
        echo include_meta_links();
        echo '<link rel="canonical" href="http://' . $_SERVER['HTTP_HOST'] . ($_SERVER['REQUEST_URI'] != '/' ? $_SERVER['REQUEST_URI'] : '') . '" />';
    ?>
    <link rel="search" type="application/opensearchdescription+xml" href="<?php echo $static_base_url ?>/static/opensearch/description.xml" title="Camptocamp.org" />
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="msapplication-TileImage" content="<?php echo $static_base_url ?>/mstile-144x144.png">
</head>
<body itemscope itemtype="http://schema.org/WebPage">
    <?php include_partial('common/section_close'); ?>
    <div id="holder"<?php echo $holder_class ?>>
        <header id="page_header">
        <?php
        if ($sf_user->getCulture() == 'fr')
        {
            include_partial('common/donate');
        }

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
    <?php
    minify_include_body_javascripts($combine, $debug);
    minify_include_maps_javascripts($combine, $debug);
    include_partial('common/tracker', array('addthis' => sfContext::getInstance()->getResponse()->hasParameter('addthis', 'helper/asset/addthis')));
    // Prompt ie6 users to install Chrome Frame - no adm rights required. chromium.org/developers/how-tos/chrome-frame-getting-started ?>
    <!--[if lt IE 7 ]><script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js"></script><script>window.attachEvent("onload",function(){CFInstall.check({mode:"overlay"})})</script><![endif]-->
</body>
</html>
