<?php
$html = array();
foreach ($parkings as $parking)
{
    $name = ucfirst($parking['ParkingI18n'][0]['name']);
    $url = '@document_by_id_lang_slug?module=parkings&id=' . $parking['id'] . '&lang=' . $parking['ParkingI18n'][0]['culture'] .
           '&slug=' . make_slug($parking['ParkingI18n'][0]['name']);
    $link = link_to($name, $url);
    if (isset($parking['lowest_elevation']) && is_scalar($parking['lowest_elevation']) && $parking['lowest_elevation'] != $parking['elevation'])
    {
        $link .= '&nbsp; ' . $parking['lowest_elevation'] . __('meters') . __('range separator') . $parking['elevation'] . __('meters');
    }
    else if (isset($parking['elevation']) && is_scalar($parking['elevation']))
    {
        $link .= '&nbsp; ' . $parking['elevation'] . __('meters');
    }
    if (isset($parking['public_transportation_types']))
    {
        $link .= field_pt_picto_if_set($parking, true, true, ' - ');
    }
    $html[] = $link;
}
echo implode('<br />', $html);
