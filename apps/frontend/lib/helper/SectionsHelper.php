<?php
use_helper('Javascript');

function start_section_tag($label, $container_id, $state = 'opened', $map = false)
{
    $static_base_url = sfConfig::get('app_static_url');
    $picto_class = ($state == 'opened') ? 'picto_close' : 'picto_open';
    $status = ($state == 'opened') ? 'close' : 'open';
    $alt = ($state == 'opened') ? '-' : '+';

    $toggle = "toggleView('$container_id', '$map', '" . __('section close') . "', '" . __('section open') . "')";
    
    $label = __($label);
    $label .= '&nbsp;&nbsp;<span id="tip_' . $container_id . '">[' . __($state == 'opened' ? 'section close' : 'section open') . ']</span>';

    $html  = '<div class="article_titre_bg">'
           . '<a name="' . $container_id . '"></a>'
           . '<div class="action_toggle '. $picto_class . '" onclick="' . $toggle . '" id="' . 'toggle_' . $container_id
           . '" title="' . __("section $status") . '"></div>'
           . '<div class="title" id="' . $container_id . '_section_title" title="' . __("section $status") . '">'
           . link_to_function($label, $toggle) 
           . '</div>'
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
