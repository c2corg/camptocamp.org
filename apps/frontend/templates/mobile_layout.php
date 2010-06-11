<?php
/**
 * Layout for the mobile version (under construction....)
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
        minify_include_main_stylesheets($combine, $debug);
        minify_include_custom_stylesheets($combine, $debug);
        minify_include_head_javascripts($combine, $debug);
        echo include_meta_links();
    ?>
    <link rel="shortcut icon" href="<?php echo $static_base_url; ?>/static/images/favicon.ico" />
</head>
<body>
    <div id="holder">
        <div id="page_header">
        <?php
        include_partial('common/header', array('lang_code' => $lang_code));
        ?>
        </div>
        <div id="content_box">
            <?php echo $sf_data->getRaw('sf_content') ?>
            </div>
        </div>
        <?php
        include_partial('common/footer', array('lang_code' => $lang_code,
                                               'footer_type' => $footer_type));
        ?>
    </div>

    <div id="fields_tooltip" class="ajax_feedback" style="display: none;" onclick="Element.hide(this); return false;"></div>

    <?php minify_include_body_javascripts($combine, $debug);
          minify_include_unminified_javascripts(); ?>

    <?php include_partial('common/tracker') ?>
</body>
</html>
