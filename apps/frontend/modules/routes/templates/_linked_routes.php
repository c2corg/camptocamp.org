<?php
use_helper('AutoComplete', 'Field');
if (count($associated_routes) == 0): ?>
    <p><?php echo __('No linked route') ?></p>
<?php
else : 
    $doc_id = $document->get('id');
    $strict = (int)$strict; // cast so that false is 0 and true is 1.
    $static_base_url = sfConfig::get('app_static_url');
?>
    <ul class="children_docs">
    <?php foreach ($associated_routes as $route):
              $georef = '';
              $route_id = $route->get('id');
              $idstring = $type . '_' . $route_id;
    ?>
            <li class="child_summit<?php echo get_activity_classes($route) ?>" id="<?php echo $idstring ?>">
            <?php
            if (!$route->getRaw('geom_wkt') instanceof Doctrine_Null)
            {
                $georef = ' - ' . image_tag($static_base_url . '/static/images/picto/gps.png',
                                            array('alt' => 'GPS',
                                                  'title' => __('has GPS track')));
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
    <?php endforeach; ?>
    </ul>
<?php
    if (!isset($do_not_filter_routes)):
    // TODO put this in a separate .js file?
    echo javascript_tag(
'var activities_to_show = $w($(\'quick_switch\').className);
 if (activities_to_show.length != 0) {
   var routes = $$(\'#routes_section_container .child_summit\');
   var sorted_routes = routes.partition(function(r) {
     var filtered = true;
     activities_to_show.each(function(a) {
       if ($w(r.className).include(a)) {
         filtered = false;
       }
     });
     return filtered;
   });
   sorted_routes[0].invoke(\'hide\');
   var div = $$(\'#routes_section_container .children_docs\').reduce();
   if (sorted_routes[1].length == 0) {
     new Insertion.Bottom(div, \'<p id="filter_no_route">'.addslashes(__('No linked route')).'</p>\');
   }
   if (sorted_routes[0].length != 0) {
     new Insertion.Bottom(div, \''.addslashes(link_to_function(__('Show filtered routes'),
     "if ($('filter_no_route') != null) {\$('filter_no_route').hide();};$(this).hide();sorted_routes[0].each(Effect.Appear);")).'\');
   }
 }');
    endif;
endif;
