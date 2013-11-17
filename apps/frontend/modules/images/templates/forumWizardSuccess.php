<?php
// this file would be more logically in webforums, but symfony helpers are very useful
?>
<div id="images_wizard">
  <div id="images_wizard_ondrag" style="z-index:-1;"><p><?php echo __('plupload drop') ?></p></div>
  <input type="file" id="images_wizard_file" />
  <ul>
    <li id="images_wizard_select_file">
      <?php echo __('Image from computer') ?>
    </li>
    <li>
      <span><?php echo __('Image from internet') ?></span>
      <input type="text" id="images_wizard_url" placeholder="<?php echo __('enter URL or paste it') ?>" />
    </li>
    <li>Image from C2C</li>
  </ul>
</div>
<script>C2C.init_forums_images_wizard();</script>
