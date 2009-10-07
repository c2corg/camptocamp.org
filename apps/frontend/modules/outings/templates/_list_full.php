<?php
use_helper('Language', 'Viewer', 'sfBBCode', 'SmartFormat', 'Field', 'SmartDate');

// lang-independent content starts here
$i18n = $item['OutingI18n'][0];
echo '<span class="item_title">' .
     format_date($item['date'], 'dd/MM/yyyy') . ' - ' .
     get_paginated_activities($item['activities']) . ' - ' .
     link_to($i18n['name'],
             '@document_by_id_lang_slug?module=outings&id=' . $i18n['id'] . '&lang=' . $i18n['culture'] . '&slug=' . formate_slug($i18n['search_name'])) . ' - ' .
     displayWithSuffix($item['max_elevation'], 'meters') . ' - ' .
     field_route_ratings_data($item, false, true);
if (isset($item['nb_images']))
{
    echo ' - ' . picto_tag('picto_images', __('nb_images')) . '&nbsp;' . $item['nb_images'];
}
if (isset($item['nb_comments']))
{
    echo ' - ' . picto_tag('action_comment', __('nb_comments')) . '&nbsp;' . link_to($item['nb_comments'], '@document_comment?module=outings&id='
. $item['OutingI18n'][0]['id'] . '&lang=' . $item['OutingI18n'][0]['culture']);
}
echo '</span>';

$participants = _format_text_data('participants', $i18n['participants'], null,
                                       array('needs_translation' => false,
                                             'show_label' => false,
                                             'show_images' => false));

$documents_versions_id = null;
foreach ($item['versions'] as $version)
{
    if (!$documents_versions_id || $version['documents_versions_id'] < $documents_versions_id)
    {
        $documents_versions_id = $version['documents_versions_id'];
        $author_info_name = $version['history_metadata']['user_private_data']['topo_name'];
        $author_info_id = $version['history_metadata']['user_private_data']['id'];
    }
}
$author = _format_data('author', link_to($author_info_name, '@document_by_id?module=users&id=' . $author_info_id));
echo '<div class="all_associations col_left col_66">';
include_partial('documents/association', array('associated_docs' => array(), 
                                                   'extra_docs' => array($author, $participants),
                                                   'module' => 'users', 
                                                   'inline' => true));
echo '</div>';

$geoassociations = $item['geoassociations'];
echo '<div class="all_associations col_right col_33">';
include_partial('areas/association', array('associated_docs' => $geoassociations, 'module' => 'areas'));
echo '</div>';

?>
    <ul class="data col_left col_33">
            <li><?php
                // get the first one that created the outing (whatever the culture) and grant him as author
                // smaller document version id = older one
                $documents_versions_id = null;
                foreach ($item['versions'] as $version)
                {
                    if (!$documents_versions_id || $version['documents_versions_id'] < $documents_versions_id)
                    {
                        $documents_versions_id = $version['documents_versions_id'];
                        $author_info_name = $version['history_metadata']['user_private_data']['topo_name'];
                        $author_info_id = $version['history_metadata']['user_private_data']['id'];
                    }
                }
                echo _format_data('author', link_to($author_info_name, '@document_by_id?module=users&id=' . $author_info_id));
                ?></li>
            <?php

            // FIXME sfOutputEscaperObjectDecorator shouldn't be used..
            $access_elevation = check_not_empty($item['access_elevation']) && !($item['access_elevation'] instanceof sfOutputEscaperObjectDecorator) ? $item['access_elevation'] : 0;
            $up_snow_elevation = check_not_empty($item['up_snow_elevation']) && !($item['up_snow_elevation'] instanceof sfOutputEscaperObjectDecorator) ? $item['up_snow_elevation'] : 0;
            $down_snow_elevation = check_not_empty($item['down_snow_elevation']) && !($item['down_snow_elevation'] instanceof sfOutputEscaperObjectDecorator) ? $item['down_snow_elevation'] : 0;
            if (check_not_empty($access_elevation) || check_not_empty($up_snow_elevation) || check_not_empty($down_snow_elevation)):
            ?>
            <li><?php
                if (check_not_empty($access_elevation))
                {
                    echo field_data_arg_if_set('access_elevation', $access_elevation, '', 'meters') . ' &nbsp; ';
                }
                echo field_data_arg_range_if_set('up_snow_elevation', 'down_snow_elevation', $up_snow_elevation, $down_snow_elevation, 'elevation separator', '', '', 'meters'); ?>
            </li><?php
            endif; ?>
    </ul>
<?php

$activities = BaseDocument::convertStringToArray($item['activities']);
if (!array_intersect(array(1,2,5), $activities))
{
    $conditions_levels = NULL;
}

echo '<div class="col_left col_66">';
if (!empty($conditions) || !empty($conditions_levels))
{
    echo '<div class="section_subtitle htext" id="_conditions">' . __('conditions') . '</div><div class="field_value">';
    $conditions_levels = $item['conditions_levels'];
    if (!empty($conditions_levels) && count($conditions_levels))
    {
        conditions_levels_data($conditions_levels);
    }
    echo parse_links(parse_bbcode($conditions, null, false));
    echo $other_conditions;
    echo '</div>';
    if ($needs_translation) echo '</div>';
}
echo '</div>';

echo '<div class="col_right col_33">';
echo _format_text_data('weather', $item['weather'], null, array('show_images' => false));
echo _format_text_data('timing', $item['timing'], null, array('show_images' => false));
echo '</div>';

echo '<div class="col_left col_66">';
echo _format_text_data('access_comments', $item['access_comments'], null, array('show_images' => false));
echo '</div>';

echo '<div class="col_left col_66">';
echo _format_text_data('hut_comments', $item['hut_comments'], null, array('show_images' => false));
echo '</div>';

echo _format_text_data('description', $item['description'], 'comments', array('show_images' => false));

