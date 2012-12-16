<?php
use_helper('Home', 'Language', 'Sections', 'Viewer', 'General', 'Field', 'AutoComplete', 'sfBBCode', 'SmartFormat', 'Button');

$culture = $sf_user->getCulture();
$connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$mobile_version = c2cTools::mobileVersion();
?>
<div class='column row1 span-9'>
  <h2><?php echo __('presentation')?></h2>
  <div class="content">
    <p><?php echo __('cda presentation'); ?></p>
  </div>
</div>
<div class='column row1 span-5'>
    <a href="<?php echo url_for('@cdasearch'); ?>">
      <?php
        $img_title = __('ecomobility');
        echo image_tag('/static/images/cda/slide1.jpg',array('alt'=>__($img_title),'title'=>__($img_title)));
      ?>
      <div class="img_title"><?php echo __($img_title); ?></div>
    </a>
</div>
<div class='column row1 span-5'>
    <a href="http://ecrins.changerdapproche.org" target="_blank">
      <?php
        $img_title = '&eacute;crins';
        echo image_tag('/static/images/cda/slide8_small.jpg',array('alt'=>__($img_title),'title'=>__($img_title)));
      ?>
      <div class="img_title"><?php echo __($img_title); ?></div>
    </a>
  <div class="image2">
    <a href="http://www.mountainwilderness.fr/component/content/article/3031" target="_blank">
      <?php
        $img_title = __('contest');
        echo image_tag('/static/images/cda/slide2_small.jpg',array('alt'=>__($img_title),'title'=>__($img_title)));
      ?>
      <div class="img_title"><?php echo __($img_title); ?></div>
    </a>
  </div>
</div>
<div class='column row1 last span-5'>
  <a href="<?php echo url_for('@default?action=list&module=images&owtp=yes&orderby=oid&order=desc'); ?>" target="_blank">
    <?php
      $img_title = __('picturial');
      echo image_tag('/static/images/cda/slide3.jpg',array('alt'=>__($img_title),'title'=>__($img_title)));
    ?>
    <div class="img_title"><?php echo __($img_title); ?></div>
  </a>
  <div class="image2">
    <a href="/forums/viewforum.php?id=42" target="_blank">
      <?php
        $img_title = __('questions?');
        echo image_tag('/static/images/cda/slide4.jpg',array('alt'=>__($img_title),'title'=>__($img_title)));
      ?>
      <div class="img_title"><?php echo __($img_title); ?></div>
    </a>
  </div>
</div>
<div id='cda_page_footer'>
  <div class='column row2 span-9'>
    <h2><?php echo __('our partners')?></h2>
    <div id="partenaires">
    </div>
  </div>
  <div class='column row2 last span-15'>
    <a href="/map?zoom=7&lat=44.5&lon=3.3&layerNodes=public_transportations&bgLayer=gmap_physical" target="_blank">
      <?php
      $img_title = __('map (cda)');
      echo image_tag('/static/images/cda/slide5.jpg',array('alt'=>__($img_title),'title'=>__($img_title)));
      ?>
      <div class="img_title"><?php echo __($img_title); ?></div>
    </a>
  </div>
</div>
<div class="fake_clear"> &nbsp;</div>
