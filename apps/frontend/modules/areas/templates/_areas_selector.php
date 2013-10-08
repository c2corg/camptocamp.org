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
        echo link_to_function(__('ranges'), "jQuery('#indicator').show();
               jQuery.ajax('" . url_for('/areas/getmultipleselect?area_type=1') . "')
                 .always(function() { jQuery('#indicator').hide(); })
                 .done(function(data) { jQuery('#area_selector').html(data); ))") . ' - ' .
             link_to_function(__('regions'), "jQuery('#indicator').show();
               jQuery.ajax('" . url_for('/areas/getmultipleselect?area_type=3') . "')
                 .always(function() { jQuery('#indicator').hide(); })
                 .done(function(data) { jQuery('#area_selector').html(data); })") . ' - ' .
             link_to_function(__('countries'), "jQuery('#indicator').show();
               jQuery.ajax('" . url_for('/areas/getmultipleselect?area_type=2') . "')
                 .always(function() { jQuery('#indicator').hide(); })
                 .done(function(data) { jQuery('#area_selector').html(data); })");

        if (!$mobile_version)
        {
            echo ' -&nbsp;' .
            picto_tag('picto_close', __('Reduce the list'),
                      array('onclick' => "C2C.changeSelectSize('places', false)")) .
            picto_tag('picto_open', __('Enlarge the list'),
                      array('onclick' => "C2C.changeSelectSize('places', true)"));
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
        if (isset($selected_areas))
        {
            $selected_areas_raw = $sf_data->getRaw('selected_areas');
        }
        else
        {
            $selected_areas_raw = array();
        }
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
