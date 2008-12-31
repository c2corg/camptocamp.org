<div class="latest">
<?php
use_helper('Link', 'MyImage');

include_partial('documents/latest_title', array('module' => 'images'));

if (count($items) == 0): ?>
    <p class="recent-changes"><?php echo __('No recent images available') ?></p>
<?php else: ?>
    <div id="image_list">
    <?php foreach ($items as $item): ?>
        <div class="image">
        <?php 
            $id = $item['id'];
            $filename = $item['filename'];
            
            // FIXME: use prefered language
            $i18n = $item['ImageI18n'][0];
            $lang = $i18n['culture'];
            $title = $i18n['name'];
            
            $image_tag = image_tag(image_url($filename, 'small'),
                                   array('title' => $title, 'alt' => $title));
            echo link_to($image_tag, "@document_by_id_lang_slug?module=images&id=$id&lang=$lang&slug=" . formate_slug($i18n['search_name']));
        ?>    
        </div>
    <?php endforeach ?>
    </div>
<?php endif;?>
<?php echo link_to(__('images list'), '@default_index?module=images', 
                   array('class' => 'home_link_list', 'style' => 'clear:both')) ?>
</div>
