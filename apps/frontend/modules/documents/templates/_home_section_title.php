<?php
$tr_module =  __($module);
$lang = $sf_user->getCulture();
$title_text = empty($custom_title_text) ? __("Latest $module") : htmlspecialchars_decode($custom_title_text);
if (!isset($has_title_link))
{
    $has_title_link = true;
}
if ($has_title_link)
{
    $title_link = empty($custom_title_link) ? "@default_index?module=$module" : htmlspecialchars_decode($custom_title_link);
    $rss_link = empty($custom_rss_link) ? "@creations_feed?module=$module&lang=$lang" : htmlspecialchars_decode($custom_rss_link);
    $rss = empty($custom_rss) ? link_to('', $rss_link,
                                             array('class' => 'home_title_right picto_rss',
                                                   'title' => __("Subscribe to latest $module creations")))
                                   : htmlspecialchars_decode($custom_rss);

}
else
{
    $rss = '';
}
if (!empty($custom_title))
{
    $title = htmlspecialchars_decode($custom_title);
}
elseif ($has_title_link)
{
    $title = link_to($title_text, $title_link);
}
else
{
    $title = $title_text;
}
$title_icon = empty($custom_title_icon) ? $module : $custom_title_icon;
$section_id = empty($custom_section_id) ? "last_$module" : $custom_section_id;
$option1 = __('section close');
$option2 = __('section open');
if (!isset($home_section))
{
    $home_section = true;
}
if ($home_section)
{
    $cookie_position = array_search($section_id, sfConfig::get('app_personalization_cookie_fold_positions'));
    $toggle = "toggleHomeSectionView('$section_id', $cookie_position); return false;";
}
else
{
    $toggle = "toggleView('$section_id'); return false;";
}
$toggle_tooltip = $option1;
$onclick = ' onclick="' . $toggle . '" title="' . $toggle_tooltip . '"';
?>
<div class="home_title" id="<?php echo $section_id; ?>_section_title"<?php
    if (!$has_title_link)
    {
        echo $onclick;
    }
    ?>>
    <div id="<?php echo $section_id; ?>_toggle" class="home_title_left picto_<?php echo $title_icon; ?>"<?php
    if ($has_title_link)
    {
        echo $onclick;
    }
    ?>></div>
    <?php echo $rss; ?>
    <div class="home_title_text">
        <?php echo $title; ?>
    </div>
</div>
