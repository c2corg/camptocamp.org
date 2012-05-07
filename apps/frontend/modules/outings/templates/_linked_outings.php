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
    if (count($items))
    {
        echo '<table class="children_docs"><tbody>';
        
        $culture = $sf_user->getCulture();
        $date = 0;
        foreach ($items as $item)
        {
            echo '<tr><td>';
            
            $timedate = $item['date'];
            if (c2cTools::mobileVersion())
            {
                $text_date = $timedate;
            }
            else
            {
                $text_date = format_date($timedate, 'D');
            }
            if ($timedate != $date)
            {
                echo '<time datetime="' . $timedate . '">' . $text_date . '</time>';
                $date = $timedate;
            }
            
            echo '</td><td>';
            
            echo get_paginated_activities($item['activities'], false, '&nbsp;');
            
            echo '</td><td>';
            
            echo list_link($item['OutingI18n'][0], 'outings');
            $max_elevation = displayWithSuffix($item['max_elevation'], 'meters');
            if (!empty($max_elevation))
            {
                echo ' - ' . $max_elevation;
            }
            
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
        echo '</tbody></table>';
    }
    elseif (isset($empty_list_tips))
    {
        echo __($empty_list_tips);
    }
}

echo '<p class="list_link">' .
     picto_tag('action_list') . ' ' .
     link_to(__('List all linked outings') . $nb_outings, "outings/list?$module=$id&orderby=date&order=desc", array('rel' => 'nofollow')) .
     ' - ' .
     link_to(__('cond short'), "outings/conditions?$module=$id&orderby=date&order=desc") .
     ' - ' .
     link_to(picto_tag('picto_rss'), "outings/rss?$module=$id&orderby=date&order=desc") .
     '</p>';
