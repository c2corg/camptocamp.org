<?php
use_helper('General', 'SmartDate', 'Pagination', 'Link');

if(isset($nb_outings))
{
    if (isset($items))
    {
        $nb_items = count($items);
        if ($nb_items > $nb_outings)
        {
            $nb_outings = "> $nb_outings";
        }
        else
        {
            $nb_outings = $nb_items;
        }
    }
    $nb_outings = " ($nb_outings)";
}
else
{
    $nb_outings = '';
}

if (isset($items))
{
    echo '<table class="children_docs"><tbody>';
    $date = 0;
    foreach ($items as $item)
    {
        echo '<tr><td>';
        $timedate = $item['date'];
        if ($timedate != $date)
        {
            echo '<time datetime="' . $timedate . '">' . format_date($timedate, 'D') . '</time>';
            $date = $timedate;
        }
        echo '</td><td>';
        echo get_paginated_activities($item['activities']);
        echo '</td><td>';
        echo link_to($i18n['name'], "@document_by_id_lang_slug?module=outings&id=$id&lang=$lang&slug=" . make_slug($i18n['name']),
                     ($lang != $culture) ? array('hreflang' => $lang) : null);
        if (isset($item['nb_images']))
        {
            $images = picto_tag('picto_images_light',
                                format_number_choice('[1]1 image|(1,+Inf]%1% images',
                                                     array('%1%' => $item['nb_images']),
                                                     $item['nb_images']));
            echo ' ' . $images;
        }
        echo '</td></tr>';
    }
    echo '</body></table>';
}

echo '<p class="list_link">' .
     picto_tag('action_list') . ' ' .
     link_to(__('List all linked outings') . $nb_outings, "outings/list?$module=$id&orderby=date&order=desc", array('rel' => 'nofollow')) .
     ' - ' .
     link_to(__('cond short'), "outings/conditions?$module=$id&orderby=date&order=desc") .
     ' - ' .
     link_to(picto_tag('picto_rss'), "outings/rss?$module=$id&orderby=date&order=desc") .
     '</p>';
