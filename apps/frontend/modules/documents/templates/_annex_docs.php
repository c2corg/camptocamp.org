<?php
use_helper('Sections', 'Field');

if (count($related_portals))
{
    echo start_section_tag('Annex', 'annex');
    
    echo '<ul id="list_associated_docs">';
    
    foreach ($related_portals as $portal)
    {
        $config = sfConfig::get('app_portals_' . $portal);

        $text = __($config['name']);
        if ($portal == 'cda')
        {
            $html = '<a href="http://' . $config['host'] . '">' . $text . '</a>';
        }
        else
        {
            $html = link_to($text, '@document_by_id?module=portals&id=' . $config['id']);
        }
        echo li(picto_tag('picto_portals') . ' ' . $html);
    }
    
    echo '</ul>';
    
    echo end_section_tag();
}
