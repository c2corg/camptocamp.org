<?php
use_helper('Text', 'Field', 'sfBBCode', 'SmartFormat', 'MyImage', 'General');

function truncate_description($description, $route, $length = 500, $has_abstract = false) {
    if ($has_abstract)
    {
        $description = extract_abstract($description);
    }
    $more = '... <span class="more_text">' . link_to('[' . __('Read more') . ']', $route) . '</span>';
    return parse_links(parse_bbcode_simple(truncate_text($description, $length, $more)));
}

function make_c2c_link($route) {
    $html = '<p id="popup_link">';
    $html .= link_to(__('Show document on camptocamp.org'), $route, array('target' => '_blank'));
    $html .= '</p>';
    return $html;
}

function make_popup_title($title, $module) {
    return '<h3>'
         . '<span class="article_title_img img_title_' . $module . '"></span>'
         . $title
         . '</h3>';
}

function formate_thumbnail($images) {
    if (count($images) == 0) return '';

    $output = '<div id="popup_slideshow"><ul id="popup_slideimages">';

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
             . '<script type="text/javascript" src="' . $static_base_url . '/static/js/fold.js"></script>'
             . '<script type="text/javascript" src="' . $static_base_url . '/static/js/popup.js"></script>';

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
