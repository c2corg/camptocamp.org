<?php
$lang_code = __('meta_language');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
$id = $sf_params->get('id');
$static_base_url = sfConfig::get('app_static_url');

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
        echo include_meta_links();
    ?>
    <link rel="search" type="application/opensearchdescription+xml" href="<?php echo $static_base_url; ?>/static/opensearch/description.xml" 
          title="Camptocamp.org" />
    <link rel="shortcut icon" href="<?php echo $static_base_url; ?>/static/images/favicon.ico" />
</head>
<body>
    <?php echo $sf_data->getRaw('sf_content') ?>
    </div> <!-- Fin wrapper_context -->

    <?php include_partial('common/tracker') ?>
</body>
</html>
