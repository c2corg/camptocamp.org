<?php
use_helper('General');
?>
<fieldset>
    <legend class="select_title">
        <?php
        $static_base_url = sfConfig::get('app_static_url');
        echo picto_tag('picto_areas') . ' - ' .
        link_to_remote(__('ranges'), 
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
                    'complete' => 'Element.hide("indicator")')) . ' -&nbsp;' .
        picto_tag('picto_close', __('Reduce the list'),
                  array('onclick' => "changeSelectSize('places', false)")) .
        picto_tag('picto_open', __('Enlarge the list'),
                  array('onclick' => "changeSelectSize('places', true)"));
        ?>
    </legend>
    <div id="area_selector">
        <?php
        //rq FIXME bug in symfony 1.0.11 which does not work with optgroups and escaping, see http://trac.symfony-project.org/ticket/3923
        // plus using sf_data->getRaw is not enough because it is passed through different partials
        $raw_ranges = array();
        foreach ($ranges as $key => $value)
        {
            if ($value instanceof sfOutputEscaperArrayDecorator)
            {
                $rr = array();
                foreach ($value as $k => $v)
                {
                    $rr[$k] = $v;
                }
                $raw_ranges[$key] = $rr;
            }
            else
            {
                $raw_ranges[$key] = $value;
            }
        }
        echo select_tag('areas', 
                        options_for_select($raw_ranges),
                        array('id' => 'places',
                              'multiple' => true,
                              'style' => 'width:400px; height:100px;'));
        ?>
    </div>
    <?php echo __('unselect dropdown tip') ?>
</fieldset>
