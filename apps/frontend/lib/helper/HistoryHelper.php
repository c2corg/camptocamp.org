<?php
/**
 * History tools
 * @version $Id: HistoryHelper.php 1020 2007-07-23 18:48:01Z alex $
 */

use_helper('Form');

function radiobuttons_history_tag($row_nb, $version)
{
    switch ($row_nb)
    {
        case 1:
            $old_checked = false;
            $new_checked = true;
            break;

        case 2:
            $old_checked = true;
            $new_checked = false;
            break;

        default:
            $old_checked = $new_checked = false;
    }
    
    echo radiobutton_tag('old', $version, $old_checked) . 
         '&nbsp;' .
         radiobutton_tag('new', $version, $new_checked);
}

function display_revision_nature($nature, $is_minor)
{
    switch ($nature)
    {
        case 'to':
            $nature = 'Text only';
            break;

        case 'fo':
            $nature = 'Figures only';
            break;

        case 'ft':
            $nature = 'Figures & Text';
            break;

        default:
            return;
    }

    echo __($nature);
    
    if ($is_minor)
    {
        echo ' (<strong>' . __('minor_tag') . '</strong>)';
    }
}
