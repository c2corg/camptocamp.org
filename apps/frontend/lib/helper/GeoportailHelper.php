<?php
use_helper('Text', 'Field', 'sfBBCode', 'SmartFormat');

function truncate_description($description, $route) {
    $more = '... <span class="more_text">' . link_to('[Lire la suite]', $route) . '</span>';
    return '<p>' . parse_links(parse_bbcode(truncate_text($description, 500, $more))) . '</p>';
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
