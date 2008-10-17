<?php
use_helper('Javascript');

function start_section_tag($label, $container_id, $state = 'opened', $map = false)
{
    $static_base_url = sfConfig::get('app_static_url');
    $filename = ($state == 'opened') ? 'close' : 'open';
    $image = image_tag("$static_base_url/static/images/ie/$filename.gif",
                       array('id' => 'toggle_' . $container_id,
                             'title' => __("section $filename"),
                             'alt' => $filename)
                      );

    $toggle = "toggleView('$container_id', '$map', '" . __("section close") . "', '" . __("section open") . "')";

    $html  = '<div class="article_titre_bg">'
           . '<a name="' . $container_id . '"></a>'
           . '<div class="action_toggle">' . link_to_function($image, $toggle) . '</div>'
           . '<div class="title" id="' . $container_id . '_section_title" title="' . __("section $filename") . '">'
           . link_to_function(__($label), $toggle) . '</div>'
           . '</div>';
    
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
