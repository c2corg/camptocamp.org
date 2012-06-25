<?php 
use_helper('Pagination','Form', 'Language', 'MyForm', 'Viewer');

$module = $sf_context->getModuleName();

echo display_title(__('Search a ' . $module), $module);

if (!c2cTools::mobileVersion())
{
    echo '<div id="nav_space">&nbsp;</div>';
    include_partial('documents/nav4list');
}

echo display_content_top();
echo start_content_tag($module . '_content');

// TODO simplify this
echo format_number_choice('[0] No result found|(1,+Inf]Found %nb_result% results for %researched_word%',
                          array('%nb_result%' => 0, 
                                '%researched_word%' => '"'. $sf_params->get('q') . '"', 
                                ),
                          0);

include_partial('common/search_form', array('autocomplete' => false, 'prefix' => '_'));

echo '<br /><p>' . link_to(__('Advanced search'), '@filter?module=' . $module) . '</p>';

include_partial("documents/google_result", array('module' => $module, 'query_string' => $query_string));

echo end_content_tag();

include_partial('common/content_bottom') ?>
