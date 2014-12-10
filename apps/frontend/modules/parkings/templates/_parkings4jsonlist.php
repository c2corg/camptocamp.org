<?php
if (!isset($use_keys))
{
    $use_keys = false;
}

foreach ($parkings as $parking)
{
    $i18n = $parking['ParkingI18n'][0];
    $url = $use_keys ? array()
                     : array('url' => absolute_link(url_for('@document_by_id_lang_slug?module=parkings&id='.$parking['id'].'&lang='
                                      . $i18n['culture'] . '&slug=' . make_slug($i18n['name'])))
                            );
    $a[] = array_merge(array(
        'id' => $parking['id'],
        'name' => $i18n->getRaw('name')
        ), $url);
}
if (isset($a))
{
    krsort($a);
    echo json_encode(array_values($a));
}
else
{
    echo '[]';
}
