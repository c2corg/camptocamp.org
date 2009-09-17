<?php
use_helper('Javascript', 'General');

function start_section_tag($label, $container_id, $state = 'opened', $map = false)
{
    $picto_class = ($state == 'opened') ? 'picto_close' : 'picto_open';
    $status = ($state == 'opened') ? 'close' : 'open';

    $toggle = "toggleView('$container_id', '$map')";
    
    $label = picto_tag($picto_class, '', array('id' => 'toggle_'.$container_id)) . __($label);
    $label .= '<span id="tip_' . $container_id . '" class="tips">[' . __($state == 'opened' ? 'section close' : 'section open') . ']</span>';

    $html  = '<div class="article_titre_bg">'
           . '<a name="' . $container_id . '"></a>'
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
