<?php
$lang_code = __('meta_language');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
$id = $sf_params->get('id');
$rss = ($id) ? "@document_feed?module=$module&id=$id&lang=$lang" : "@feed?module=$module&lang=$lang";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang_code ?>" lang="<?php echo $lang_code ?>">
<head>
    <?php
        echo include_http_metas();
        echo include_metas();
        echo include_title();
        echo auto_discovery_link_tag('rss', $rss);
    ?>
    <link rel="search" type="application/opensearchdescription+xml" href="/static/opensearch/description.xml" title="Camptocamp.org" />
    <link rel="shortcut icon" href="/static/images/favicon.ico" />
</head>

<body>

    <!--[if lt IE 7]>
        <link rel="stylesheet" type="text/css" media="all" href="/static/css/ie.css" />
    <![endif]-->
    <!--[if IE 7]>
        <link rel="stylesheet" type="text/css" media="all" href="/static/css/ie7.css" />
    <![endif]-->

    <div id="holder">
        <?php include_partial('common/header', array('lang_code' => $lang_code)); ?>
        <div id="content_box">
            <?php echo $sf_data->getRaw('sf_content') ?>
            </div> <!-- Fin wrapper_context -->
        </div>
        <?php
        include_partial('common/footer', array('sf_cache_key' => $lang_code));
        ?>
    </div>

    <div id="fields_tooltip" class="ajax_feedback" style="display: none;" onclick="Element.hide(this); return false;"></div>
</body>
</html>
