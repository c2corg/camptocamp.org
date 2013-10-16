<?php 
use_helper('Pagination', 'MyImage', 'Lightbox', 'Javascript', 'Link', 'Viewer', 'General', 'Field', 'MyForm');

// add lightbox ressources
addLbMinimalRessources();

$id = $sf_params->get('id');
$lang = $sf_params->get('lang');

echo display_title(__('User image management'), $sf_params->get('module'), false);

echo '<div id="nav_space">&nbsp;</div>';
include_partial('documents/nav4home');

echo display_content_top('list_content');
echo start_content_tag('images_content');

echo javascript_tag('lightbox_msgs = Array("' . __('View image details') . '","' . __('View original image') . '");');

echo '<p class="mandatory_fields_warning">' . __('manage images presentation');

$items = $pager->getResults('array', ESC_RAW);

if (count($items) == 0):
    echo '<br /><br />' . __('All your images are already collaborative') . '</p>';
else:
    echo '</p>';
    $pager_navigation = pager_navigation($pager);
    echo $pager_navigation;
    echo '<div class="clearer"></div>';
    $items = Language::parseListItems($items, 'Image');


    echo form_tag("@user_manage_images?module=users",
                  array('onsubmit' => 'C2C.submitonce(this)',
                        'id' => 'editform'));
?>
<?php foreach ($items as $item): ?>
    <div class="thumb_data manageimages_list">
    <?php
    $i18n_item = $item['ImageI18n'][0];
    $title = $i18n_item['name'];
    $image_type = $item['image_type'];
    $filename = $item['filename'];
    $thumb_url = image_url($filename, 'small');
    $slug = make_slug($i18n_item['name']);
    $image_id = $item['id'];
    $image_route = '@document_by_id_lang_slug?module=images&id=' . $image_id . '&lang=' . $i18n_item['culture'] . '&slug=' . $slug;
    echo link_to(image_tag($thumb_url, array('class' => 'img', 'alt' => $title)),
                 absolute_link(image_url($filename, 'big', true), true),
                 array('title' => $title,
                       'data-lightbox' => 'document_images',
                       'class' => 'view_big',
                       'id' => 'lightbox_' . $image_id . '_' . $image_type));
    ?>
    <div class="manageimages_info">
        <?php
        echo input_tag('switch[]', null, array('type' => 'checkbox', 'value' => "$image_id", 'id' => 'switch_' . $image_id)) .
             link_to(__('Details'), $image_route, array('class' => 'toto'));
        ?>
        </div>
    </div>
<?php endforeach; ?>
<div style="clear:both"><?php echo input_hidden_tag('page', $page); ?><?php echo $pager_navigation; ?></div>
<ul class="action_buttons">
    <li><?php echo c2c_submit_tag(__('Switch license'), array('picto' => 'action_edit')); ?></li>
    <li><?php echo button_tag(__('Select all'), array('onclick' => "$('#editform .manageimages_info input[type=checkbox]').prop('checked', true);",
                                                      'picto' => 'action_create',
                                                      'title' => __('Select all'))); ?></li>
    <li><?php echo button_tag(__('Deselect all'), array('onclick' => "$('#editform .manageimages_info input[type=checkbox]').prop('checked', false);",
                                                        'picto' => 'action_rm',
                                                        'title' => __('Deselect all'))); ?></li>
</ul>
</form>
<?php
endif;

echo end_content_tag();

include_partial('common/content_bottom') ?>
