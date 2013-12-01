<?php
use_helper('Javascript', 'General');

function start_section_tag($label, $container_id, $state = 'opened', $map = false, $first = false, $hide = false, $show_tip = true)
{
    $class = 'article_titre_bg';
    if ($first)
    {
        $class .= ' hfirst';
    }
    $picto_class = ($state == 'opened') ? 'picto_close' : 'picto_open';
    $status = __(($state == 'opened') ? 'section close' : 'section open');

    $label = picto_tag($picto_class, '', array('id' => $container_id . '_toggle')) . __($label);
    if ($show_tip && !c2cTools::mobileVersion())
    {
        $label .= '<span id="tip_' . $container_id . '" class="tips">[' . $status . ']</span>';
        $up = '<a href="#header">'
            . picto_tag('action_up', __('menu'), array('class' => 'go_up'))
            . '</a>';
    }
    else
    {
        $up = '';
    }
    
    $style = $hide ? '" style="display:none' : '';

    $html  = '<div class="' . $class . '" id="' . $container_id . '_tbg' . $style . '">'
           . '<div class="title" id="' . $container_id . '_section_title" title="' . $status . '">'
           . '<a href="#" id="' . $container_id . '" data-toggle-view="' . $container_id . '">' . $label . '</a>'
           . $up
           . '</div>'
           . '</div>';
    
    if (!$map)
    {
        $display = ($state == 'opened' && !$hide) ? '' : ' style="display:none;"';
        $html .= '<section id="' . $container_id . '_section_container" class="section"' . $display . '>';
    }
    return $html;
}

function end_section_tag($map = false)
{
    return !$map ? '</section>' : '';
}
