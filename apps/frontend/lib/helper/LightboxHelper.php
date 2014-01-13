<?php

function addLbMinimalRessources()
{
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript('/static/js/lightbox.js');
    $response->addStylesheet('/static/css/lightbox.css');
}
