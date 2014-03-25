<?php
foreach ($parkings as $parking)
{
    $i18n = $parking['ParkingI18n'][0];
    $a[] = array(
        'name' => $i18n->getRaw('name'),
        'url' => absolute_link(url_for('@document_by_id_lang_slug?module=parkings&id='.$parking['id'].'&lang='
                     .$i18n['culture'].'&slug='.make_slug($i18n['name'])))
    );
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
