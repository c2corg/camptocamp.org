<?php
/**
 * $Id: PaginationHelper.php 2523 2007-12-17 15:36:01Z alex $
 */

function _getSeparator($uri)
{
    return (preg_match('/\?/', $uri) ? '&' : '?');
}

function _getBaseUri()
{
    $context = sfContext::getInstance();
    return '/' . $context->getModuleName() . '/' . $context->getActionName();
}

function _addUrlParamters($uri, $params_to_ignore = array())
{
    $request = sfContext::getInstance()->getRequest();
    $request_parameters = $request->getParameterHolder()->getAll();
    
    $params_to_ignore = array_merge($params_to_ignore, array('module', 'action'));
    // remove action and module names and the escapted give by parameter
    foreach ($params_to_ignore as $param)
    {
        unset($request_parameters[$param]);
    }
    
    foreach($request_parameters as $key => $request_parameter)
    {
        if (!is_null($request_parameter))
        {
        	$uri .= _getSeparator($uri) . $key . '=' . $request_parameter;
        }
    }
    
    return $uri;
}

function pager_navigation($pager)
{
    $navigation = '';

    if ($pager->haveToPaginate())
    {
        $context = sfContext::getInstance();
        $request = $context->getRequest();
     
        $orderby = $request->getParameter('orderby');
        $order = $request->getParameter('order');
     
        $uri = _getBaseUri();
     
        if (!is_null($orderby))
        {
            $uri .= _getSeparator($uri) . 'orderby=' . $orderby;
        }
     
        if (!is_null($order))
        {
            $uri .= _getSeparator($uri) . 'order=' . $order;
        }
     
        $uri .= _addUrlParamters($uri);
        $uri .= _getSeparator($uri) . 'page=';

        $static_base_url = sfConfig::get('app_static_url');
     
        // First and previous pages
        if ($pager->getPage() != 1)
        {
            $navigation .= link_to(image_tag($static_base_url . '/static/images/picto/first.png',
                                             array('alt' => '<<',
                                                   'title' => __('first page'))),
                                   $uri . '1');
            $navigation .= '&nbsp;';
            $navigation .= link_to(image_tag($static_base_url . '/static/images/picto/back.png',
                                             array('alt' => '<',
                                                   'title' => __('previous page'))),
                                   $uri . $pager->getPreviousPage());
            $navigation .= '&nbsp;';
        }
     
        // Pages one by one
        $links = array();
        foreach ($pager->getLinks() as $page)
        {
            $links[] = link_to_unless($page == $pager->getPage(), $page, $uri.$page);
        }
        $navigation .= join('&nbsp;&nbsp;', $links);
     
        // Next and last pages
        if ($pager->getPage() != $pager->getLastPage())
        {
            $navigation .= '&nbsp;';
            $navigation .= link_to(image_tag($static_base_url . '/static/images/picto/next.png',
                                             array('alt' => '>',
                                                   'title' => __('next page'))),
                                   $uri . $pager->getNextPage());
            $navigation .= '&nbsp;';
            $navigation .= link_to(image_tag($static_base_url . '/static/images/picto/last.png',
                                            array('alt' => '>>',
                                                  'title' => __('last page'))),
                                   $uri . $pager->getLastPage());
        }
    }
   
    return '<div class="pages_navigation">' . $navigation . '</div>';
}

function header_list_tag($field_name, $label = NULL)
{
    $order = $page = '';
    
    $param_page = sfContext::getInstance()->getRequest()->getParameter('page');
    $param_order = sfContext::getInstance()->getRequest()->getParameter('order');
    $param_orderby = sfContext::getInstance()->getRequest()->getParameter('orderby');
    
    if (isset($param_page))
    {
        $page = '&page=' . $param_page;
    }
    
    if (isset($param_order))
    {
        if ($param_orderby == $field_name)
        {
            $order = '&order=' . (($param_order == 'asc') ? 'desc' : 'asc');
            $class = ($param_order == 'asc') ? 'order_desc' : 'order_asc';
        }
        else
        {
            $order = '&order=asc';
            $class = '';
        }
    }
    else
    {
        $order = '&order=' . sfConfig::get('app_list_default_order', 'asc');
        $class = '';
    }
    
    $uri ='?orderby=' . $field_name . $order . $page;
    $uri .= _addUrlParamters($uri, array('order', 'page', 'orderby'));

    if (!empty($label))
    {
        $label = __($label);
    }
    else
    {
        $label = __($field_name);
    }
    $label = str_replace(array(' :', ':'), '', $label);
    
    return '<th class="' . $class . '">' . link_to($label, _getBaseUri() . $uri, array('rel' => 'nofollow')) . '</th>';
}



function simple_header_list_tag($field_name)
{
    return '<th>' . __($field_name) . '</th>';
}

function images_header_list_tag()
{
    return '<th>' . image_tag(sfConfig::get('app_static_url') . '/static/images/picto/images.png',
                              array('title' => __('nb_images'))) . '</th>'; 
}

function comments_header_list_tag()
{
    return '<th>' . image_tag(sfConfig::get('app_static_url') . '/static/images/picto/comment.png',
                              array('title' => __('nb_comments'))) . '</th>';
}

function getTheBestLanguage($array, $modelName)
{
    return Language::getTheBest($array, $modelName);
}

function get_paginated_value($value, $config)
{
    if (is_null($value) || is_object($value))
    {
        return '';
    }

    $list = sfConfig::get($config);
    if (empty($list[$value]))
    {
        return '';
    }
    return __($list[$value]);
}

function get_paginated_value_from_list($value, $config)
{
    if (empty($value) || !is_string($value))
    {
        return '';
    }

    // FIXME: perform this conversion when retrieving data
    $value = BaseDocument::convertStringToArray($value);

    if (count($value) == 1)
    {
        return get_paginated_value($value[0], $config);
    }

    $list = sfConfig::get($config);
    $out = array();
    foreach ($value as $item)
    {
        if (!empty($list[$item]))
        {
            $out[] = __($list[$item]);
        }
    }
    return implode (', ', $out);
}

function get_paginated_activities($value)
{
    if (empty($value) || !is_string($value))
    {
        return '';
    }
    
    // FIXME: perform this conversion when retrieving data
    $value = BaseDocument::convertStringToArray($value);

    $activities = sfConfig::get('app_activities_list');
    $out = array();
    $static_base_url = sfConfig::get('app_static_url');
    foreach ($value as $item)
    {
        if (array_key_exists($item, $activities))
        {
            $activity = $activities[$item];
            $name = __($activity);
            $out[] = image_tag($static_base_url . '/static/images/picto/' . $activity . '_mini.png',
                               array('alt' => $name, 'title' => $name));
        }
    }
    return implode(' ', $out);
}

function displayWithSuffix($data, $suffix)
{
    if (empty($data))
    {
        return '';
    }

    return $data . ' ' . __($suffix);
}
