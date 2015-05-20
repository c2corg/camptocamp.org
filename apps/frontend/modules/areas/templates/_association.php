<?php
use_helper('General', 'Field', 'Date');

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

$has_box = (isset($box) && $box);
$has_weather = (isset($weather) && $weather);
$has_avalanche_bulletin = (isset($avalanche_bulletin) && count($avalanche_bulletin));
$has_date = (isset($date) && $date);
if ($has_date)
{
    $date_1 = strtotime($date);
    $date_0 = $date_1 - 86400;
    $archive_month = date('n', $date_1);
    if ($archive_month > 5 && $archive_month < 10)
    {
        $has_date = false;
    }
}
else
{
    $date_1 = null;
}
if ($has_weather || $has_avalanche_bulletin)
{
    $has_coord = isset($lat) && $lat && isset($lon) && $lon && isset($elevation) && $elevation;
    $weather_title_list = array();
    $weather_link_list = array();
    $avalanche_title_list = array();
    $avalanche_link_list = array();
    $avalanche_link_list_2 = array();
    $avalanche_archive_title_list = array();
    $avalanche_archive_0_link_list = array();
    $avalanche_archive_1_link_list = array();
    
    if ($has_weather && $has_coord)
    {
        $lang = $sf_user->getCulture();
        list($title, $link) = weather_coord_link($lat, $lon, $elevation, $lang);
        if (!empty($link))
        {
            $weather_title_list[] = $title;
            $weather_link_list[] = $link;
        }
    }
    
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
            if ($has_date)
            {
                $link = avalanche_link($doc_id, $doc_name, $date_0);
                if (!empty($link))
                {
                    $avalanche_archive_0_link_list[] = $link;
                    
                    $link = avalanche_link($doc_id, $doc_name, $date_1);
                    $avalanche_archive_1_link_list[] = $link;
                }
            }
            
            $current_month = date('n', time());
            if ($current_month <= 5 || $current_month >= 10)
            {
                $link = avalanche_link($doc_id, $doc_name);
                if (!empty($link))
                {
                    $avalanche_link_list[] = $link;
                }
            }
        }
    }
    
    $has_weather_link = !empty($weather_link_list);
    $has_avalanche_archive_link = count($avalanche_archive_0_link_list);
    $has_avalanche_last_link = count($avalanche_link_list);
    $has_avalanche_link = ($has_avalanche_last_link || $has_avalanche_archive_link);
    
    if ($has_weather_link || $has_avalanche_link)
    {
?>
<div class="one_kind_association no_print">
<div class="association_content">
<?php
        if ($has_box)
        {
            $label = array();
            if ($has_weather_link)
            {
                $label[] = __('Weather forecast');
            }
            if ($has_avalanche_link)
            {
                $label[] = __('Avalanche bulletin');
            }
            $label = '<span class="assoc_img picto_open_light" id="toggle_weather"></span>'
                   . '<span class="linked_elt">' . implode(', ', $label) . '</span>';

            echo '<div class="box_title" id="weather_box_title" title="' . __('section open') . '">'
           . link_to_function($label, "C2C.toggleBox('weather')") 
           . '</div>'
           . '<div id="weather_box" style="display:none;">';
        }
        
        if ($has_weather_link)
        {
            echo '<div class="section_subtitle assoc_img picto_weather" id="_weather_forecast" title="' . __('Weather forecast') . '"><span>' . __('Weather forecast') . __('&nbsp;:') . '</span></div>';
            if (count($weather_link_list) > 1)
            {
                foreach($weather_link_list as $key => $link)
                {
                    $weather_link_list[$key] = $weather_title_list[$key] . $link;
                }
            }
            if ($has_box)
            {
                foreach ($weather_link_list as $link)
                {
                    echo '<div class="linked_elt">' . $link . '</div>';
                }
            }
            else
            {
                echo '<div class="linked_elt">' . implode(', ', $weather_link_list) . '</div>';
            }
        }
        if ($has_avalanche_link)
        {
            echo '<div class="section_subtitle assoc_img picto_snow" id="_avalanche_bulletin" title="' . __('Avalanche bulletin') . '"><span>' . __('Avalanche bulletin') . __('&nbsp;:') . '</span></div>';
            if ($has_avalanche_archive_link)
            {
                $avalanche_title_list[] = '<span class="title_inline">' . format_date($date_0, 'D') . __('&nbsp;:') . '</span> ';
                $avalanche_title_list[] = '<span class="title_inline">' . format_date($date_1, 'D') . __('&nbsp;:') . '</span> ';
                $avalanche_link_list_2[] = implode(' ', $avalanche_archive_0_link_list);
                $avalanche_link_list_2[] = implode(' ', $avalanche_archive_1_link_list);
            }
            if ($has_avalanche_archive_link || $has_box)
            {
                if ($has_avalanche_last_link)
                {
                    $avalanche_title_list[] = '<span class="title_inline">' . __('Last bulletin') . __('&nbsp;:') . '</span> ';
                    $avalanche_link_list_2[] = implode(' ', $avalanche_link_list);
                }
                
                foreach($avalanche_link_list_2 as $key => $link)
                {
                    $avalanche_link_list_2[$key] = $avalanche_title_list[$key] . $link;
                }
                $avalanche_link_list = $avalanche_link_list_2;
            }
            
            if ($has_box)
            {
                foreach ($avalanche_link_list as $link)
                {
                    echo '<div class="linked_elt">' . $link . '</div>';
                }
            }
            else
            {
                echo '<div class="linked_elt">' . implode(', ', $avalanche_link_list) . '</div>';
            }
        }
        
        if ($has_box)
        {
            echo '</div>';
        }
?>
</div>
</div>
<?php
    }
}

endif ?>
