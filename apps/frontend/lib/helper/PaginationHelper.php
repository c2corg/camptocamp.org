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

function _addUrlParameters($uri, $params_to_ignore = array(), $params = array(), $rename_params = array(), $params_if_empty = array())
{
    $request = sfContext::getInstance()->getRequest();
    $request_parameters = $request->getParameterHolder()->getAll();
    
    $params_to_ignore = array_merge($params_to_ignore, array('module', 'action'));
    // remove action and module names and the escapted give by parameter
    foreach ($params_to_ignore as $param)
    {
        unset($request_parameters[$param]);
    }
    
    foreach ($rename_params as $old => $new)
    {
        if (isset($request_parameters[$old]))
        {
            $value = $request_parameters[$old];
            unset($request_parameters[$old]);
            $request_parameters[$new] = $value;
        }
        if (isset($params[$old]))
        {
            $value = $params[$old];
            unset($params[$old]);
            $params[$new] = $value;
        }
    }
    
    if (empty($request_parameters))
    {
        $request_parameters = $params_if_empty;
    }
    
    $request_parameters = array_merge($request_parameters, $params);
    
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

function unpackUrlParameters($params, &$out, $rename = array())
{
    $params = explode('/', $params);
    $names = $values = $criteria = array();
    $is_name = true;
    foreach ($params as $param)
    {
        if ($is_name)
        {
            if (isset($rename[$param]))
            {
                $param = $rename[$param];
            }
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
            $value = str_replace('+', ' ', $values[$key]);
            $criteria[$name] = $value;
            $out[] = $name . '=' . $value;
        }
        else
        {
            break;
        }
    }
    
    return $criteria;
}

function pager_navigation($pager, $class = array())
{
    sfLoader::loadHelpers(array('General'));

    $navigation = '';

    if ($pager->haveToPaginate())
    {
        $context = sfContext::getInstance();
        $request = $context->getRequest();
     
        $orderby_params = array('orderby', 'orderby2', 'orderby3');
        $order_params = array('order', 'order2', 'order3');
        $orderby_list = c2cTools::getRequestParameterArray($orderby_params);
        $order_list = c2cTools::getRequestParameterArray($order_params);
        
        $params = array();
        foreach($orderby_params as $key => $orderby_param)
        {
            $params[$orderby_param] = $orderby_list[$key];
            $params[$order_params[$key]] = $order_list[$key];
        }
     
        $uri = _addUrlParameters(_getBaseUri(), array(), $params);
     
        $uri .= _getSeparator($uri) . 'page=';
     
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
    
    $class[] = 'pages_navigation';
    
    return '<div class="' . implode(' ', $class) . '">' . $navigation . '</div>';
}

function pager_nb_results($pager)
{
    if ($pager->haveToPaginate())
    {
        $out = __('Results %1% - %2% of %3%', array('%1%' => ($pager->getMaxPerPage() * ($pager->getPage() - 1) + 1),
                                                    '%2%' => ($pager->getPage() != $pager->getLastPage()) ?
                                                                  $pager->getMaxPerPage() * $pager->getPage() :
                                                                  $pager->getNbResults(),
                                                    '%3%' => $pager->getNbResults()));
    }
    else
    {
        $out = __('%1% results', array('%1%' => $pager->getNbResults()));
    }
    
    return '<p>' . $out . '</p>';
}

/* simple pager that will show the current div and display the selected one instead */
function simple_pager_navigation($current_page, $nb_pages, $div_prefix)
{
    sfLoader::loadHelpers(array('General'));

    $navigation = '';

    if ($current_page != 0)
    {
        $navigation .= link_to_function(picto_tag('action_first', __('first page')),
                                        "$('#${div_prefix}$current_page, #${div_prefix}0').slideToggle(1000)");
        $navigation .= '&nbsp;';
        $navigation .= link_to_function(picto_tag('action_back', __('previous page')),
                                        "$('#${div_prefix}$current_page, #${div_prefix}".($current_page-1)."').slideToggle(1000)");
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
                   link_to_function($i+1, "$('#${div_prefix}$current_page, #${div_prefix}$i').slideToggle(1000)");
        $i++;
    }
    $navigation .= join('&nbsp;&nbsp;', $links);

    if ($current_page != $nb_pages-1)
    {
        $navigation .= '&nbsp;';
        $navigation .= link_to_function(picto_tag('action_next', __('next page')),
                                        "$('#${div_prefix}$current_page, #${div_prefix}".($current_page+1)."').slideToggle(1000)");
        $navigation .= '&nbsp;';
        $navigation .= link_to_function(picto_tag('action_last', __('last page')),
                                        "$('#${div_prefix}$current_page, #${div_prefix}".($nb_pages-1)."').slideToggle(1000)");
    }

    return '<div class="pages_navigation">' . $navigation . '</div>';
}

function link_to_default_order($label, $default_label)
{
    $param_orderby = sfContext::getInstance()->getRequest()->getParameter('orderby');
    
    if (isset($param_orderby))
    {
        $uri = _addUrlParameters(_getBaseUri(), array('orderby', 'orderby2', 'orderby3', 'order', 'order2', 'order3', 'page'));
        return link_to($label, $uri);
    }
    else
    {
        return $default_label;
    }
}

function link_to_conditions($label)
{
    $params = array('orderby' => 'date', 'order' => 'desc');
    $uri = _addUrlParameters('/outings/conditions', array('orderby', 'orderby2', 'orderby3', 'order', 'order2', 'order3', 'page'), $params);
    
    return link_to($label, $uri, array('rel' => 'nofollow'));
}

function link_to_outings($label)
{
    $uri = '/outings/list';
    $uri .= _addUrlParameters($uri, array('page'));
    
    return link_to($label, $uri);
}

function link_to_associated_images($label, $join = '', $orderby = array())
{
    $params = array();
    $rename_params['users'] = 'ousers';
    $perso = array();
    if (!empty($join))
    {
        $perso_param = c2cPersonalization::getDefaultFiltersUrlParam($join, array('ifon'));
        if (!empty($perso_param))
        {
            $perso['perso'] = $perso_param;
        }
        $rename_params['act'] = c2cTools::Module2Letter($join) . 'act';
        $rename_params['id'] = $join;
        $join = substr($join, 0, -1);
        $params['join'] = $join;
    }
    $params = array_merge($params, $orderby);
    $uri = '/images/list';
    $uri .= _addUrlParameters($uri,
                              array('orderby', 'orderby2', 'orderby3', 'order', 'order2', 'order3', 'page'),
                              $params,
                              $rename_params,
                              $perso);
    
    return link_to($label, $uri, array('rel' => 'nofollow'));
}

function header_list_tag($field_name, $label = null, $default_order = '', $simple = false)
{
    if (empty($label))
    {
        $label = $field_name;
    }
    
    if ($simple)
    {
        return simple_header_list_tag($label);
    }
    
    $request = sfContext::getInstance()->getRequest();
    $params = array();
    $order = $page = '';
    $base_default_order = sfConfig::get('app_list_default_order', 'asc');
    
    $param_page = $request->getParameter('page', '');
    $param_order = $request->getParameter('order', $base_default_order);
    $param_orderby = $request->getParameter('orderby', '');
    
    $params['orderby'] = $field_name;
    
    if (empty($default_order))
    {
        $default_order = $base_default_order;
    }
    
    if (!empty($param_orderby) && !empty($param_order))
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
    
    if (!empty($param_page))
    {
        $params['page'] = $param_page;
    }
    
    $uri = _addUrlParameters(_getBaseUri(), array('orderby', 'orderby2', 'orderby3', 'order', 'order2', 'order3', 'page'), $params);

    $label = __($label);
    $label = str_replace(array('&nbsp;:', ' :', ':'), '', $label);
    if ($class)
    {
        $class = ' class="' . $class . '"';
    }
    
    return "<th$class>" . link_to($label, $uri, array('rel' => 'nofollow')) . '</th>';
}

function region_header_list_tag($label = null, $default_order = '', $simple = false)
{
    $params_list = array_keys(c2cTools::getCriteriaRequestParameters());
    $is_default_list = empty($params_list);
    
    $orderby = sfContext::getInstance()->getRequest()->getParameter('orderby');
    if (in_array($orderby, array('range', 'admin', 'country', 'valley')))
    {
        $orderby_area = $orderby;
    }
    else
    {
        $orderby_area = 'range';
    }
    
    return header_list_tag($orderby_area, $label, $default_order, $is_default_list || $simple);
}

function simple_header_list_tag($field_name = '')
{
    $label = str_replace(array('&nbsp;:', ' :', ':'), '', __($field_name));
    return '<th>' . ucfirst($label) . '</th>';
}

function select_all_header_list_tag($title = '')
{
    if (!empty($title))
    {
        $title = ' title="' . $title . '"';
    }
    return "<th$title>" . '<input type="checkbox" id="select_all" /></th>';
}

function picto_header_list_tag($picto, $title = '')
{
    sfLoader::loadHelpers(array('General'));

    return '<th>' . picto_tag($picto, __($title)) . '</th>'; 
}

function images_header_list_tag()
{
    return picto_header_list_tag('picto_images', 'nb_linked_images');
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

function get_paginated_activities($value, $hide_picto = false, $picto_separator = ' ')
{
    if (empty($value) || !is_string($value))
    {
        return '';
    }
    
    // FIXME: perform this conversion when retrieving data
    $value = BaseDocument::convertStringToArray($value);

    $activities = sfConfig::get('app_activities_list');
    $out = array();
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
                $out[] = '<span class="activity_' . $activity_num . ' picto" title="' . $name . '"></span>';
            }
        }
    }

    if ($hide_picto)
    {
        return implode(', ', $out);
    }
    
    return implode($picto_separator, $out);
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
        if ($geoP['type'] == 'dr') // only areas, not maps
        {
            $areas[] = $geoP['AreaI18n'][0]['name'];
        }
    }
    return implode(', ', $areas);
}
