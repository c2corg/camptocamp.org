<?php
use_helper('Ajax', 'Form', 'Javascript', 'MyForm', 'MyImage');
echo tips_tag('insert image tag help');

if (count($associated_images) == 0): ?>
<p><?php echo __('No available image for insertion'); ?></p>
<?php else: ?>
<form action="#">
<div id="insertimagetag_list">
<?php foreach ($associated_images as $image):
    $caption = $image['name'];
    $image_tag = image_tag(image_url($image['filename'], 'small'),
                                     array('alt' => $caption, 'title' => $caption, 'class' => 'insertimagetag',
                                           'onclick' => 'C2C.updateSelectedImage(this)')); ?>
    <div class="image<?php if ($image == $associated_images[0]): echo ' selected_image'; endif; ?>" id="insertimagetag_id<?php echo $image['id'] ?>">
        <div class="image_actions">
            <?php echo $image_tag ?>
        </div>
    </div>
<?php endforeach; ?>
</div>
<div id="customize">
    <fieldset class="separator">
        <legend><?php echo __('options') ?></legend>
        <p>
<?php echo content_tag('label', checkbox_tag('customlegend', 'customlegend', false,
                       array('id' => 'inserted_image_customlegend', 'onclick' => 'C2C.doUpdateImageLegend();'))
               . ' ' . __('Custom legend')) . ' '
        . input_tag('legend', $associated_images[0]['name'], array('id' => 'inserted_image_legend', 'class' => 'medium_input', 'disabled' => 'disabled')); ?>
        </p>
        <p>
<?php
    echo __('Alignment') . ' '
       . content_tag('label', radiobutton_tag('inserted_image_alignment', 'right',  1, array('id' => 'alignment1')) . ' ' . __('right')) . ' '
       . content_tag('label', radiobutton_tag('inserted_image_alignment', 'left',   0, array('id' => 'alignment2')) . ' ' . __('left')) . ' '
       . content_tag('label', radiobutton_tag('inserted_image_alignment', 'center', 0, array('id' => 'alignment3')) . ' ' . __('center')) . ' '
       . content_tag('label', radiobutton_tag('inserted_image_alignment', 'inline', 0, array('id' => 'alignment4')) . ' ' . __('inline'));
    echo '</p><p>';
    echo content_tag('label',  __('hideborderlegend') . ' ' . checkbox_tag('hideborderlegend', 'hideborderlegend',
             false, array('id' => 'inserted_image_hideborderlegend')));
?>
        </p>
    </fieldset>
</div>
<?php echo input_hidden_tag('inserted_image_id', $associated_images[0]['id']). input_hidden_tag('inserted_image_div', $div); ?>
</form>
<ul class="action_buttons">
  <li><?php echo button_tag(__('Insert'), array('onclick' => 'C2C.doInsertImgTag()',
                                                'picto' => 'action_create')); ?></li>
  <li><?php echo button_tag(__('Cancel'), array('onclick' => '$.modalbox.hide();',
                                                'picto' => 'action_cancel')); ?></li>
</ul>
<?php endif; ?>
<div>
