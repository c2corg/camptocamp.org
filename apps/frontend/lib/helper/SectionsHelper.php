<?php
use_helper('Javascript');

function start_section_tag($label, $container_id, $state = 'opened', $map = false)
{

    $filename = ($state == 'opened') ? 'close' : 'open';
    $image = image_tag("/static/images/ie/$filename.gif",
                       array('id' => 'toggle_' . $container_id,
                             'title' => __("section $filename"),
                             'alt' => $filename)
                      );

    $toggle = "toggleView('$container_id', '$map', '" . __("section close") . "', '" . __("section open") . "')";

    $html  = '<div class="article_titre_bg">';
    $html .= '<a name="' . $container_id . '"></a>';
    $html .= '<div class="action_toggle">' . link_to_function($image, $toggle) . '</div>';
    $html .= '<div class="title" id="' . $container_id . '_section_title">' . link_to_function(__($label), $toggle) . '</div>';
    $html .= '</div>';
    
    if (!$map)
    {
        $display = ($state == 'opened') ? '' : ' style="display:none;"';
        $html .= '<div id="' . $container_id . "_section_container\"$display><div>";
    }
    return $html;
}

function end_section_tag($map = false)
{
    return !$map ? '</div></div>' : '';
}
