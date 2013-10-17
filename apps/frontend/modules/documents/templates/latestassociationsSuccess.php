<?php use_helper('Javascript', 'Pagination', 'MyForm', 'SmartDate', 'Viewer', 'General');

use_javascript('/static/js/history_tools.js', 'last');

echo display_title(__('Recent associations'), null, false);

echo '<div id="nav_space">&nbsp;</div>';
include_partial('documents/nav');

echo display_content_top('list_content');
echo start_content_tag();

?>
<p class="whatsnew_controls"><?php echo __('Recent associations list') . ' '; ?>
[<?php echo link_to_function(__('toggle date info'), 'C2C.toggle_time()') ?>]
</p>

<p class="whatsnew_controls">
<?php
$pager_navigation = pager_navigation($pager);
echo $pager_navigation;
?>
</p>

<?php 
$added_pic = picto_tag('picto_add', __('added'));
$deleted_pic = picto_tag('picto_rm', __('deleted'));
?>

<table class="list">
    <thead>
        <tr>
            <th>&nbsp;</th>
            <th><?php echo __('Date'); ?></th>
            <th><?php echo __('Main document'); ?></th>
            <th><?php echo __('Linked document'); ?></th>
            <th><?php echo __('Author'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php
    $table_list_even_odd = 0;
    
    foreach ($items as $item):
    
        $table_class = ($table_list_even_odd++ % 2 == 0) ? 'table_list_even' : 'table_list_odd'; 
        $user_link = '@document_by_id?module=users&id=' . $item['user_private_data']['id']; 
        $models = c2cTools::Type2Models($item['type']);
        $main_module = c2cTools::model2module($models['main']);
        $linked_module = c2cTools::model2module($models['linked']);
        $main_item = count($item['mainI18n']) ? $item['mainI18n'][0] : null;
        $linked_item = count($item['linkedI18n']) ? $item['linkedI18n'][0] : null;

        // FIXME: routes slugs
        $main_link = '@document_by_id_lang_slug?module=' . $main_module . '&id=' . $item['main_id'] .
                     '&lang=' . $main_item['culture'] . '&slug=' . make_slug($main_item['name']);
        $linked_link = '@document_by_id_lang_slug?module=' . $linked_module . '&id=' . $item['linked_id'] .
                        '&lang=' . $linked_item['culture'] . '&slug=' . make_slug($linked_item['name']);
        ?>
        <tr class="<?php echo $table_class; if ($item['is_creation']) echo ' creation'; else echo ' deletion'; ?>">
            <td> <?php echo ($item['is_creation']) ? $added_pic : $deleted_pic ; ?> </td>
            <td> <?php echo smart_date($item['written_at']) ?> </td>
            <td> <?php echo '<div class="assoc_img picto_'.$main_module.'" title="'.__($main_module).'"></div>' . 
                            (!empty($main_item) ? link_to($main_item['name'], $main_link) : __('deleted document')); ?> </td>
            <td> <?php echo '<div class="assoc_img picto_'.$linked_module.'" title="'.__($linked_module).'"></div>' . 
                            (!empty($linked_item) ? link_to($linked_item['name'], $linked_link) : __('deleted document')); ?> </td>
            <td> <?php echo link_to($item['user_private_data']['topo_name'], $user_link); ?> </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
<p class="whatsnew_controls">
<?php echo $pager_navigation; ?>
</p>

<?php
echo end_content_tag();

include_partial('common/content_bottom') ?>
