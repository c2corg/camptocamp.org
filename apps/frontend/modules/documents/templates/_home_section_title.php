<?php
$tr_module =  __($module);
$lang = $sf_user->getCulture();

if (!empty($custom_title_text))
{
    $title_text = trim($sf_data->getRaw('custom_title_text'));
}
else
{
    $title_text = __("Latest $module");
}

if (!isset($has_title_link))
{
    $has_title_link = true;
}
if (!isset($has_title_rss))
{
    $has_title_rss = $has_title_link;
}

if ($has_title_link)
{
    if (!empty($custom_title_link))
    {
        $title_link = $sf_data->getRaw('custom_title_link');
    }
    else
    {
        $title_link =  "@default_index?module=$module";
    }
}

if ($has_title_rss)
{
    // no rss link in mobile version
    if (!c2cTools::mobileVersion())
    {
        if (!empty($custom_rss_link))
        {
            $rss_link = $sf_data->getRaw('custom_rss_link');
        }
        else
        {
            $rss_link = "@creations_feed?module=$module&lang=$lang";
        }

        if (!empty($custom_rss))
        {
            $rss = $sf_data->getRaw('custom_rss');
        }
        else
        {
            $rss = link_to('', $rss_link,
                           array('class' => 'home_title_right picto_rss',
                                 'title' => __("Subscribe to latest $module creations"),
                                 'rel' => 'nofollow'));
        }
    }
    else
    {
        $rss = '';
    }
}
else
{
    $rss = '';
}

if (!empty($custom_title))
{
    $title = $sf_data->getRaw('custom_title');
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

if (!isset($home_section))
{
    $home_section = true;
}

$toggle = ' data-toggle-view="'. $section_id . '" title="' . __('section close') . '"';
if ($home_section)
{
    $cookie_position = array_search($section_id, sfConfig::get('app_personalization_cookie_fold_positions'));
    $toggle .= ' data-cookie-position="'. $cookie_position . '"';
}
?>
<div class="home_title" id="<?php echo $section_id; ?>_section_title"<?php
    if (!$has_title_link)
    {
        echo $toggle;
    }
    ?>>
    <div id="<?php echo $section_id; ?>_toggle" class="home_title_left picto_<?php echo $title_icon; ?>"<?php
    if ($has_title_link)
    {
        echo $toggle;
    }
    ?>></div>
    <?php echo $rss; ?>
    <div class="home_title_text">
        <?php echo $title; ?>
    </div>
</div>
