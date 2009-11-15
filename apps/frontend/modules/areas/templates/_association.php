<?php
use_helper('General', 'Field');

if (count($associated_docs)): ?>
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
        $url = "@document_by_id_lang_slug?module=$module&id=$doc_id" . '&lang=' . $doc['culture'] . '&slug=' . formate_slug($doc['search_name']);
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

$has_weather = (isset($weather) && $weather);
$has_avalanche_bulletin = (isset($avalanche_bulletin) && $avalanche_bulletin);
if ($has_weather || $has_avalanche_bulletin)
{
    $weather_list = array();
    $avalanche_bulletin_list = array();
    if ($has_avalanche_bulletin)
    {
        $avalanche_bulletin_url = sfConfig::get('app_areas_avalanche_bulletins');
        $avalanche_bulletin_areas = array_keys($avalanche_bulletin_url);
        foreach ($associated_docs as $doc)
        {
            if (!in_array($doc['id'], $avalanche_bulletin_areas))
            {
                continue;
            }
            $doc_id = $doc['id'];
            $name = ucfirst($doc['name']);
            $url = 'http://' . $avalanche_bulletin_url[$doc_id];
            // Swiss bulletin
            if ($doc_id == 14067)
            {
                $lang = strtoupper($this->getUser()->getCulture());
                if (in_array($lang, array('CA', 'ES')))
                {
                    $lang = 'EN';
                }
                elseif ($lang == 'EU')
                {
                    $lang = 'FR';
                }
                $url .= $lang;
            }
            
            $avalanche_bulletin_list[] = link_to($name, $url);
        }
    }
    
    if (!empty($weather_list) || !empty($avalanche_bulletin_list))
    {
?>
<div class="one_kind_association">
<div class="association_content">
<?php
        echo '<div class="assoc_img picto_'.$module.'" title="'.ucfirst(__('wheather')).'"><span>'.ucfirst(__('wheather')).__('&nbsp;:').'</span></div>';
        if (!empty($avalanche_bulletin_list))
        {
            echo '<div class="linked_elt">' . __('Avalanche bulletins') . __(' :') . ' ' . implode(', ', $avalanche_bulletin_list) . '</div>';
        }
?>
</div>
</div>
<?php
    }
}

endif ?>
