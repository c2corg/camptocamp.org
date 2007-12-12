<div class="file_to_upload" id="div_image_<?php echo $image_number ?>">
  <?php echo form_error("image_$image_number"); ?>
  <p><?php echo __('File:') . ' ' . input_file_tag("image[$image_number]") 
                . ' ' .
                link_to_function(image_tag('/static/images/picto/rm.png',
                                           array('alt' => '-', 'title' => __('delete this file'))),
                                           "$('div_image_" . $image_number . "').remove()")
  ?></p>
  <?php echo form_error("name_$image_number"); ?>
  <p><?php echo __('name') . ' ' . input_tag("name[$image_number]") ?></p>
</div>
