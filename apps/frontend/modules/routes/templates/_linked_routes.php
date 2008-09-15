<?php
use_helper('AutoComplete');
if (count($associated_routes) == 0): ?>
    <p><?php echo __('No linked route') ?></p>
<?php
else : 
    $doc_id = $document->get('id');
    $strict = (int)$strict; // cast so that false is 0 and true is 1.
?>
    <ul class="children_docs">
    <?php foreach ($associated_routes as $route):
              $georef = '';
              $route_id = $route->get('id');
              $idstring = $type . '_' . $route_id;
    ?>
            <li class="child_summit" id="<?php echo $idstring ?>">
            <?php
            if (!$route->getRaw('geom_wkt') instanceof Doctrine_Null)
            {
                $georef = ' - ' . image_tag('/static/images/picto/gps.png',
                                            array('alt' => 'GPS',
                                                  'title' => __('has GPS track')));
            }
            echo link_to($route->get('name'), '@document_by_id?module=routes&id=' . $route_id) .
                        ' - ' . field_activities_data($route, true)
                        . ' - ' . $route['height_diff_up'] . ' ' . __('meters')
                        . ' - ' . field_data_from_list_if_set($route, 'facing', 'app_routes_facings', false, true) 
                        . ' - ' . field_route_ratings_data($route)
                        . $georef;
            if ($sf_user->hasCredential('moderator') && $sf_context->getActionName() != 'geoportail')
            {
                $idstring = $type . '_' . $route_id;
                echo c2c_link_to_delete_element(
                                    "documents/addRemoveAssociation?main_".$type."_id=$doc_id&linked_id=$route_id&mode=remove&type=$type&strict=$strict",
                                    "del_$idstring",
                                    $idstring);
            }
            ?>
            </li>
    <?php endforeach; ?>
    </ul>
<?php
endif;
