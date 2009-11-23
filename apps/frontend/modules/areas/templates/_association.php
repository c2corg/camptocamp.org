<?php
use_helper('General', 'Field');

if (count($associated_docs)):

$has_areas = (!isset($areas) || (isset($areas) && $areas));
if ($has_areas)
{
 ?>
<div class="one_kind_association">
<div class="association_content">
<?php
    $area_type_list = array_keys(sfConfig::get('app_areas_area_types'));
    array_shift($area_type_list);
    echo '<div class="assoc_img picto_'.$module.'" title="'.ucfirst(__($module)).'"><span>'.ucfirst(__($module)).__('&nbsp;:').'</span></div>';
    foreach ($area_type_list as $area_type)
    {
        $element = array();
        foreach ($associated_docs as $doc)
        {
            if ($doc['area_type'] != $area_type)
            {
                continue;
            }
            $doc_id = $doc['id'];
            $name = ucfirst($doc['name']);
            $url = "@document_by_id_lang_slug?module=$module&id=$doc_id" . '&lang=' . $doc['culture'] . '&slug=' . make_slug($doc['name']);
            $element[] = link_to($name, $url);
        }
        if (!empty($element))
        {
            echo '<div class="linked_elt">' . implode(', ', $element) . '</div>';
        }
    }
?>
</div>
</div>
<?php
}

$has_weather = (isset($weather) && $weather);
$has_avalanche_bulletin = (isset($avalanche_bulletin) && !empty($avalanche_bulletin));
if ($has_weather || $has_avalanche_bulletin)
{
    $weather_list = array();
    $avalanche_list = array();
    foreach ($associated_docs as $doc)
    {
        $doc_id = $doc['id'];
        $doc_name = ucfirst($doc['name']);
        
        if ($has_weather)
        {
            $link = weather_link($doc_id, $doc_name);
            if (!empty($link))
            {
                $weather_list[] = $link;
            }
        }
        
        if ($has_avalanche_bulletin)
        {
            $link = avalanche_link($doc_id, $doc_name);
            if (!empty($link))
            {
                $avalanche_list[] = $link;
            }
        }
    }
    
    if (!empty($weather_list) || !empty($avalanche_list))
    {
?>
<div class="one_kind_association">
<div class="association_content">
<?php
        echo '<div class="assoc_img picto_'.$module.'" title="'.ucfirst(__('weather short')).'"><span>'.ucfirst(__('weather short')).__('&nbsp;:').'</span></div>';
        if (!empty($weather_list))
        {
            echo '<div class="linked_elt"><div class="section_subtitle" id="_weather_forecast">' . __('Weather forecast') . __('&nbsp;:') . '</div> ' . implode(', ', $weather_list) . '</div>';
        }
        if (!empty($avalanche_list))
        {
            echo '<div class="linked_elt"><div class="section_subtitle" id="_avalanche_bulletin">' . __('Avalanche bulletin') . __('&nbsp;:') . '</div> ' . implode(', ', $avalanche_list) . '</div>';
        }
?>
</div>
</div>
<?php
    }
}

endif ?>
