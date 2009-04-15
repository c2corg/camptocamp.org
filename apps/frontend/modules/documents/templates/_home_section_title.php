<?php
$tr_module =  __($module);
$lang = $sf_user->getCulture();
$title_text = empty($custom_title_text) ? __("Latest $module") : $custom_title_text;
$title_link = empty($custom_title_link) ? "@default_index?module=$module" : htmlspecialchars_decode($custom_title_link);
$title = empty($custom_title) ? link_to($title_text, $title_link) : htmlspecialchars_decode($custom_title);
$rss_link = empty($custom_rss) ? link_to('',
                                         "@creations_feed?module=$module&lang=$lang",
                                         array('class' => 'home_title_right action_rss',
                                               'title' => __("Subscribe to latest $module creations")))
                               : htmlspecialchars_decode($custom_rss);
$title_icon = empty($custom_title_icon) ? $module : $custom_title_icon;
$section_id = empty($custom_section_id) ? "last_$module" : $custom_section_id;
$option1 = __('section close');
$option2 = __('section open');
$toggle = "toggleHomeSectionView('$section_id', '" . $option1 . "', '" . $option2 . "'); return false;";
$toggle_tooltip = $option1;
?>
<div class="home_title" id="<?php echo $section_id; ?>_section_title">
    <div id="<?php echo $section_id; ?>_toggle" class="home_title_left home_title_<?php echo $title_icon; ?>"
         onclick="<?php echo $toggle; ?>" title="<?php echo $toggle_tooltip; ?>"></div>
    <?php echo $rss_link; ?>
    <div class="home_title_text">
        <?php echo $title; ?>
    </div>
</div>
