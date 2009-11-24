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

function _addParameters($uri, $params = array())
{
    foreach($params as $name => $value)
    {
        if (!is_null($value))
        {
            $uri .= _getSeparator($uri) . $name . '=' . $value;
        }
    }
    
    return $uri;
}

function _addUrlParameters($uri, $params_to_ignore = array())
{
    $request = sfContext::getInstance()->getRequest();
    $request_parameters = $request->getParameterHolder()->getAll();
    
    $params_to_ignore = array_merge($params_to_ignore, array('module', 'action'));
    // remove action and module names and the escapted give by parameter
    foreach ($params_to_ignore as $param)
    {
        unset($request_parameters[$param]);
    }
    
    $uri = _addParameters($uri, $request_parameters);
    
    return $uri;
}

function packUrlParameters($uri = '', $params_to_ignore = array(), $condensed = true)
{
    $request = sfContext::getInstance()->getRequest();
    $request_parameters = $request->getParameterHolder()->getAll();
    
    $params_to_ignore = array_merge($params_to_ignore, array('module', 'action'));
    // remove action and module names and the escapted give by parameter
    foreach ($params_to_ignore as $param)
    {
        unset($request_parameters[$param]);
    }
    
    $params = array();
    $separator = $condensed ? '/' : '=';
    foreach($request_parameters as $key => $request_parameter)
    {
        if (!is_null($request_parameter))
        {
        	$params[] = $key . $separator . $request_parameter;
        }
    }
    $separator = $condensed ? '/' : '&';
    $params = implode($separator, $params);
    
    return $uri . $params;
}

function unpackUrlParameters($params, &$out)
{
    $params = explode('/', $params);
    $names = $values = array();
    $is_name = true;
    foreach ($params as $param)
    {
        if ($is_name)
        {
            $names[] = $param;
        }
        else
        {
            $values[] = $param;
        }
        $is_name = !$is_name;
    }
    
    foreach ($names as $key => $name)
    {
        if (isset($values[$key]))
        {
            $out[] = $name . '=' . $values[$key];
        }
        else
        {
            break;
        }
    }
}

function pager_navigation($pager)
{
    sfLoader::loadHelpers(array('General'));

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
     
        $uri .= _addUrlParameters($uri);
        $uri .= _getSeparator($uri) . 'page=';

        $static_base_url = sfConfig::get('app_static_url');
     
        // First and previous pages
        if ($pager->getPage() != 1)
        {
            $navigation .= link_to(picto_tag('action_first', __('first page')),
                                   $uri . '1');
            $navigation .= '&nbsp;';
            $navigation .= link_to(picto_tag('action_back', __('previous page')),
                                   $uri . $pager->getPreviousPage());
            $navigation .= '&nbsp;&nbsp;';
        }
     
        // Pages one by one
        $links = array();
        foreach ($pager->getLinks() as $page)
        {
            $links[] = link_to_unless($page == $pager->getPage(), $page, $uri.$page);
        }
        $navigation .= implode('&nbsp;&nbsp;', $links);
     
        // Next and last pages
        if ($pager->getPage() != $pager->getLastPage())
        {
            $navigation .= '&nbsp;&nbsp;';
            $navigation .= link_to(picto_tag('action_next', __('next page')),
                                   $uri . $pager->getNextPage());
            $navigation .= '&nbsp;';
            $navigation .= link_to(picto_tag('action_last', __('last page')),
                                   $uri . $pager->getLastPage());
        }
    }
   
    return '<div class="pages_navigation">' . $navigation . '</div>';
}

/* simple pager that will show the current div and display the selected one instead */
function simple_pager_navigation($current_page, $nb_pages, $div_prefix)
{
    sfLoader::loadHelpers(array('General'));

    $navigation = '';
    $static_base_url = sfConfig::get('app_static_url');

    if ($current_page != 0)
    {
        $navigation .= link_to_function(picto_tag('action_first', __('first page')),
                                        "new Effect.BlindUp($('${div_prefix}$current_page'));new Effect.BlindDown($('${div_prefix}0'))");
        $navigation .= '&nbsp;';
        $navigation .= link_to_function(picto_tag('action_back', __('previous page')),
                                        "new Effect.BlindUp($('${div_prefix}$current_page'));new Effect.BlindDown($('${div_prefix}".($current_page-1)."'))");
        $navigation .= '&nbsp;';
    }

    $links = array();
    $tmp = $current_page - 2;
    $check = $nb_pages - 5;
    $limit = ($check > 0) ? $check : 0;
    $begin = ($tmp > 0) ? (($tmp > $limit) ? $limit : $tmp) : 0;
    $i = $begin;

    while (($i < $begin + 5) && ($i < $nb_pages))
    {
        $links[] = ($i == $current_page) ? $i+1 :
                   link_to_function($i+1, "new Effect.BlindUp($('${div_prefix}$current_page'));new Effect.BlindDown($('${div_prefix}$i'))");
        $i++;
    }
    $navigation .= join('&nbsp;&nbsp;', $links);

    if ($current_page != $nb_pages-1)
    {
        $navigation .= '&nbsp;';
        $navigation .= link_to_function(picto_tag('action_next', __('next page')),
                                        "new Effect.BlindUp($('${div_prefix}$current_page'));new Effect.BlindDown($('${div_prefix}".($current_page+1)."'))");
        $navigation .= '&nbsp;';
        $navigation .= link_to_function(picto_tag('action_last', __('last page')),
                                        "new Effect.BlindUp($('${div_prefix}$current_page'));new Effect.BlindDown($('${div_prefix}".($nb_pages-1)."'))");
    }

    return '<div class="pages_navigation">' . $navigation . '</div>';
}

function link_to_default_order($label, $default_label)
{
    $param_orderby = sfContext::getInstance()->getRequest()->getParameter('orderby');
    
    if (isset($param_orderby))
    {
        $uri = _addUrlParameters('', array('orderby', 'order', 'page'));
        return link_to($label, _getBaseUri() . $uri);
    }
    else
    {
        return $default_label;
    }
}

function link_to_conditions($label)
{
    $uri = '/outings/conditions';
    $uri .= _addUrlParameters($uri, array('order', 'page', 'orderby'));
    $params = array('orderby' => 'date', 'order' => 'desc');
    $uri = _addParameters($uri, $params);
    
    return link_to($label, $uri);
}

function header_list_tag($field_name, $label = NULL, $default_order = '')
{
    $params = array();
    $order = $page = '';
    
    $param_page = sfContext::getInstance()->getRequest()->getParameter('page');
    $param_order = sfContext::getInstance()->getRequest()->getParameter('order');
    $param_orderby = sfContext::getInstance()->getRequest()->getParameter('orderby');
    
    $params['orderby'] = $field_name;
    
    if (empty($default_order))
    {
        $default_order = sfConfig::get('app_list_default_order', 'asc');
    }
    
    if (isset($param_order))
    {
        if ($param_orderby == $field_name)
        {
            $params['order'] = ($param_order == 'asc') ? 'desc' : 'asc';
            $class = ($param_order == 'asc') ? 'order_desc' : 'order_asc';
        }
        else
        {
            $params['order'] = $default_order;
            $class = '';
        }
    }
    else
    {
        $params['order'] = $default_order;
        $class = '';
    }
    
    if (isset($param_page))
    {
        $params['page'] = $param_page;
    }
    
    $uri = _addUrlParameters(_getBaseUri(), array('order', 'page', 'orderby'));
    $uri = _addParameters($uri, $params);

    if (!empty($label))
    {
        $label = __($label);
    }
    else
    {
        $label = __($field_name);
    }
    $label = str_replace(array('&nbsp;:', ' :', ':'), '', $label);
    if ($class)
    {
        $class = ' class="' . $class . '"';
    }
    
    return "<th$class>" . link_to($label, $uri, array('rel' => 'nofollow')) . '</th>';
}



function simple_header_list_tag($field_name)
{
    return '<th>' . ucfirst(__($field_name)) . '</th>';
}

function picto_header_list_tag($picto, $title)
{
    sfLoader::loadHelpers(array('General'));

    return '<th>' . picto_tag($picto, __($title)) . '</th>'; 
}

function images_header_list_tag()
{
    return picto_header_list_tag('picto_images', 'nb_images');
}

function comments_header_list_tag()
{
    return picto_header_list_tag('action_comment', 'nb_comments');
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

function get_paginated_activities($value, $hide_picto = false, $hide_text = true)
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
        if (array_key_exists($item, $activities) && !empty($activities[$item]))
        {
            $activity = $activities[$item];
            $activity_num = $item;
            $name = __($activity);
            if ($hide_picto)
            {
                $out[] = $name;
            }
            else
            {
                $activity_text = ($hide_text) ? '' : $activity;
                $out[] = '<span class="activity_'. $activity_num. ' picto" title="' . $name . '">'.$activity_text.'</span>';
            }
        }
    }

    if ($hide_picto)
    {
        return implode(', ', $out);
    }
    
    return implode(' ', $out);
}

function displayWithSuffix($data, $suffix)
{
    $data_as_string = strval($data);
    if (empty($data_as_string))
    {
        return '';
    }

    return $data_as_string . __($suffix);
}

function get_paginated_areas($geoassociations)
{
    $areas = array();
    foreach ($geoassociations as $geo_id => $geoP)
    {
        $areas[] = $geoP['AreaI18n'][0]['name'];
    }
    return implode(', ', $areas);
}
