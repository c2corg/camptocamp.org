<?php
/**
 * Helper containing globally used tools
 */

function formate_slug($search_name)
{
    $slug = html_entity_decode($search_name, ENT_QUOTES, 'UTF-8');
    $pattern = array('~[\W\s_]+~u', '~[^-\w]+~');
    $replace = array('-', '');
    $slug = preg_replace($pattern, $replace, $slug);
    $slug = trim($slug, '-');
    return substr($slug, 0, 100);
}

function get_slug($document)
{
    return formate_slug($document->get('search_name'));
}

function _keep_in_slug($str)
{
    return !is_null($str) && $str != '';
}
