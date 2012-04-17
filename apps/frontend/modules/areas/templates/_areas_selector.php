<?php
use_helper('General');
$mobile_version = c2cTools::mobileVersion();
?>
<fieldset id="fs_area">
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

        $selected_ranges = array();
        if (isset($use_personalization) && $use_personalization)
        {
            $perso = c2cPersonalization::getInstance();
            if ($perso->isMainFilterSwitchOn())
            {
                $selected_ranges = $perso->getPlacesFilter();
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
                        options_for_select($raw_ranges, $selected_ranges),
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
