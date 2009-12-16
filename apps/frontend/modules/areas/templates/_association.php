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
$has_avalanche_bulletin = (isset($avalanche_bulletin) && count($avalanche_bulletin));
if ($has_weather || $has_avalanche_bulletin)
{
    $weather_title_list = array();
    $weather_link_list = array();
    $avalanche_list = array();
    foreach ($associated_docs as $doc)
    {
        $doc_id = $doc['id'];
        $doc_name = ucfirst($doc['name']);
        
        if ($has_weather)
        {
            list($title, $link) = weather_link($doc_id, $doc_name);
            if (!empty($link))
            {
                $weather_title_list[] = $title;
                $weather_link_list[] = $link;
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
    
    if (!empty($weather_link_list) || !empty($avalanche_list))
    {
?>
<div class="one_kind_association">
<div class="association_content">
<?php
        if (!empty($weather_link_list))
        {
            echo '<div class="section_subtitle assoc_img picto_weather" id="_weather_forecast" title="' . __('Weather forecast') . '"><span>' . __('Weather forecast') . __('&nbsp;:') . '</span></div>';
            if (count($weather_link_list) > 1)
            {
                foreach($weather_link_list as $key => $link)
                {
                    $weather_link_list[$key] = $weather_title_list[$key] . $link;
                }
            }
            echo '<div class="linked_elt">' . implode(', ', $weather_link_list) . '</div>';
        }
        if (!empty($avalanche_list))
        {
            echo '<div class="section_subtitle assoc_img picto_snow" id="_avalanche_bulletin" title="' . __('Avalanche bulletin') . '"><span>' . __('Avalanche bulletin') . __('&nbsp;:') . '</span></div>';
            echo '<div class="linked_elt">' . implode(', ', $avalanche_list) . '</div>';
        }
?>
</div>
</div>
<?php
    }
}

endif ?>
