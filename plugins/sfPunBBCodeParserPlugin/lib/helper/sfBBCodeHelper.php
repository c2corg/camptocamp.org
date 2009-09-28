<?php
/**
 * $Id: sfBBCodeHelper.php 1754 2007-09-22 14:24:25Z alex $
 */

function parse_bbcode($unformatted, $images = null, $filter_image_type = true, $show_images = true) {
    return sfPunBBCodeParser::parse_message($unformatted, $images, $filter_image_type, $show_images);
}

function parse_bbcode_simple($unformatted) {
    return sfPunBBCodeParser::parse_message_simple($unformatted);
}

function parse_bbcode_abstract($unformatted) {
    return sfPunBBCodeParser::parse_message_abstract($unformatted);
}
