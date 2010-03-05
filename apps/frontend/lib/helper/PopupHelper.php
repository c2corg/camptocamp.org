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

function make_c2c_link($route, $size_ctrl = false, $raw = false)
{
    $html = '<p id="popup_link">';
    if ($raw)
    {
        $title = __('Show document');
    }
    else
    {
        $title = __('Show document on camptocamp.org');
    }
    $html .=  link_to($title, $route, array('target' => '_blank'));
    if ($size_ctrl)
    {
        $html .= '<span id="size_ctrl">'
                 . picto_tag('picto_images', __('Images'),
                       array('class' => 'click', 'id' => 'toggle_images'))
                 . picto_tag('picto_close', __('Reduce the list'),
                       array('class' => 'click', 'id' => 'close_popup_routes'))
                 . picto_tag('picto_open', __('Enlarge the list'),
                       array('class' => 'click', 'id' => 'open_popup_routes'))
                 . '</span>';
    }
    $html .= '</p>';
    
    $html .= javascript_tag('init_popup(this);');
    
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
        $output .= '<li style="display:none">' . image_tag(image_url($image['filename'], 'medium'),
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

function make_routes_title($title, $nb_routes, $size_ctrl = false)
{
    $output = '<h4 id="routes_title">';
    
    if ($nb_routes)
    {
        $output .= $title . __('&nbsp;:') . ' ' . $nb_routes;
        
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
