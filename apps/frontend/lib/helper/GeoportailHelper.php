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
    
    // take first image available
    $image = $images[0];
    $caption = $image['name'];

    return '<div class="image">' .
           image_tag(image_url($image['filename'], 'small'),
                     array('alt' => $caption, 'title' => $caption)) .
           '</div>';
}
