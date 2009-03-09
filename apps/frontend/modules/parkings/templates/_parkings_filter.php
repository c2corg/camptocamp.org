<br />
<?php
echo __('access_status') . ' ' . input_tag('pnam') . ' ';
echo __('elevation') . ' ' . elevation_selector('palt') . ' ';
$tp_selector = array();
foreach (sfConfig::get('mod_parkings_public_transportation_ratings_list') as $tp_id => $tp)
{
    if ($tp_id == 0) continue;
    $tp_selector[] = checkbox_tag('tp[]', $tp_id, false)
                     . ' ' .
                     label_for('tp_' . $tp_id, __($tp));
}
$tp_selector = implode(' &nbsp; ', $tp_selector);
echo __('public_transportation_rating short') . ' ' . $tp_selector;