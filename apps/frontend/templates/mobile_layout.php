<?php
/**
 * Layout for the mobile version
 * We use html5, except for the forums
 */
$lang_code = __('meta_language');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
$action = $sf_context->getActionName();
$id = $sf_params->get('id');
$cda_config = sfConfig::get('app_portals_cda');
$cda_id = isset($cda_config['id']) ? $cda_config['id'] : -1;
$footer_type = ($id == $cda_id) ? 'cda' : 'normal';

use_helper('MyMinify', 'MetaLink');

$static_base_url = sfConfig::get('app_static_url');
$response = sfContext::getInstance()->getResponse();

// alpine club logo is included by css, but only in en
if ($lang === 'en') use_stylesheet('/static/css/ac');
?>
<!doctype html>
<html lang="<?php echo $lang_code ?>" class="mobile">
<head>
    <meta charset="utf-8">
    <?php
        $debug = (bool) sfConfig::get('app_minify_debug');
        $combine = !$debug;
        echo include_http_metas();
        echo include_title();
    ?>
    <meta name="viewport" content="initial-scale=1">
    <?php // Mobile IE allows us to activate ClearType technology for smoothing fonts for easy reading ?>
    <meta http-equiv="cleartype" content="on">
    <?php
        // we remove title from metas, because we don't want a <meta name=title>
        $response->getParameterHolder()->remove('title', 'helper/asset/auto/meta');
        echo include_metas();
        echo '<link rel="canonical" href="http://' . $_SERVER['HTTP_HOST'] . ($_SERVER['REQUEST_URI'] != '/' ? $_SERVER['REQUEST_URI'] : '') . '" />';
    ?>
    <script type="text/javascript">
    (function(m,w){var l='<?php echo trim(minify_get_main_stylesheets($combine, $debug)); ?>',r=1
    w.devicePixelRatio?r=w.devicePixelRatio:"matchMedia"in w&&w.matchMedia&&(w.matchMedia("(min-resolution: 2dppx)").matches||w.matchMedia("(min-resolution: 192dpi)").matches?r=2:(w.matchMedia("(min-resolution: 1.5dppx)").matches||w.matchMedia("(min-resolution: 144dpi)").matches)&&(r=1.5))
    if(r>1){l=l.replace(m,m+'@'+(r>=2?2:1.5)+'x');}document.write(l);})('mobile',window);
    </script>
    <?php
        minify_include_custom_stylesheets($combine, $debug);
        echo include_meta_links();
    ?>
    <link rel="apple-touch-icon" href="<?php echo $static_base_url; ?>/apple-touch-icon.png">
</head>
<body>
    <?php include_partial('common/section_close'); ?>
    <div id="holder">
        <header id="page_header">
        <?php
        $header_partial = ($action == 'view' && $footer_type == 'cda') ? 'portals/cda_mobile_header' : 'common/mobile_header';
        include_partial($header_partial, array('lang_code' => $lang_code,
                                               'footer_type' => $footer_type));
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
        include_partial('common/mobile_footer', array('lang_code' => $lang_code,
                                                      'footer_type' => $footer_type));
        ?>
    </div>
    <?php minify_include_body_javascripts($combine, $debug);
          include_partial('common/tracker') ?>
</body>
</html>
