<?php
/**
 * Home helpers
 */

function nav_title($id, $title, $icon)
{
    $option1 = __('section close');
    $option2 = __('section open');
    $html = '<div id="nav_' . $id . '_section_title" class="nav_box_title" title="' . $option1 .
            '" onclick="toggleHomeSectionView(\'nav_' . $id . '\', \'' . $option1 .
            '\', \'' . $option2 . '\'); return false;">';
    $html .= '<div id="nav_' . $id . '_toggle" class="nav_box_image picto_' . $icon . '"></div>';
    $html .= '<div class="nav_box_title_text">' . $title . '</div>';
    $html .= '</div>';
    return $html;
}
?>
