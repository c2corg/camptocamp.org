<?php 
use_helper('Pagination', 'MyImage', 'Lightbox', 'Javascript', 'Link', 'Viewer', 'General', 'Field', 'MyForm');

// add lightbox ressources
addLbMinimalRessources();

$id = $sf_params->get('id');
$lang = $sf_params->get('lang');

echo display_title(__('User image management'), $sf_params->get('module'));

echo '<div id="nav_space">&nbsp;</div>';
include_partial('documents/nav4home');
?>

<div id="wrapper_context">
<div id="ombre_haut">
    <div id="ombre_haut_corner_right"></div>
    <div id="ombre_haut_corner_left"></div>
</div>

<div id="content_article">
<div id="article" class="images_content">
<?php 
echo '<p class="mandatory_fields_warning">' . __('manage images presentation') . '</p>';

$items = $pager->getResults('array', ESC_RAW);

if (count($items) == 0):
    echo '<br /><br /><p>' . __('All your images are already collaborative') . '</p>';
else:
    echo '</p>';
    $pager_navigation = pager_navigation($pager);
    echo $pager_navigation;
    $items = Language::parseListItems($items, 'Image');
    $static_base_url = sfConfig::get('app_static_url');


    echo form_tag("@user_manage_images?module=users",
                  array('onsubmit' => 'submitonce(this)',
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
    $slug = formate_slug($i18n_item['search_name']);
    $image_id = $item['id'];
    $image_route = '@document_by_id_lang_slug?module=images&id=' . $image_id . '&lang=' . $i18n_item['culture'] . '&slug=' . $slug;
    echo link_to(image_tag($thumb_url, array('class' => 'img', 'alt' => $title)),
                 absolute_link(image_url($filename, 'big', true), true),
                 array('title' => $title,
                       'rel' => 'lightbox[document_images]',
                       'class' => 'view_big'));
    ?>
    <div class="manageimages_info">
        <?php
        echo input_tag('switch[]', null, array('type' => 'checkbox', 'value' => "$image_id", 'id' => 'switch_' . $image_id)) .
             link_to(__('Details'), $image_route, array('class' => 'toto'));
        ?>
        </div>
    </div>
<?php endforeach;

echo input_hidden_tag('page', $page);
?>
<div style="clear:both"><?php echo $pager_navigation; ?></div>
<ul class="action_buttons">
    <li><?php echo submit_tag(__('Switch license'), array('class' => 'action_edit')); ?></li>
    <li><?php echo button_tag(__('Select all'), __('Select all'), array('onclick' => "$$('form#editform div.manageimages_info input[type=checkbox]').each(function(obj){obj.checked=true;});",
                                                                    'class' => 'action_create',
                                                                    'title' => null)); ?></li>
    <li><?php echo button_tag(__('Deselect all'), __('Deselect all'), array('onclick' => "$$('form#editform div.manageimages_info input[type=checkbox]').each(function(obj){obj.checked=false;});",
                                                                        'class' => 'action_rm',
                                                                        'title' => null)); ?></li>
</ul>
</form>
<?php endif ?>
</div>
</div>

<?php include_partial('common/content_bottom') ?>
