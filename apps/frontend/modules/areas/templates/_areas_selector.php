<fieldset>
    <legend>
        <?php  echo link_to_remote(__('ranges'), 
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
                                'complete' => 'Element.hide("indicator")'));
                    ?>
    </legend>
    <div id="area_selector">
        <?php
        echo select_tag('areas', 
                        options_for_select($ranges), 
                        array('id' => 'places', 
                              'multiple' => true,
                              'style' => 'width:300px; height:100px;'));
        ?>
    </div>
    <?php echo __('unselect dropdown tip') ?>
</fieldset>
