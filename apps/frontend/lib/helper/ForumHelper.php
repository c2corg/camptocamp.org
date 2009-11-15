<?php
use_helper('Link');

function f_link_to($name, $url = null, $html_options = null)
{
    $html_options['href'] = '/forums/' . $url;
    return content_tag('a', $name, $html_options);
}
