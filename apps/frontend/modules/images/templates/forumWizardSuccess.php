<?php
// this file would be more logically in webforums, but symfony helpers are very useful
?>
<div id="images_wizard">
  <input type="file" id="images_wizard_file" />
  <ul>
    <li id="images_wizard_select_file">
      <div class="images_wizard_image" />
      <?php echo __('Click here to select an image from your computer or drag&drop it') ?>
    </li>
    <li id="images_wizard_url">
      <div class="images_wizard_image" />
      <span><?php echo __('Image on the internet') ?></span>
      <input type="text" id="images_wizard_url_input" placeholder="<?php echo __('enter URL or paste it from clipboard') ?>" />
    </li>
  </ul>
</div>
<script>C2C.init_forums_images_wizard({
  wait: "<?php echo __('wait while image is uploaded') ?>"
});</script>
