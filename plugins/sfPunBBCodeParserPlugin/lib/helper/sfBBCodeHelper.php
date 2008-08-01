<?php
/**
 * $Id: sfBBCodeHelper.php 1754 2007-09-22 14:24:25Z alex $
 */

function parse_bbcode($unformatted) {
    return sfPunBBCodeParser::parse_message($unformatted);
}

function parse_bbcode_simple($unformatted) {
    return sfPunBBCodeParser::parse_message_simple($unformatted);
}
