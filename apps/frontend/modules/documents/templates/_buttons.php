<?php
use_helper('Button');
$lang = $sf_user->getCulture();

switch ($lang) {
    case 'fr': $donate_file = 'donate_fr.gif'; break;
    case 'es': $donate_file = 'donate_es.gif'; break;
    default: $donate_file = 'donate_en.gif';
}
?>
<div id="nav_buttons">

<form method="post" action="https://www.paypal.com/cgi-bin/webscr">
<input type="hidden" value="_xclick" name="cmd" />
<input type="hidden" value="registration@camptocamp.org" name="business" />
<input type="hidden" value="EUR" name="currency_code" />
<input type="hidden" value="<?php echo __('Donate to Camptocamp Association') ?>" name="item_name" />
<input type="hidden" value="http://camptocamp.org/" name="return" />
<p align="center">
  <input type="image" id="donate" src="/static/images/<?php echo $donate_file ?>" alt="PayPal - Donate" />
</p>
</form>

<p align="center">
<?php
$cc_file = ($lang == 'fr') ? 'cc_fr.gif' : 'cc_en.gif';
echo link_to(image_tag("/static/images/$cc_file"), getMetaArticleRoute('licenses'));
?>
</p>
</div>
