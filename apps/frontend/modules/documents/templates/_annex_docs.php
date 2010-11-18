<?php
use_helper('Sections', 'Field');

if (count($related_portals))
{
    echo start_section_tag('Annex', 'annex');
    
    echo '<ul id="list_associated_docs">';
    
    $portals_definition = sfConfig::get('app_portals');
    foreach ($related_portals as $portal)
    {
        $text = __($portals_definition[$portal]['name']);
        if ($portal == 'cda')
        {
            $html = '<a href="http://' . $portals_definition['cda']['host'] . '">' . $text . '</a>';
        }
        else
        {
            $html = link_to($text, '@document_by_id?module=portals&id=' . $portals_definition[$portal]['id']);
        }
        echo li(picto_tag('picto_portals') . ' ' . $html);
    }
    
    echo '</ul>';
    
    echo end_section_tag();
}
