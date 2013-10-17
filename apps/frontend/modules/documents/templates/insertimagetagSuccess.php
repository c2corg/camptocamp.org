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
<?php echo checkbox_tag('customlegend', 'customlegend', false, array('onclick' => 'C2C.doUpdateImageLegend();'))
         . ' ' . label_for('customlegend', __('Custom legend')) . ' '
         . input_tag('legend', $associated_images[0]['name'], array('class' => 'medium_input', 'disabled' => 'disabled')); ?>
        </p>
        <p>
<?php
    echo __('Alignment') . ' '
       . radiobutton_tag('alignment', 'right',  1, array('id' => 'alignment1')) . ' ' . label_for('alignment1', __('right')) . ' '
       . radiobutton_tag('alignment', 'left',   0, array('id' => 'alignment2')) . ' ' . label_for('alignment2', __('left')) . ' '
       . radiobutton_tag('alignment', 'center', 0, array('id' => 'alignment3')) . ' ' . label_for('alignment3', __('center')) . ' '
       . radiobutton_tag('alignment', 'inline', 0, array('id' => 'alignment4')) . ' ' . label_for('alignment4', __('inline'));
    echo '</p><p>';
    echo label_for('hideborderlegend', __('hideborderlegend')) . ' '
       . checkbox_tag('hideborderlegend', 'hideborderlegend', false);
?>
        </p>
    </fieldset>
</div>
<?php echo input_hidden_tag('id', $associated_images[0]['id']). input_hidden_tag('div', $div); ?>
</form>
<ul class="action_buttons">
  <li><?php echo button_tag(__('Insert'), array('onclick' => 'C2C.doInsertImgTag()',
                                                'picto' => 'action_create')); ?></li>
  <li><?php echo button_tag(__('Cancel'), array('onclick' => '$.modalbox.hide();',
                                                'picto' => 'action_cancel')); ?></li>
</ul>
<?php endif; ?>
<div>
