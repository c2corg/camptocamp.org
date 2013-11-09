<?php
use_helper('Sections', 'Field');

if (!isset($related_articles))
{
    $related_articles = array();
}


if (count($related_articles) || count($related_portals))
{
    echo start_section_tag('Annex', 'annex');
    
    if (count($related_articles))
    {
        $module = $document->get('module');
        $fixed_type = c2cTools::Module2Letter($module) . 'c';
        include_partial('articles/association',
                        array('document' => $document,
                              'associated_documents' => $related_articles,
                              'show_link_to_delete' => $show_link_to_delete,
                              'show_link_tool' => false,
                              'show_default_text' => false,
                              'fixed_type' => $fixed_type,
                              'id_list_associated_docs' => 'list_associated_articles'));
    }
    
    if (count($related_portals))
    {
        echo '<ul id="list_related_portals" class="no_print">';
        
        foreach ($related_portals as $portal)
        {
            $portal_config = sfConfig::get('app_portals_' . $portal);

            $text = __($portal_config['name']);
            if (isset($portal_config['url']) || isset($portal_config['annex_url']))
            {
                if (isset($portal_config['url']))
                {
                    $portal_url = $portal_config['url'];
                }
                else
                {
                    $portal_url = $portal_config['annex_url'];
                }
                $portal_url = $portal_url;
            }
            else
            {
                $portal_url = '@document_by_id?module=portals&id=' . $portal_config['id'];
            }
            $html = link_to($text, $portal_url);
            echo li(picto_tag('picto_portals') . ' ' . $html);
        }
        
        echo '</ul>';
    }
    
    echo end_section_tag();
}
