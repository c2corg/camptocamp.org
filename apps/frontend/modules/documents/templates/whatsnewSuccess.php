<?php use_helper('Javascript', 'Pagination', 'MyForm', 'Viewer', 'General');

use_javascript('/static/js/history_tools.js', 'last');

$module = $sf_context->getModuleName();
$table_list_even_odd = 0;

echo display_title(__('Recent changes'), $module, false);

echo '<div id="nav_space">&nbsp;</div>';
include_partial('documents/nav');

echo display_content_top('list_content');
echo start_content_tag($module . '_content');

?>
<p class="whatsnew_controls"><?php echo __('Recent changes list in category: %1%',
                 array('%1%' => __($module)))  ?>
 &nbsp; [<?php echo link_to_function(__('toggle date info'), 'tog()') ?>]
</p>

<p class="whatsnew_controls">
<?php 
$pager_navigation = pager_navigation($pager);
echo $pager_navigation;
?>
</p>
<br />
<p class="whatsnew_controls">
<?php
echo '<strong>' . __('minor_tag') . '</strong> = ' . __('minor modification') . '<br />';
echo label_tag('minor_revision_checkbox', __('hide minor revisions'));
echo checkbox_tag('minor_revision_checkbox', '1', false, array('onclick' => 'toggle_minor_revision();'));
?>
</p>

<?php //include_partial('documents/list_changes', array('items' => $items, 'needs_username' => true)) ?>

<table class="list">
    <thead>
        <tr>
            <?php if ($module == 'documents'): ?>
                <th class="cell_image"></th>
            <?php endif; ?>
            <th><?php echo __('Name'); ?></th>
            <th><?php echo __('Date'); ?></th>
            <th><?php echo __('Author'); ?></th>
            <th><?php echo __('Rev nature'); ?></th>
            <th><?php echo __('Rev comment'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php 
    foreach ($items as $item):
        $table_class = ($table_list_even_odd++ % 2 == 0) ? 'table_list_even' : 'table_list_odd'; ?>
        <tr class="<?php echo $table_class; if($item['history_metadata']['is_minor']) echo ' minor_revision'; ?>">
        <?php if ($module == 'documents'): ?>
            <td class="cell_image"><?php
                $module_name = $item['archive']['module'];
                echo picto_tag('picto_' . $module_name, __($module_name));
                ?></td>
        <?php endif; ?>
        <?php echo include_partial('documents/list_body_changes', array('item' => $item, 'table_class' => $table_class, 'needs_username' => true)); ?>
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
