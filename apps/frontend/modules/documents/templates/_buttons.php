<?php
use_helper('Button');
$lang_class = 'lang_' . $sf_user->getCulture();
?>
<!--[if IE 7]><div id="nav_buttons" class="nav_box"><![endif]-->
<form method="post" action="https://www.paypal.com/cgi-bin/webscr">
<!--[if IE 7]> <![if !IE]> <![endif]-->
<div id="nav_buttons" class="nav_box">
<!--[if IE 7]> <![endif]> <![endif]-->
<input type="hidden" value="_xclick" name="cmd" />
<input type="hidden" value="registration@camptocamp.org" name="business" />
<input type="hidden" value="EUR" name="currency_code" />
<input type="hidden" value="<?php echo __('Donate to Camptocamp Association') ?>" name="item_name" />
<input type="hidden" value="http://camptocamp.org/" name="return" />
<p>
<input value="<?php echo __('donate') ?>" id="paypal-button" type="submit" />
</p>

<p>
<?php
echo link_to(content_tag('span', '',
                       array('class' => 'cc_gen ' . $lang_class, 'title' => 'Creative Commons')),
             getMetaArticleRoute('licenses'));
?>
</p>

<p><?php echo buttons_facebook_twitter_c2c(); ?></p>
</div>
</form>
