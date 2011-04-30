<?php
$lang_code = __('meta_language');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
$footer_type = 'normal';
$action = sfContext::getInstance()->getActionName();
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
<!doctype html>
<html lang="<?php echo $lang_code ?>">
<head>
    <meta charset="utf-8">
    <?php
        $debug = (bool)sfConfig::get('app_minify_debug');
        $combine = !$debug;
        echo include_http_metas();
        echo include_metas();
        echo include_title();
        minify_include_main_stylesheets($combine, $debug);
        minify_include_custom_stylesheets($combine, $debug);
    ?>
    <!--[if IE 6]><link rel="stylesheet" type="text/css" media="all" href="<?php echo $static_base_url . '/' . sfTimestamp::getTimestamp('/static/css/ie6.css'); ?>/static/css/ie6.css" /><![endif]-->
    <!--[if IE 7]><link rel="stylesheet" type="text/css" media="all" href="<?php echo $static_base_url. '/' . sfTimestamp::getTimestamp('/static/css/ie7.css'); ?>/static/css/ie7.css" /><![endif]-->
    <!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <?php
        minify_include_head_javascripts($combine, $debug);
        echo auto_discovery_link_tag('rss', $rss);
        echo include_meta_links();
    ?>
    <link rel="search" type="application/opensearchdescription+xml" href="<?php echo $static_base_url; ?>/static/opensearch/description.xml" title="Camptocamp.org" />
    <link rel="shortcut icon" href="<?php
    if ($footer_type == 'cda')
    {
        $favicon = 'portals/cda_favicon.ico';
    }
    else
    {
        $favicon = 'favicon.ico';
    }
    echo $static_base_url . '/static/images/' . $favicon;
    ?>" />
</head>
<body>
    <div id="holder">
        <header id="page_header">
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
        </header>
        <div id="content_box">
            <?php echo $sf_data->getRaw('sf_content') ?>
            </div>
        </div>
        <?php
        include_partial('common/footer', array('sf_cache_key' => $footer_type . '_' . $lang,
                                               'lang_code' => $lang_code,
                                               'footer_type' => $footer_type,
                                               'html5' => true));
        ?>
    </div>
    <div id="fields_tooltip" class="ajax_feedback" style="display: none;" onclick="Element.hide(this); return false;"></div>
    <?php
    minify_include_body_javascripts($combine, $debug);
    minify_include_maps_javascripts($combine);
    include_partial('common/tracker', array('addthis' => sfContext::getInstance()->getResponse()->hasParameter('addthis', 'helper/asset/addthis')));
    ?>
</body>
</html>
