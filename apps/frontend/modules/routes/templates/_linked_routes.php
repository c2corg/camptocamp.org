<?php
use_helper('AutoComplete', 'Field', 'General');
if (count($associated_routes) == 0): ?>
    <p><?php echo __('No linked route') ?></p>
<?php
else : 
    $doc_id = $document->get('id');
    $strict = (int)$strict; // cast so that false is 0 and true is 1.
    
    $activity_list = sfConfig::get('app_activities_list');
    array_shift($activity_list);
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
        $routes_activities[$key] = $activities;
        foreach ($activities as $activity_index)
        {
            $routes_per_activity[$activity_index][] = $key;
        }
    }
    
    $activity_summary = array();
    foreach ($activity_list as $activity_index => $activity)
    {
        $nb_routes = count($routes_per_activity[$activity_index]);
        if ($nb_routes)
        {
            $activity_summary[] = '<a href="#' . $activity . '_routes" onclick="showRoutes(' . $activity_index . ', ' . $activity . '); return false;">' . picto_tag('activity_' . $activity_index) . '&nbsp;(' . $nb_routes . ')</a>';
        }
    }
    if ((count($activity_summary) > 1) && (count($associated_routes) > 5))
    {
        echo '<div id="routes_summary" class="title2 htext">' . implode($activity_summary) . '</div>';
        $actvity_section = true;
    }
    else
    {
        $actvity_section = false;
    }
    
    foreach ($activity_list as $activity_index => $activity):
        if ($actvity_section)
        {
            $routes = $routes_per_activity[$activity_index];
            if (empty($routes))
            {
                continue;
            }
?>
    <div id="<?php echo $activity ?>_routes" class="title2 htext">
    <a onclick="toggleRoutes(<?php echo $activity_index ?>); return false;" href="#"><span class="picto picto_close_light"></span><span class="picto activity_<?php echo $activity_index ?>"></span><?php echo __($activity) . ' (' . count($routes) . ')' ?></a>
    </div>
<?php
        }
        else
        {
            $routes = $associated_routes;
        }
?>
    <ul class="children_docs child_routes <?php echo $activity ?>" id="routes_<?php echo $activity_index ?>">
<?php
        foreach ($routes as $key):
            $route = $associated_routes[$key];
            $activities = $routes_activities[$key];
            if (in_array($activity_index, $activities)):
                $georef = '';
                $route_id = $route->get('id');
                $idstring = $type . '_' . $route_id;
                    
  ?>        <li class="child_summit" id="<?php echo $idstring ?>">
<?php
                if (!$route->getRaw('geom_wkt') instanceof Doctrine_Null)
                {
                    $georef = ' - ' . picto_tag('action_gps', __('has GPS track'));
                }
                
                echo link_to($route->get('name'),
                             '@document_by_id_lang_slug?module=routes&id=' . $route_id . '&lang=' . $route->get('culture') . '&slug=' . get_slug($route))
                     . summarize_route($route) . $georef;

                if ($sf_user->hasCredential('moderator') && $sf_context->getActionName() != 'popup')
                {
                    $idstring = $type . '_' . $route_id;
                    echo c2c_link_to_delete_element(
                                        "documents/addRemoveAssociation?main_".$type."_id=$doc_id&linked_id=$route_id&mode=remove&type=$type&strict=$strict",
                                        "del_$idstring",
                                        $idstring);
                }
                ?>
        </li>
    <?php
            endif;
        endforeach;
    ?>
    </ul>
<?php
        if (!$actvity_section)
        {
            break;
        }
    endforeach;
endif;
