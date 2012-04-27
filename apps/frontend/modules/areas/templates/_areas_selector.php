<?php
use_helper('General');
$mobile_version = c2cTools::mobileVersion();
?>
<fieldset id="fs_area">
    <legend class="select_title">
        <?php
        if (!isset($show_picto))
        {
            $show_picto = true;
        }
        if ($show_picto)
        {
            echo picto_tag('picto_areas') . ' - ';
        }
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
                        'complete' => 'Element.hide("indicator")'));
        if (!$mobile_version)
        {
            echo ' -&nbsp;' .
            picto_tag('picto_close', __('Reduce the list'),
                      array('onclick' => "changeSelectSize('places', false)")) .
            picto_tag('picto_open', __('Enlarge the list'),
                      array('onclick' => "changeSelectSize('places', true)"));
        }
        ?>
    </legend>
    <div id="area_selector">
        <?php
        //rq FIXME bug in symfony 1.0.11 which does not work with optgroups and escaping, see http://trac.symfony-project.org/ticket/3923
        // plus using sf_data->getRaw is not enough because it is passed through different partials
        $ranges_raw = array();
        foreach ($ranges as $key => $value)
        {
            if ($value instanceof sfOutputEscaperArrayDecorator)
            {
                $rr = array();
                foreach ($value as $k => $v)
                {
                    $rr[$k] = $v;
                }
                $ranges_raw[$key] = $rr;
            }
            else
            {
                $ranges_raw[$key] = $value;
            }
        }
        $selected_areas_raw = $sf_data->getRaw('selected_areas');
        if (isset($use_personalization) && $use_personalization && !count($selected_areas_raw))
        {
            $perso = c2cPersonalization::getInstance();
            if ($perso->isMainFilterSwitchOn())
            {
                $selected_areas_raw = $perso->getPlacesFilter();
            }
        }
        if (!$mobile_version)
        {
            $width = 'auto';
            if (!isset($height))
            {
                $height = '350px';
            }
        }
        else
        {
            $width = '216px';
            $height = '3.8em';
        }
        echo select_tag('areas', 
                        options_for_select($ranges_raw, $selected_areas_raw),
                        array('id' => 'places',
                              'multiple' => true,
                              'style' => 'width:' . $width . '; height:' . $height . ';'));
        ?>
    </div>
    <?php
    if (!$mobile_version)
    {
        echo '<p class="tips">' .  __('unselect dropdown tip') . '</p>';
    }
    ?>
</fieldset>
