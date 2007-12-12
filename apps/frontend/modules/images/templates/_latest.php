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
            $lang = $item['culture'];
            $filename = $item['filename'];
            $title = $item['name'];
            $image_tag = image_tag(image_url($filename, 'small'),
                                   array('title' => $title, 'alt' => $title));
            echo link_to($image_tag, "@document_by_id_lang?module=images&id=$id&lang=$lang");
        ?>    
        </div>
    <?php endforeach ?>
    </div>
<?php endif;?>
<?php echo link_to(__('images list'), '@default_index?module=images', 
                   array('class' => 'home_link_list', 'style' => 'clear:both')) ?>
</div>
