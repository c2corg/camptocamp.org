<?php

function addMetaLink($relation, $href)
{
  sfContext::getInstance()->getResponse()->setParameter($relation, $href, '/helper/asset/auto/metalink');
}

function include_meta_links()
{
    $meta_links = sfContext::getInstance()->getResponse()->getParameterHolder()->getAll('/helper/asset/auto/metalink');
    $out = '';
    foreach ($meta_links as $relation => $href)
    {
        $out .= '<link rel="'.$relation.'" href="'.$href.'" />';
    }
    return $out;
}
