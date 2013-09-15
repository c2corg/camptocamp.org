<?php
use_helper('AutoComplete', 'Field', 'General');

if (count($associated_routes) == 0)
{
    echo "\n" . '<p class="default_text">' . __('No linked route') . '</p>';
}
else
{
    if (!isset($is_popup))
    {
        $is_popup = false;
    }
    if (!isset($show_list_link))
    {
        $show_list_link = true;
    }
    $mobile_version =  c2cTools::mobileVersion();
    if (!isset($list_format))
    {
        $list_format = $is_popup;
    }
    $list_format = $list_format || $mobile_version;
    $show_link_to_delete = ($sf_user->hasCredential('moderator') && !empty($type) && !$is_popup && !$mobile_version);
    
    $doc_id = $document->get('id');
    if (isset($use_doc_activities) && $use_doc_activities)
    {
        $doc_activities = $document->getRaw('activities');
        $has_doc_activities = count($doc_activities);
    }
    else
    {
        $has_doc_activities = false;
    }
    $strict = (int)$strict; // cast so that false is 0 and true is 1.
    
    $activity_list = sfConfig::get('app_activities_list');
    unset($activity_list[0]);
    $routes_per_activity = array();
    foreach ($activity_list as $activity_index => $activity)
    {
        $routes_per_activity[$activity_index] = array();
    }
    $routes_activities = array();
    foreach ($associated_routes as $key => $route)
    {
        $activities = (isset($route['activities']) ?
                Document::convertStringToArray($route['activities']) : $route->getRaw('activities'));
        if ($has_doc_activities)
        {
            $inter_activities = array_intersect($activities, $doc_activities);
            if (!empty($inter_activities))
            {
                $activities = $inter_activities;
            }
        }
        $routes_activities[$key] = $activities;
        foreach ($activities as $activity_index)
        {
            $routes_per_activity[$activity_index][] = $key;
        }
    }
    
    $activity_summary = array();
    if ((count($associated_routes) > 5))
    {
        foreach ($activity_list as $activity_index => $activity)
        {
            $nb_routes = count($routes_per_activity[$activity_index]);
            if ($nb_routes)
            {
                if ($is_popup)
                {
                    $activity_summary[] = true;
                }
                else
                {
                    $activity_summary[] = '<a href="#' . $activity . '_routes" onclick="C2C.linkRoutes(\'act' . $activity_index . '\'); return false;" title="' . __($activity) . '">' . picto_tag('activity_' . $activity_index) . '&nbsp;(' . $nb_routes . ')</a>';
                }
            }
        }
    }
    
    if (isset($id) && !empty($id) && !$is_popup && $show_list_link)
    {
        $routes_list_link = link_to('<span class="list_link">' . __('List all linked routes') . '</span>', "routes/list?$module=$id", array('rel' => 'nofollow'));
    }
    else
    {
        $routes_list_link = '';
    }
    
    if ((count($associated_routes) > 5) && (count($activity_summary) > 1))
    {
        if (!$is_popup)
        {
            echo "\n" . '<div id="routes_summary" class="no_print">'
               . implode($activity_summary)
               . picto_tag('picto_close', __('Close all sections'),
                           array('class' => 'click', 'id' => 'close_routes'))
               . picto_tag('picto_open', __('Open all sections'),
                           array('class' => 'click', 'id' => 'open_routes'))
               . $routes_list_link
               . '</div>';
        }
        $activity_section = true;
    }
    else
    {
        $activity_section = false;
    }
    
    foreach ($activity_list as $activity_index => $activity)
    {
        if ($activity_section)
        {
            $routes = $routes_per_activity[$activity_index];
            if (empty($routes))
            {
                continue;
            }
            
            echo "\n" . '<div id="' . $activity . '_routes" class="title2 htext act' . $activity_index . '">'
               . '<span class="picto picto_close_light" id="act' . $activity_index . '"></span>'
               . '<span class="picto activity_' . $activity_index . '"></span>'
               . __($activity) . ' (' . count($routes) . ')'
               . "\n</div>";
        }
        else
        {
            $routes = array_keys($routes_activities);
        }
        
        $class = 'children_docs child_routes act' . $activity_index;
        if ($list_format)
        {
            echo "\n" . '<ul class="' . $class . '">';
            $line_tag = 'li';
        }
        else
        {
            echo "\n" . '<table class="' . $class . '"><tbody>';
            $line_tag = 'tr';
        }
        
        foreach ($routes as $key)
        {
            $route = $associated_routes[$key];
            $activities = $routes_activities[$key];
            if (in_array($activity_index, $activities) || !$activity_section)
            {
                if ($activity_section)
                {
                    $avalaible_activities = array($activity_index);
                }
                else
                {
                    $avalaible_activities = null;
                }
                $georef = '';
                $route_id = $route->get('id');
                $idstring = $type . '_' . $route_id;
                
                echo '<' . $line_tag . ' class="' . $idstring . '">';
                
                if (!$route->getRaw('geom_wkt') instanceof Doctrine_Null)
                {
                    if ($list_format)
                    {
                        $georef = ' - ';
                    }
                    $georef .= picto_tag('action_gps', __('has GPS track'));
                }
                
                $route_link = '@document_by_id_lang_slug?module=routes&id=' . $route_id . 
                              '&lang=' . $route->get('culture') .
                              '&slug=' . make_slug($route->get('full_name'));
                $options = array();
                if (!empty($external_links))
                {
                    $options['target'] = '_blank';
                }
                
                if ($list_format)
                {
                    echo link_to($route->get('name'), $route_link, $options)
                       . '<div class="short_data">'
                       . summarize_route($route, true, false, $avalaible_activities, true)
                       . $georef;

                    if ($show_link_to_delete)
                    {
                        echo c2c_link_to_delete_element($type, $doc_id, $route_id, true, $strict);
                    }
                    echo '</div>';
                }
                else
                {
                    echo '<td>'
                       . link_to($route->get('name'), $route_link, $options)
                       . '</td>'
                       . summarize_route($route, true, true, $avalaible_activities, false)
                       . '<td>'
                       . $georef
                       . '</td>';

                    if ($show_link_to_delete)
                    {
                        echo '<td>'
                           . c2c_link_to_delete_element($type, $doc_id, $route_id, true, $strict)
                           . '</td>';
                    }
                }
                
                echo '</' . $line_tag . '>';
            }
        }
        
        if ($list_format)
        {
            echo '</ul>';
        }
        else
        {
            echo '</tbody></table>';
        }
        
        if (!$activity_section)
        {
            if (!empty($routes_list_link))
            {
                echo '<p class="list_link">'
                   . picto_tag('picto_routes') . ' '
                   . $routes_list_link
                   . '</p>';
            }
            break;
        }
    }
    
    if ($activity_section && !$is_popup)
    {
        echo javascript_tag('C2C.initRoutes();');
    }
}
