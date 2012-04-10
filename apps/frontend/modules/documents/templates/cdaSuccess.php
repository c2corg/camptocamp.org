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
  </a>
    <a href="<?php echo url_for('@cdasearch?lang=' . $culture); ?>">
      <?php
        $img_title = __('Ecomobility');
        echo image_tag('/static/images/cda/slide1.jpg',array('alt'=>__($img_title),'title'=>__($img_title)));
      ?>
      <div class="img_title"><?php echo __($img_title); ?></div>
    </a>
</div>
<div class='column row1 span-5'>
    <a href="<?php echo url_for('@document_by_id?module=articles&id=' . sfConfig::get('app_mw_contest_id') . '&lang=' . $culture); ?>">
      <?php
        $img_title = __('Contest');
        echo image_tag('/static/images/cda/slide2.jpg',array('alt'=>__($img_title),'title'=>__($img_title)));
      ?>
      <div class="img_title"><?php echo __($img_title); ?></div>
    </a>
</div>
<div class='column row1 last span-5'>
  <a href="<?php echo url_for('@default?action=list&module=images&owtp=yes'); ?>">
    <?php
      $img_title = __('Picturial');
      echo image_tag('/static/images/cda/slide3.jpg',array('alt'=>__($img_title),'title'=>__($img_title)));
    ?>
    <div class="img_title"><?php echo __($img_title); ?></div>
  </a>
  <br />
  <div class="image2">
    <a href="/forums/viewforum.php?id=42">
      <?php
        $img_title = __('Questions?');
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
    <a href="/map?zoom=7&amp;lat=45.5&amp;lon=7&amp;layerNodes=public_transportations&amp;bgLayer=gmap_physical">
      <?php
      $img_title = __('Map');
      echo image_tag('/static/images/cda/slide5.jpg',array('alt'=>__($img_title),'title'=>__($img_title)));
      ?>
      <div class="img_title"><?php echo __($img_title); ?></div>
    </a>
  </div>
</div>
<div class="fake_clear"> &nbsp;</div>
