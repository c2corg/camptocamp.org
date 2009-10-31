<?php
use_helper('Text', 'Field', 'sfBBCode', 'SmartFormat', 'MyImage', 'General');

function truncate_description($description, $route) {
    $more = '... <span class="more_text">' . link_to('[' . __('Read more') . ']', $route) . '</span>';
    return parse_links(parse_bbcode_simple(truncate_text($description, 500, $more)));
}

function make_c2c_link($route) {
    $html = '<p id="gp_link">';
    $html .= link_to(__('Show document on camptocamp.org'), $route, array('target' => '_blank'));
    $html .= '</p>';
    return $html;
}

function make_gp_title($title, $module) {
    return '<h3>'
         . '<span class="article_title_img img_title_' . $module . '"></span>'
         . $title
         . '</h3>';
}

function formate_thumbnail($images) {
    if (count($images) == 0) return '';

    $output = '<div id="gp_slideshow"><ul id="gp_slideimages">';

    foreach($images as $image) {
        $caption = $image['name'];
        $output .= '<li>' . image_tag(image_url($image['filename'], 'small'),
        array('alt' => $caption, 'title' => $caption)) . '</li>';
    }

    $output .= '</ul></div>';

    return $output;
}

function insert_popup_js()
{
    $static_base_url = sfConfig::get('app_static_url');
    $prototype_url = $static_base_url . sfConfig::get('sf_prototype_web_dir') . '/js/';
    $output = '<script type="text/javascript" src="' . $prototype_url . 'prototype.js"></script>'
             . '<script type="text/javascript" src="' . $prototype_url . 'scriptaculous.js"></script>'
             . '<script type="text/javascript" src="' . $prototype_url . 'effects.js"></script>'
             . '<script type="text/javascript" src="' . $static_base_url . '/static/js/fold.js?'
             . sfSVN::getHeadRevision('fold.js') .'"></script>'
             . '<script type="text/javascript" src="' . $static_base_url . '/static/js/popup.js?'
             . sfSVN::getHeadRevision('popup.js') .'"></script>';

    return $output;
}

function make_routes_title($title, $has_routes, $size_ctrl)
{
    $output = '<h4 id="routes_title">';
    
    if ($has_routes)
    {
        $output .= $title;
        
        if ($size_ctrl)
        {
            $output .= '<span id="size_ctrl">'
                     . picto_tag('picto_close', __('Reduce the list'),
                           array('class' => 'click', 'id' => 'close_popup_routes'))
                     . picto_tag('picto_open', __('Enlarge the list'),
                           array('class' => 'click', 'id' => 'open_popup_routes'))
                     . '</span>';
        }
    }
    else
    {
        $output .= __('No linked route');
    }
    
    $output .= '</h4>';
    
    return $output;
}
