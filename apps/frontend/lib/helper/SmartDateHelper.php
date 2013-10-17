<?php
/**
 * This helper formats raw dates
 * $Id: SmartDateHelper.php 1759 2007-09-22 20:31:45Z alex $
 */

use_helper('Date');

function add_span($in, $date)
{
    return '<time datetime="' . date('c', strtotime($date)). '"><span class="relative_time">' . $in . '</span><span class="absolute_time" style="display:none">' .
           format_datetime($date).'</span></time>';
}

function smart_date($date, $is_timestamp = false)
{
    $timestamp = $is_timestamp ? $date : strtotime($date);
    $current_timestamp = time();

    $years = date('Y', $current_timestamp - $timestamp) - 1970;
    switch ($years)
    {
        case 0: break;
        case 1: return add_span(__('last year'), $date);
        default: return add_span(__('%1% years ago', array('%1%' => $years)), $date);
    }

    $months = date('n', $current_timestamp - $timestamp) - 1;
    switch ($months)
    {
        case 0: break;
        case 1: return add_span(__('last month'), $date);
        default: return add_span(__('%1% months ago', array('%1%' => $months)), $date);
    }    

    $weeks = date('W', $current_timestamp - $timestamp) - 1;
    switch ($weeks)
    {
        case 0: break;
        case 1: return add_span(__('last week'), $date);
        default: return add_span(__('%1% weeks ago', array('%1%' => $weeks)), $date);
    }    

    $time = date('H:i', $timestamp);
    if (date('Y', $current_timestamp) == date('Y', $timestamp))
    {
        $days = date('z', $current_timestamp) - date('z', $timestamp);
    }
    else
    {
        $days = date('z', $current_timestamp) + (365 + date('L', $timestamp) - date('z', $timestamp));
    }
    switch ($days)
    {
        case 0: break;
        case 1: return add_span(__('yesterday, at %1%', array('%1%' => $time)), $date); 
        default: return add_span(__('%1% days ago, at %2%', array('%1%' => $days, '%2%' => $time)), $date);
    }
    
    $hours = date('G', $current_timestamp - $timestamp) - 1; 
    switch ($hours)
    {
        case 0: break;
        default: return add_span(__('today, at %1%', array('%1%' => $time)), $date);
    }

    $minutes = date('i', $current_timestamp - $timestamp);
    switch ($minutes < 5)
    {
        case true : return add_span(__('very recently'), $date);
        default: return add_span(__('%1% minutes ago', array('%1%' => $minutes)), $date);
    }
}
