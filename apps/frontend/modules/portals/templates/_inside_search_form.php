<?php
use_helper('MyForm');

$params = array();

$params_fields = array('main' => 'topo_filter',
                      'outings' => 'outing_filter',
                      'images' => 'image_filter',
                      'videos' => 'video_filter',
                      'articles' => 'article_filter');
foreach ($params_fields as $key => $field)
{
    $value = $document->getRaw($field);
    if (!empty($value))
    {
        $params[$key] = $value;
    }
};

echo '</div>';
echo form_tag('documents/portalredirect', array('method' => 'get', 'class' => 'search'));
echo '<div class="sbox">';
echo portal_search_box_tag($params, 'portals');
echo '</div></form>';
?>