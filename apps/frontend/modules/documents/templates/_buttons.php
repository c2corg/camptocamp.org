<?php
use_helper('Button');
$lang = $sf_user->getCulture();
$static_base_url = sfConfig::get('app_static_url');
?>
<div id="nav_buttons">

<form method="post" action="https://www.paypal.com/cgi-bin/webscr">
<input type="hidden" value="_xclick" name="cmd" />
<input type="hidden" value="registration@camptocamp.org" name="business" />
<input type="hidden" value="EUR" name="currency_code" />
<input type="hidden" value="<?php echo __('Donate to Camptocamp Association') ?>" name="item_name" />
<input type="hidden" value="http://camptocamp.org/" name="return" />
<p align="center">
  <input value="<?php echo __('donate') ?>" class="paypal-button" type="submit">
</p>
</form>

<p align="center">
<?php
$cc_file = ($lang == 'fr') ? 'cc_fr.gif' : 'cc_en.gif';
echo link_to(image_tag("$static_base_url/static/images/$cc_file", array('title' => 'Creative Commons', 'alt' => 'CC')),
             getMetaArticleRoute('licenses'));
?>
</p>
</div>
