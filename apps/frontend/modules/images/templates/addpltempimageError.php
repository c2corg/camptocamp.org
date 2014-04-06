<?php use_helper('MyForm', 'Javascript'); ?>
<div class="plupload-error">
  <div>
    <?php echo $image_name; ?>
    <div class="global_form_error">
      <ul>
        <?php
        foreach(sfContext::getInstance()->getRequest()->getErrors() as $name => $error)
        {
            echo '<li>' . __($error) . '</li>';
        }
        ?>
      </ul>
    </div>
  </div>
  <button type="button" class="plupload-close">×</button>
</div>
