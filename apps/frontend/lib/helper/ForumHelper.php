<?php
use_helper('Link');

function f_link_to($name, $url = null, $html_options = null)
{
    return link_to($name, '/forums/' . $url, $html_options);
}