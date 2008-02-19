<?php
if (count($associated_routes) == 0): ?>
    <p><?php echo __('No linked route') ?></p>
<?php else : ?>
    <ul class="children_docs">
    <?php foreach ($associated_routes as $route): ?>
            <li class="child_summit">
            <?php 
            $georef = '';
            if (!$route->getRaw('geom_wkt') instanceof Doctrine_Null)
            {
                $georef = ' - ' . image_tag('/static/images/picto/gps.png',
                                            array('alt' => 'GPS',
                                                  'title' => __('has GPS track')));
            }
            echo link_to($route->get('name'), '@document_by_id?module=routes&id=' . $route->get('id')) .
                        ' - ' . field_activities_data($route, true)
                        . ' - ' . $route['height_diff_up'] . ' ' . __('meters')
                        . ' - ' . field_data_from_list_if_set($route, 'facing', 'app_routes_facings', false, true) 
                        . ' - ' . field_route_ratings_data($route)
                        . $georef;
            ?>
            </li>
    <?php endforeach; ?>
    </ul>
<?php
endif;
