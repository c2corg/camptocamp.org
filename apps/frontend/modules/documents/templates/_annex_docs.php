<?php
use_helper('Sections', 'Field');

if (count($related_portals))
{
    echo start_section_tag('Annex', 'annex');
    
    echo '<ul id="list_associated_docs">';
    
    foreach ($related_portals as $portal)
    {
        $text = __(sfConfig::get('app_portals_' . $portal . '_name'));
        if ($portal == 'cda')
        {
            $html = '<a href="http://' . sfConfig::get('app_portals_cda_host') . '">' . $text . '</a>';
        }
        else
        {
            $html = link_to($text, '@document_by_id?module=portals&id=' . sfConfig::get('app_portals_' . $portal . '_id'));
        }
        echo li(picto_tag('picto_portals') . ' ' . $html);
    }
    
    echo '</ul>';
    
    echo end_section_tag();
}
