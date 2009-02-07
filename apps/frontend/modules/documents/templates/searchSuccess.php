<?php 
use_helper('Pagination','Form', 'Language', 'MyForm', 'Viewer');

$module = $sf_context->getModuleName();

echo display_title(__('Search'), 'documents');
echo '<div id="nav_space">&nbsp;</div>';
include_partial('documents/nav4list');

$table_list_even_odd = 0;
?>

<div id="wrapper_context">
<div id="ombre_haut">
    <div id="ombre_haut_corner_right"></div>
    <div id="ombre_haut_corner_left"></div>
</div>

<div id="content_article">
<div id="article" class="article <?php echo $module . '_content'; ?>">

<?php
$nb_results = $pager->getNbResults();
echo format_number_choice('[0] No result found|(1,+Inf]Found %nb_result% results for %researched_word%',
                          array('%nb_result%' => $nb_results, 
                                '%researched_word%' => '"'. $sf_params->get('q') . '"', 
                                ),
                          $nb_results);

echo include_partial('common/search_form');

if ($nb_results):
    $pager_navigation = pager_navigation($pager);
    echo $pager_navigation;
?>
<table class="list">
  <thead>
    <tr><?php echo include_partial($module . '/list_header'); ?></tr>
  </thead>
  <tbody>
<?php
$items = $pager->getResults('array', ESC_RAW);
    foreach ($items as $item): ?>
        <?php $table_class = ($table_list_even_odd++ % 2 == 0) ? 'table_list_even' : 'table_list_odd'; ?>
        <tr class="<?php echo $table_class ?>"><?php 
            echo include_partial($module . '/list_body', array('item' => $item, 'model_i18n' => $model_i18n));
        ?></tr>
    <?php endforeach ?>
  </tbody>
</table>
<?php
    echo $pager_navigation;
endif;
?>

</div>
</div>

<?php include_partial('common/content_bottom') ?>
