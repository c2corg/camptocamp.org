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

function make_c2c_link($route, $raw = false, $has_image = false, $has_text = false, $has_list = false)
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
    
    $elements = array($has_image, $has_text, $has_list);
    $size_ctrl = array();
    foreach ($elements as $element)
    {
        if ($element)
        {
            $size_ctrl[] = true;
        }
    }
    if (count($size_ctrl) >= 2)
    {
        $html .= '<span id="popup_menu">'
                 . picto_tag('action_description', __('Mixed'),
                       array('class' => 'click', 'id' => 'popup_menu_mixed'));
        if ($has_image)
        {
            picto_tag('picto_images', __('Images'),
                       array('class' => 'click', 'id' => 'popup_menu_images'));
        }
        if ($has_text)
        {
            picto_tag('action_list', __('Text'),
                       array('class' => 'click', 'id' => 'popup_menu_text'));
        }
        if ($has_list)
        {
            picto_tag('picto_' . $has_list, __(ucfirst($has_list)),
                       array('class' => 'click', 'id' => 'popup_menu_list'));
        }
        $html .= '</span>';
    }
    $html .= '</p>';
    
    $html .= javascript_tag('init_popup();');
    
    return $html;
}

function make_popup_title($title, $module) {
    return '<h3>'
         . '<span class="article_title_img img_title_' . $module . '"></span>'
         . $title
         . '</h3>';
}

function make_thumbnail_slideshow($images) {
    if (!count($images)) return '';

    $output = '<div class="popup_slideshow"><ul class="popup_slideimages">';

    $count = 0;
    foreach($images as $image) {
        $count += 1;
        $caption = $image['name'];
        $style = ($count != 1) ? ' style="display:none"' : '';
        $output .= "<li$style>" . image_tag(image_url($image['filename'], 'medium'),
                                            array('alt' => $caption, 'title' => $caption))
                 . '</li>';
    }

    $output .= '</ul></div>';

    return $output;
}

function insert_popup_js()
{
    $static_base_url = sfConfig::get('app_static_url');
    $output = '<script type="text/javascript" src="' . $static_base_url . '/static/js/prototype.js"></script>'
             . '<script type="text/javascript" src="' . $static_base_url . '/static/js/scriptaculous.js"></script>'
             . '<script type="text/javascript" src="' . $static_base_url . '/static/js/effects.js"></script>'
             . '<script type="text/javascript" src="' . $static_base_url . '/static/js/fold.js"></script>'
             . '<script type="text/javascript" src="' . $static_base_url . '/static/js/popup.js"></script>';

    return $output;
}

function make_routes_title($title, $nb_routes)
{
    $output = '<h4 class="popup_list_title">';
    
    if ($nb_routes)
    {
        $output .= $title . __('&nbsp;:') . ' ' . $nb_routes;
    }
    else
    {
        $output .= __('No linked route');
    }
    
    $output .= '</h4>';
    
    return $output;
}
