<?php
use_helper('Button');
$lang = $sf_user->getCulture();
$static_base_url = sfConfig::get('app_static_url');
$lang_class = 'lang_' . $lang;
?>
<div id="nav_buttons">

<form method="post" action="https://www.paypal.com/cgi-bin/webscr">
<input type="hidden" value="_xclick" name="cmd" />
<input type="hidden" value="registration@camptocamp.org" name="business" />
<input type="hidden" value="EUR" name="currency_code" />
<input type="hidden" value="<?php echo __('Donate to Camptocamp Association') ?>" name="item_name" />
<input type="hidden" value="http://camptocamp.org/" name="return" />
<p>
<input value="<?php echo __('donate') ?>" class="paypal-button" type="submit" />
</p>
</form>

<p>
<?php
echo link_to(content_tag('div', '',
                       array('class' => 'cc_gen ' . $lang_class, 'title' => 'Creative Commons')),
             getMetaArticleRoute('licenses'));
?>
</p>
</div>
