<?php
/**
 * Layout for the mobile version
 * We use html5, except for the forums
 */
$lang_code = __('meta_language');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
$footer_type = 'normal';
$action = sfContext::getInstance()->getActionName();
$id = $sf_params->get('id');

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
    ?>
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
    <?php
        echo include_title();
        minify_include_main_stylesheets($combine, $debug);
        minify_include_custom_stylesheets($combine, $debug);
        minify_include_head_javascripts($combine, $debug);
        echo include_meta_links();
    ?>
    <link rel="shortcut icon" href="<?php echo $static_base_url; ?>/static/images/favicon.ico" />
    <link rel="apple-touch-icon" href="<?php echo $static_base_url; ?>/static/images/apple-touch-icon.png" />
    <link rel="apple-touch-icon-precomposed" href="<?php echo $static_base_url; ?>/static/images/apple-touch-icon.png" />
</head>
<body>
    <div id="holder">
        <header id="page_header">
        <?php
        include_partial('common/mobile_header', array('lang_code' => $lang_code));
        ?>
        </header>
        <div id="content_box">
            <?php echo $sf_data->getRaw('sf_content') ?>
            </div>
        </div>
        <?php
        include_partial('common/mobile_footer', array('lang_code' => $lang_code,
                                                      'footer_type' => $footer_type,
                                                      'html5' => true));
        ?>
    </div>
    <div id="fields_tooltip" class="ajax_feedback" style="display: none;" onclick="Element.hide(this); return false;"></div>
    <?php minify_include_body_javascripts($combine, $debug);
          include_partial('common/tracker') ?>
</body>
</html>
