<?php
use_helper('General');

if (!isset($show_tpty))
{
    $show_tpty = true;
}
?>
<br />
<?php
echo '<div class="fieldname">' . picto_tag('picto_parkings') . __('Access point:') . ' </div>' .
     (isset($autofocus) ? input_tag('pnam', null, array('autofocus' => 'autofocus')) : input_tag('pnam'));
echo __('elevation') . ' ' . elevation_selector('palt');
?>
<br />
<?php
echo __('public_transportation_rating short') . ' ' . field_value_selector('tp', 'app_parkings_public_transportation_ratings',
    array('keepfirst' => false, 'multiple' => true));

if ($show_tpty)
{
    echo __('public_transportation_types short') . ' ' . field_value_selector('tpty', 'app_parkings_public_transportation_types',
        array('keepfirst' => false, 'multiple' => true, 'exclude' => 9));
}
echo bool_selector_from_list('tpty', 'app_parkings_public_transportation_types', 9); // cable car
