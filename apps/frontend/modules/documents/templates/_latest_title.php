<?php
$tr_module =  __($module);
$lang = $sf_user->getCulture();
$link = empty($link) ? "@default_index?module=$module" : htmlspecialchars_decode($link);
?>
<div class="home_title"><div class="home_title_left"></div><span class="home_title_text">
<?php
echo '<span class="home_title_' . $module . '" title="' . $tr_module . '">'. $tr_module .'</span>';
echo link_to(__("Latest $module"), $link);
?>
</span><span class="home_title_right">
<?php
echo link_to('',
             "@creations_feed?module=$module&lang=$lang",
             array('class' => 'home_title_rss',
                   'title' => __("Subscribe to latest $module creations")));
?>
</span></div>
