<?php
use_helper('Field'); 

$id = $document->get('id');

echo field_text_data($document, 'description', 'road access', array('needs_translation' => $needs_translation, 'images' => $images, 'class' => 'hfirst'));
if ($document->get('geom_wkt'))
{
    echo field_getdirections($id);
}

$has_pt_access = ($document->get('public_transportation_rating') != 3);
if ($has_pt_access)
{
    echo field_text_data($document, 'public_transportation_description', null, array('needs_translation' => $needs_translation, 'images' => $images));

    if ($sf_params->get('action') != 'preview')
    {
        $link_text = __('outings using PT from this access point');
        $url = "outings/list?parkings=$ids&owtp=yes&orderby=date&order=desc";
        echo '<p class="big_tips">' . link_to($link_text, $url, array('rel' => 'nofollow')) . "</p>\n";
    }
}
else
{
    echo field_text_data_if_set($document, 'public_transportation_description', null, array('needs_translation' => $needs_translation, 'images' => $images));
}

echo field_text_data_if_set($document, 'snow_clearance_comment', null, array('needs_translation' => $needs_translation, 'images' => $images));

echo field_text_data_if_set($document, 'accommodation', null, array('needs_translation' => $needs_translation, 'images' => $images));
