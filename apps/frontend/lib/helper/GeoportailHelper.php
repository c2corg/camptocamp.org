<?php
use_helper('Text', 'Field', 'sfBBCode', 'SmartFormat', 'MyImage');

function truncate_description($description, $route) {
    $more = '... <span class="more_text">' . link_to('[Lire la suite]', $route) . '</span>';
    return parse_links(parse_bbcode_simple(truncate_text($description, 500, $more)));
}

function make_c2c_link($route) {
    $html = '<p id="gp_link">';
    $html .= link_to('Voir la fiche sur camptocamp.org', $route, array('target' => '_blank'));
    $html .= '</p>';
    return $html;
}

function make_gp_title($title, $module) {
    return '<h3 class="gp_' . $module . '">' . $title . '</h3>';
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

    if (count($images) > 1) {
        $output .= '<script type="text/javascript" src="/sfPrototypePlugin/js/prototype.js"></script>'
                 . '<script type="text/javascript" src="/sfPrototypePlugin/js/scriptaculous.js"></script>'
                 . '<script type="text/javascript" src="/sfPrototypePlugin/js/effects.js"></script>'
                 . '<script type="text/javascript" src="/static/js/geoportail.js"></script>';
    }

    return $output;
}
