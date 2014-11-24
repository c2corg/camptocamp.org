<?php
$lang_code = __('meta_language');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
$action = sfContext::getInstance()->getActionName();
$type = $sf_params->get('type');
$id = $sf_params->get('id');
$mw_contest_id = sfConfig::get('app_mw_contest_id');

use_helper('MyMinify', 'MetaLink', 'Forum', 'Link', 'Language', 'Ajax');

$static_base_url = sfConfig::get('app_static_url');
$response = sfContext::getInstance()->getResponse();
?>
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
        echo '<link rel="canonical" href="http://' . $_SERVER['HTTP_HOST'] . ($_SERVER['REQUEST_URI'] != '/' ? $_SERVER['REQUEST_URI'] : '') . '" />';
        minify_include_main_stylesheets($combine, $debug);
        minify_include_custom_stylesheets($combine, $debug);
    ?>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo  minify_get_combined_files_url('/static/css/cda.css', $debug); ?>" />
    <!--[if IE 7]><link rel="stylesheet" type="text/css" media="all" href="<?php echo minify_get_combined_files_url('/static/css/ie7.css'); ?>" /><![endif]-->
     <!--[if lt IE 9]><script src="<?php echo minify_get_combined_files_url(array('/static/js/html5shiv.js','/static/js/autofocus.js', '/static/js/indexof.js'), $debug) ?>"></script><![endif]-->
</head>
<body>
<div id="holder">
  <div id='top'>
    <div id='topright'>
      <?php echo select_interface_language() ?>
    </div>
  </div>
  <header id="page_header">
    <?php echo ajax_feedback(); ?>
    <map name="map">
      <area shape="rect" coords="579,153,746,170" alt="<?php echo __('Moutainwilderness') ?>" href="http://www.mountainwilderness.fr" target="_blank" />
      <area shape="rect" coords="758,144,828,179" alt="camptocamp.org" href="http://www.camptocamp.org" target="_blank" />
    </map>
        <a href="/cda"><?php echo image_tag('/static/images/cda/bandeau.jpg',array('alt'=>__('changerdapproche'),'usemap'=>"#map")) ?></a>
    
    <?php
    if ($action != "cda"):
    ?>
    <div id="menu">
      <ul>
        <li class="active"><?php echo link_to(__('ecomobility'), '@cdasearch'); ?></li>
        <li><a href="http://www.mountainwilderness.fr/component/content/article/3031" target="_blank"><?php echo __('contest') ?></a></li>
        <li><?php echo link_to(__('picturial'), '@default?module=images&action=list&owtp=yes&orderby=oid&order=desc', array('target' => '_blank')); ?></li>
        <li><?php echo f_link_to(__('questions?'), 'viewforum.php?id=42', array('target' => '_blank')); ?></li>
        <li><a href="/map?zoom=7&lat=44.5&lon=3.3&layerNodes=public_transportations&bgLayer=gmap_physical" target="_blank"><?php echo __('map (cda)'); ?></a></li>
      </ul>
    </div>
    <?php endif; ?>
  </header>
  <div id="container">
      <?php echo $sf_data->getRaw('sf_content') ?>
  </div>
  <?php
  if ($action != "cda"):
  ?>
  <div id="page_footer">
    <div class="column span-24">
      <div id="partenairesbas">
        <ul>
          <li><img alt="FEDER Alpes" title="FEDER Alpes" src="/static/images/cda/FEDER-Alpes_70.jpg"></li>
          <li><img alt="EUROPE" title="EUROPE" src="/static/images/cda/EUROPE_70.jpg"></li>
          <li><img alt="DATAR" title="DATAR" src="/static/images/cda/datar.jpg"></li>
          <li><img alt="Rhônes Alpes" title="Rhônes Alpes" src="/static/images/cda/rhones-alpes_70.jpg"></li>
          <li><img alt="Conseil Général 06" title="Conseil Général 06" src="/static/images/cda/alpemaritime.jpg"></li>
          <li><img alt="Languedoc Rousillon" title="Languedoc Rousillon" src="/static/images/cda/LanguedoRousillon.jpg"></li>
          <li><img alt="Aquitaine" title="Aquitaine" src="/static/images/cda/Aquitaine.jpg"></li>
          <li><img alt="PACA" title="PACA" src="/static/images/cda/paca_70.jpg"></li>
        </ul>
        <p class="credentials"><?php echo __('cda partners tips'); ?></p>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>
    <?php
    minify_include_body_javascripts($combine, $debug);
    minify_include_maps_javascripts($combine);
    include_partial('common/tracker', array('addthis' => sfContext::getInstance()->getResponse()->hasParameter('addthis', 'helper/asset/addthis')));
    // Prompt ie6 users to install Chrome Frame - no adm rights required. chromium.org/developers/how-tos/chrome-frame-getting-started ?>
    <!--[if lt IE 7 ]><script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script><script>window.attachEvent("onload",function(){CFInstall.check({mode:"overlay"})})</script><![endif]-->
</body>
</html>
