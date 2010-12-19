<div id="last_outings" class="latest">
<?php
use_helper('SmartDate', 'Pagination', 'Link');

if (!isset($default_open))
{
    $default_open = true;
}

if (isset($custom_title_text))
{
    $custom_title_text = $sf_data->getRaw('custom_title_text');
}
else
{
    $custom_title_text = '';
}

if (isset($custom_footer_text))
{
    $custom_footer_text = $sf_data->getRaw('custom_footer_text');
}
else
{
    $custom_footer_text = __('outings list');
}

if (isset($custom_url_params))
{
    $custom_url_params = $sf_data->getRaw('custom_url_params');
}
else
{
    $custom_url_params = '';
}

if (isset($custom_title_link))
{
    $custom_title_link = $sf_data->getRaw('custom_title_link');
}
else
{
    $url_params = $custom_url_params;
    if (!empty($url_params))
    {
        $url_params .= '&'; 
    }
    $custom_link = 'outings/list?' . $url_params . 'orderby=date&order=desc';
    $custom_title_link = $custom_link;
}

if (isset($custom_footer_link))
{
    $custom_footer_link = $sf_data->getRaw('custom_footer_link');
}
elseif (!empty($custom_url_params))
{
    $custom_footer_link = 'outings/list?' . $custom_url_params;
}
else
{
    $custom_footer_link = $custom_link;
}

$conditions_link = 'outings/conditions';
if (!empty($custom_url_params))
{
    $conditions_link .= '?' . $custom_url_params;
}

if (isset($custom_rss_link))
{
    $custom_rss_link = $sf_data->getRaw('custom_rss_link');
}
elseif (!empty($custom_url_params))
{
    $custom_rss_link = 'outings/rss?' . $custom_url_params;
}
else
{
    $custom_rss_link = '';
}

include_partial('documents/home_section_title',
                array('module'            => 'outings',
                      'custom_title_text' => $custom_title_text,
                      'custom_title_link' => $custom_title_link, // FIXME not sure, but prevents double escaping of ampersands
                      'custom_rss_link' => $custom_rss_link)); ?>
<div id="last_outings_section_container" class="home_container_text">
<?php if (count($items) == 0): ?>
    <p><?php echo __('No recent changes available') ?></p>
<?php else: ?>
    <ul class="dated_changes">
    <?php 
    $date = $list_item = 0;
    foreach ($items as $item): ?>
        <?php
            // Add class to know if li is odd or even
            if ($list_item%2 == 1): ?>
                <li class="odd">
            <?php else: ?>
                <li class="even">
            <?php endif;
            $list_item++;

            $timedate = $item['date'];
            if ($timedate != $date)
            {
                echo '<span class="date">' . format_date($timedate, 'dd/MM') . '</span>';
                $date = $timedate;
            }
            
            echo get_paginated_activities($item['activities']) . ' ';

            $i18n = $item['OutingI18n'][0];
            $id = $item['id'];
            $lang = $i18n['culture'];
            
            echo link_to($i18n['name'], "@document_by_id_lang_slug?module=outings&id=$id&lang=$lang&slug=" . make_slug($i18n['name']),
                         ($lang != $culture) ? array('hreflang' => $lang) : null);
            
            $outing_data = array();

            $max_elevation = displayWithSuffix($item['max_elevation'], 'meters');
            if (!empty($max_elevation))
            {
                $outing_data[] = $max_elevation;
            }

            $area_name = Area::getBestRegionDescription($item['geoassociations'], true);
            if (!empty($area_name))
            {
                $outing_data[] = $area_name;
            }

            if (count($outing_data) > 0)
            {
                echo ' <span class="meta">(' . implode(' - ', $outing_data) . ')</span>';
            }
            
            if (isset($item['nb_images']))
            {
                $images = picto_tag('picto_images_light',
                                    format_number_choice('[1]1 image|(1,+Inf]%1% images',
                                                         array('%1%' => $item['nb_images']),
                                                         $item['nb_images']))
                        . ' ';
                echo $images;
            }
            
            ?>
            </li>
    <?php endforeach ?>
    </ul>
<?php endif;?>
<div class="home_link_list">
<?php echo link_to($custom_footer_text, $custom_footer_link)
           . ' - ' .
           link_to(__('recent conditions'), $conditions_link)
           . ' - ' .
           link_to(__('Prepare outing'), getMetaArticleRoute('prepare_outings'));
      if ($sf_user->isConnected())
      {
           echo ' - ' . button_wizard();
      }
      echo ' - ' . customize_link_to();
?>
</div>
</div>
<?php
$cookie_position = array_search('last_outings', sfConfig::get('app_personalization_cookie_fold_positions'));
echo javascript_tag('setHomeFolderStatus(\'last_outings\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').");");
?>
</div>
