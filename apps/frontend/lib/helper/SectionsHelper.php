<?php
use_helper('Javascript', 'General');

function start_section_tag($label, $container_id, $state = 'opened', $map = false, $first = false, $hide = false)
{
    $class = 'article_titre_bg';
    if ($first)
    {
        $class .= ' hfirst';
    }
    $picto_class = ($state == 'opened') ? 'picto_close' : 'picto_open';
    $status = __(($state == 'opened') ? 'section close' : 'section open');

    $toggle = "toggleView('$container_id')";
    
    $label = picto_tag($picto_class, '', array('id' => 'toggle_'.$container_id)) . __($label);
    $label .= '<span id="tip_' . $container_id . '" class="tips">[' . $status . ']</span>';

    $style = $hide ? '" style="display:none' : '';

    $html  = '<div class="' . $class . $style . '">'
           . '<a name="' . $container_id . '"></a>'
           . '<div class="title" id="' . $container_id . '_section_title" title="' . $status . '">'
           . link_to_function($label, $toggle) 
           . '</div>'
           . '</div>';
    
    if (!$map)
    {
        $display = ($state == 'opened' && !$hide) ? '' : ' style="display:none;"';
        $html .= '<div id="' . $container_id . '_section_container" class="section"' . $display . '>';
    }
    return $html;
}

function end_section_tag($map = false)
{
    return !$map ? '</div>' : '';
}
