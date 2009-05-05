<fieldset>
    <legend>
        <?php
        $static_base_url = sfConfig::get('app_static_url');
        echo '<span class="picto picto_areas"> &nbsp; </span>';
        echo link_to_remote(__('ranges'), 
            array(  'update' => 'area_selector', 
                    'url' => '/areas/getmultipleselect?area_type=1', 
                    'loading' => 'Element.show("indicator")', 
                    'complete' => 'Element.hide("indicator")')) . ' - ' . 
        link_to_remote(__('regions'), 
            array(  'update' => 'area_selector', 
                    'url' => '/areas/getmultipleselect?area_type=3', 
                    'loading' => 'Element.show("indicator")', 
                    'complete' => 'Element.hide("indicator")')) . ' - ' . 
        link_to_remote(__('countries'), 
            array(  'update' => 'area_selector', 
                    'url' => '/areas/getmultipleselect?area_type=2', 
                    'loading' => 'Element.show("indicator")', 
                    'complete' => 'Element.hide("indicator")')) . ' - ';
        echo image_tag("$static_base_url/static/images/picto/close.png",
                       array('title' => __('Reduce the list'),
                             'onclick' => "changeSelectSize('area_selector', false)")
                      ) . ' ' .
        image_tag("$static_base_url/static/images/picto/open.png",
                       array('title' => __('Enlarge the list'),
                             'onclick' => "changeSelectSize('area_selector', true)")
                      );

        ?>
    </legend>
    <div id="area_selector">
        <?php
        echo select_tag('areas', 
                        options_for_select($ranges), 
                        array('id' => 'places', 
                              'multiple' => true,
                              'style' => 'width:400px; height:100px;'));
        ?>
    </div>
    <?php echo __('unselect dropdown tip') ?>
</fieldset>
