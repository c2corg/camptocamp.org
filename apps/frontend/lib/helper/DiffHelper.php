<?php
/**
 * This helper provides MediaWiki-like diffing tools.
 * $Id: DiffHelper.php 1161 2007-08-02 19:31:00Z alex $
 */

function show_documents_diff($document1, $document2, $fields, $module)
{
    if (get_class($document1) != get_class($document2))
    {
        return;
    }
    $haveDiff = false;

    foreach ($fields as $field)
    {
        $field1 = $document1->getRaw($field);
        $field2 = $document2->getRaw($field);
        $display_line_numbers = false;

        if ($field == 'filename')
        {
            $diff = show_imgs_diff($field1, $field2);
        }
        else if ($field == 'conditions_levels')
        {
            $fs = array($field1, $field2);
            foreach($fs as &$f)
            {
                foreach($f as &$row)
                {
                    $row = implode(' | ', $row);
                }
                $f = implode("\n", $f);
            }
            $diff = show_texts_diff($fs[0], $fs[1], true);
        }
        else
        {
            if (is_int($field1) || is_int($field2))
            {
                $display_line_numbers = true;
                $field1 = get_field_value($field, $module, $field1);
                $field2 = get_field_value($field, $module, $field2);
            }
            else if (is_array($field1) || is_array($field2))
            {
                $display_line_numbers = true;
                foreach ($field1 as &$value)
                {
                    $value = get_field_value($field, $module, $value);
                }
                foreach ($field2 as &$value)
                {
                    $value = get_field_value($field, $module, $value);
                }
                $field1 = implode(", ", $field1);
                $field2 = implode(", ", $field2);
            }

            $diff = show_texts_diff($field1, $field2, $display_line_numbers);
        }

        if ($diff)
        {
            if (!$haveDiff)
            {
                $haveDiff = true;
            }
            echo '<table class="diff">' . "\n";
            echo '<caption>' . __($field) . '</caption>' . "\n";
            echo '<tr><td class="diff-symbol"></td><td class="diff-content"></td><td style="diff-symbol;"></td><td class="diff-content"></td></tr>';
            echo "$diff\n";
            echo '</table>' . "\n";
        }
    }

    if (!$haveDiff)
    {
        echo '<p>' . __('No difference') . '</p>' . "\n";
    }
}

function show_texts_diff($text1, $text2, $display_line_numbers = false)
{
    if (is_null($text1))
    {
        $text1 = '';
    }
    
    if (is_null($text2))
    {
        $text2 = '';
    }

    if ($text1 == $text2 || !is_scalar($text1) || !is_scalar($text2))
    {
        // arguments are not scalars or are identical => do nothing
        return '';
    }

    $lines1 = explode("\n", $text1);
    $lines2 = explode("\n", $text2);
    $diffs = new Diff($lines1, $lines2);
    $formatter = new TableDiffFormatter($display_line_numbers);
    return $formatter->format($diffs);
}

function show_imgs_diff($img1, $img2)
{
    if ($img1 == $img2) return '';
    use_helper('MyImage');
    return '<tr><td>-</td><td class="diff-deletedline" style="text-align:center"><a href="'.image_url($img1).'"><img src="'.image_url($img1, "medium").'" /></a></td>'
           . '<td>+</td><td class="diff-addedline" style="text-align:center"><a href="'.image_url($img2).'"><img src="'.image_url($img2, "medium").'" /></td></tr>';
}

function get_field_value($field_name, $module, $abstract_value)
{
    switch ($field_name)
    {
        case 'activities': $conf = 'app_activities_list'; break;
        case 'area_type': $conf = 'mod_areas_area_types_list'; break;
        case 'categories': $conf = 'mod_' . $module . '_categories_list'; break;
        case 'article_type': $conf = 'mod_articles_article_types_list'; break;
        case 'book_types': $conf = 'mod_books_book_types_list'; break;
        case 'shelter_type': $conf = 'mod_huts_shelter_types_list'; break;
        case 'editor': $conf = 'mod_maps_editors_list'; break;
        case 'scale': $conf = 'mod_maps_scales_list'; break;
        case 'hut_status': $conf = 'mod_outings_hut_statuses_list'; break;
        case 'frequentation_status': $conf = 'mod_outings_frequentation_statuses_list'; break;
        case 'conditions_status': $conf = 'mod_outings_conditions_statuses_list'; break;
        case 'access_status': $conf = 'mod_outings_access_statuses_list'; break;
        case 'lift_status': $conf = 'mod_outings_lift_statuses_list'; break;
        case 'glacier_status': $conf = 'mod_outings_glacier_statuses_list'; break;
        case 'track_status': $conf = 'mod_outings_track_statuses_list'; break;
        case 'public_transportation_rating' : $conf = 'app_parkings_public_transportation_ratings'; break;
        case 'public_transportation_types': $conf = 'app_parkings_public_transportation_types'; break;
        case 'snow_clearance_rating': $conf = 'mod_parkings_snow_clearance_ratings_list'; break;
        case 'facing': $conf = 'app_routes_facings'; break;
        case 'route_type': $conf = 'mod_routes_route_types_list'; break;
        case 'duration': $conf = 'mod_routes_durations_list'; break;
        case 'toponeige_technical_rating': $conf = 'app_routes_toponeige_technical_ratings'; break;
        case 'toponeige_exposition_rating': $conf = 'app_routes_toponeige_exposition_ratings'; break;
        case 'labande_ski_rating': $conf = 'app_routes_labande_ski_ratings'; break;
        case 'labande_global_rating': $conf = 'app_routes_global_ratings'; break;
        case 'sub_activities': $conf = 'mod_routes_sub_activities_list'; break;
        case 'ice_rating': $conf = 'app_routes_ice_ratings'; break;
        case 'mixed_rating': $conf = 'app_routes_mixed_ratings'; break;
        case 'rock_free_rating': $conf = 'app_routes_rock_free_ratings'; break;
        case 'rock_required_rating': $conf = 'app_routes_rock_free_ratings'; break;
        case 'aid_rating': $conf = 'app_routes_aid_ratings'; break;
        case 'rock_exposition_rating': $conf = 'app_routes_rock_exposition_ratings'; break;
        case 'configuration': $conf = 'mod_routes_configurations_list'; break;
        case 'global_rating': $conf = 'app_routes_global_ratings'; break;
        case 'engagement_rating': $conf = 'app_routes_engagement_ratings'; break;
        case 'objective_risk_rating': $conf = 'app_routes_objective_risk_ratings'; break;
        case 'hiking_rating': $conf = 'app_routes_hiking_ratings'; break;
        case 'max_rating': $conf = 'mod_sites_rock_free_ratings_list'; break;
        case 'min_rating': $conf = 'mod_sites_rock_free_ratings_list'; break;
        case 'mean_rating': $conf = 'mod_sites_rock_free_ratings_list'; break;
        case 'equipment_rating': $conf = 'app_equipment_ratings_list'; break;
        case 'climbing_styles': $conf = 'mod_sites_climbing_styles_list'; break;
        case 'rock_types': $conf = 'mod_sites_rock_types_list'; break;
        case 'site_types': $conf = 'app_sites_site_types'; break;
        case 'children_proof': $conf = 'mod_sites_children_proof_list'; break;
        case 'rain_proof': $conf = 'mod_sites_rain_proof_list'; break;
        case 'facings': $conf = 'mod_sites_facings_list'; break;
        case 'summit_type': $conf = 'app_summits_summit_types'; break;
        case 'category': $conf = 'mod_users_category_list'; break;
        case 'image_type': $conf = 'mod_images_type_full_list'; break;
        default: return $abstract_value;
    }

    $list = sfConfig::get($conf);

    return (!empty($list[$abstract_value]) ? __($list[$abstract_value]) : '');
}
