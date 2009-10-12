<?php
use_helper('AutoComplete', 'Field', 'General');

if (count($associated_routes) == 0)
{
    echo "\n" . '<p class="default_text">' . __('No linked route') . '</p>';
}
else
{ 
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
                $activity_summary[] = '<a href="#' . $activity . '_routes" onclick="linkRoutes(\'act' . $activity_index . '\'); return false;" title="' . __($activity) . '">' . picto_tag('activity_' . $activity_index) . '&nbsp;(' . $nb_routes . ')</a>';
            }
        }
    }
    
    if (isset($id))
    {
        $routes_list_link = link_to(__('List all linked routes'), "routes/list?$module=$id");
    }
    else
    {
        $routes_list_link = '';
    }
    
    if ((count($associated_routes) > 5) && (count($activity_summary) > 1))
    {
        echo "\n" . '<div id="routes_summary" class="no_print">'
           . implode($activity_summary)
           . picto_tag('picto_close', __('Close all sections'),
                       array('class' => 'click', 'id' => 'close_routes'))
           . picto_tag('picto_open', __('Open all sections'),
                       array('class' => 'click', 'id' => 'open_routes'))
           . $routes_list_link
           . '</div>';
        $actvity_section = true;
    }
    else
    {
        $actvity_section = false;
    }
    
    foreach ($activity_list as $activity_index => $activity)
    {
        if ($actvity_section)
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
        
        echo "\n" . '<ul class="children_docs child_routes act' . $activity_index . '">';
        
        foreach ($routes as $key)
        {
            $route = $associated_routes[$key];
            $activities = $routes_activities[$key];
            if (in_array($activity_index, $activities) || !$actvity_section)
            {
                $georef = '';
                $route_id = $route->get('id');
                $idstring = $type . '_' . $route_id;
                
                echo "\n\t" . '<li id="' . $idstring . '">';
                
                if (!$route->getRaw('geom_wkt') instanceof Doctrine_Null)
                {
                    $georef = ' - ' . picto_tag('action_gps', __('has GPS track'));
                }
                
                echo "\n\t\t" . link_to($route->get('name'),
                             '@document_by_id_lang_slug?module=routes&id=' . $route_id . '&lang=' . $route->get('culture') . '&slug=' . get_slug($route));
                echo '<div class="short_data">';
                echo summarize_route($route) . $georef;

                if ($sf_user->hasCredential('moderator') && $sf_context->getActionName() != 'popup')
                {
                    $idstring = $type . '_' . $route_id;
                    echo c2c_link_to_delete_element($type, $doc_id, $route_id, true, $strict);
                }
                echo '</div>';
                
                echo "\n\t</li>";
            }
        }
        
        echo "\n</ul>";
        
        if (!$actvity_section)
        {
            echo '<p class="list_link">'
               . picto_tag('picto_routes') . ' '
               . $routes_list_link
               . '</p>';
            break;
        }
    }
    echo javascript_tag('initRoutes();');
}
