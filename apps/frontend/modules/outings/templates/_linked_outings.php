<?php
use_helper('General', 'SmartDate', 'Pagination', 'Link');
$is_mobile_version = c2cTools::mobileVersion();
$is_connected = $sf_user->isConnected();

if(isset($nb_outings))
{
    if (isset($items))
    {
        $nb_items = count($items);
        if (!isset($nb_outings_limit))
        {
            $nb_outings_limit = 0;
        }
        if ($nb_items > $nb_outings)
        {
            $nb_outings = "> $nb_outings";
        }
        elseif ($nb_outings_limit == 0)
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
            if ($is_mobile_version)
            {
                $text_date = $timedate;
            }
            else
            {
                $text_date = format_date($timedate, 'D');
            }
            if ($timedate != $date || $is_mobile_version)
            {
                echo '<time datetime="' . $timedate . '">' . $text_date . '</time>';
                $date = $timedate;
            }
            
            echo '</td><td>';
            
            echo get_paginated_activities($item['activities'], false, '&nbsp;');
            
            echo '</td><td>';
            
            echo list_link($item['OutingI18n'][0], 'outings');
            $max_elevation = displayWithSuffix($item['max_elevation'], 'meters');
            
            if (!$is_mobile_version)
            {
                echo '</td><td>';
            }
            if (!empty($max_elevation))
            {
                if ($is_mobile_version)
                {
                    echo ' - ';
                }
                echo $max_elevation;
            }
            
            if (!$is_mobile_version)
            {
                echo '</td><td>';
            }
            if (isset($item['nb_images']))
            {
                $images = picto_tag('picto_images_light',
                                    format_number_choice('[1]1 image|(1,+Inf]%1% images',
                                                         array('%1%' => $item['nb_images']),
                                                         $item['nb_images']));
                if ($is_mobile_version)
                {
                    echo ' ';
                }
                echo $images;
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
$join_outing = (!in_array($module, array('outings', 'users'))) ? 'join=outing&' : '';
$module_url = ($module != 'users') ? $module : 'ousers';
echo '<p class="list_link">',
     picto_tag('action_list'), ' ',
     link_to(__('List all linked outings') . $nb_outings, "outings/list?$module=$id&orderby=date&order=desc", array('rel' => 'nofollow')),
     ' - ',
     link_to(__('cond short'), "outings/conditions?$module=$id&orderby=date&order=desc"),
     ' - ',
     link_to(__('Comments'), "outings/conditions?$module=$id&format=full&orderby=date&order=desc"),
     ' - ',
     link_to(__('Images'), "images/list?$join_outing$module_url=$id&orderby=odate&order=desc", array('rel' => 'nofollow'));

if ($module == 'users')
{
    echo ' - ', link_to(__('Statistics'), 'http://' . sfConfig::get('app_statistics_base_url') . '/user/' . $id);
}
else if ($is_connected)
{
    echo ' - ', link_to(__('My outings'), "outings/list?$module=$id&myoutings=1&orderby=date&order=desc");
}

if (in_array($module, array('summits', 'routes', 'parkings', 'huts', 'sites')))
{
    echo ' - ', link_to(picto_tag('action_gps'), "outings/list?$module=$id&geom=yes&orderby=date&order=desc");
    
    if (isset($lat) && $lat)
    {
        if (isset($document))
        {
            $activities = $document->getRaw('activities');
        }
        else
        {
            $activities = array();
        }
        
        if(count($activities))
        {
            $activities_url = 'act=' . implode('-', $activities) . '&';
        }
        else
        {
            $activities_url = '';
        }
        
        echo ' - ', link_to(ucfirst(__('within km: ')) . ' 10km', "outings/list?" . $activities_url . "sarnd=$lon,$lat,10000&orderby=date&order=desc");
    }
}
     
if (!$is_mobile_version)
{
    echo ' - ', link_to(picto_tag('picto_rss'), "outings/rss?$module=$id&orderby=date&order=desc");
}

echo '</p>';
