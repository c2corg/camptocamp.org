<?php
use_helper('Home', 'Language', 'Sections', 'Viewer', 'General', 'Field', 'AutoComplete', 'sfBBCode', 'SmartFormat', 'Button');

$culture = $sf_user->getCulture();
$connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$mobile_version = c2cTools::mobileVersion();
?>
<div class='column row1 span-9'>
  <h2><?php echo __('présentation')?></h2>
  <div class="content">
    <p>Une idée de <strong>sortie sans voiture ?</strong><br />Un bon plan <strong>produits locaux ?</strong><br />
changerdapproche.org ouvre la voie à plus de 10 000 itinéraires en montagne accessibles en transports en commun : randos à pieds, en raquette et à ski, voies d'escalades, alpinisme, parapente et cascades de glace.
<br />Mountain Wilderness et Camptocamp vous proposent la plus grande base de données européenne en faveur de la mobilité douce et de l'écotourisme en montagne. Une base de données ouverte qui va s'enrichir de vos sorties en montagne réalisées en transports collectifs. Une base de données participative pour partager les bons plans écotourisme des vallées. Le plaisir d'une sortie en montagne commence à votre porte !
    </p>
  </div>
</div>
<div class='column row1 span-5'>
  </a>
    <a href="<?php echo url_for('@cdasearch?lang=' . $culture); ?>">
      <?php
        $img_title = 'Écomobilité';
        echo image_tag('/static/images/cda/slide1.jpg',array('alt'=>__($img_title),'title'=>__($img_title)));
      ?>
      <div class="img_title"><?php echo __($img_title); ?></div>
    </a>
</div>
<div class='column row1 span-5'>
    <a href="<?php echo url_for('@document_by_id?module=articles&id=' . sfConfig::get('app_mw_contest_id') . '&lang=' . $culture); ?>">
      <?php
        $img_title = 'Concours';
        echo image_tag('/static/images/cda/slide2.jpg',array('alt'=>__($img_title),'title'=>__($img_title)));
      ?>
      <div class="img_title"><?php echo __($img_title); ?></div>
    </a>
</div>
<div class='column row1 last span-5'>
  <a href="<?php echo url_for('@default?action=list&module=images&owtp=yes'); ?>">
    <?php
      $img_title = 'En images';
      echo image_tag('/static/images/cda/slide3.jpg',array('alt'=>__($img_title),'title'=>__($img_title)));
    ?>
    <div class="img_title"><?php echo __($img_title); ?></div>
  </a>
  <br />
  <div class="image2">
    <a href="/forums/viewforum.php?id=42">
      <?php
        $img_title = 'Des questions ?';
        echo image_tag('/static/images/cda/slide4.jpg',array('alt'=>__($img_title),'title'=>__($img_title)));
      ?>
      <div class="img_title"><?php echo __($img_title); ?></div>
    </a>
  </div>
</div>
<div id='cda_page_footer'>
  <div class='column row2 span-9'>
    <h2><?php echo __('nos partenaires')?></h2>
    <div id="partenaires">
    </div>
  </div>
  <div class='column row2 last span-15'>
    <a href="/map?zoom=7&amp;lat=45.5&amp;lon=7&amp;layerNodes=public_transportations&amp;bgLayer=gmap_physical">
      <?php
      $img_title = 'Cartographie';
      echo image_tag('/static/images/cda/slide5.jpg',array('alt'=>__($img_title),'title'=>__($img_title)));
      ?>
      <div class="img_title"><?php echo __($img_title); ?></div>
    </a>
  </div>
</div>
<div class="fake_clear"> &nbsp;</div>
