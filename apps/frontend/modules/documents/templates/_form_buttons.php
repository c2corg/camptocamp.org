<?php use_helper('MyForm', 'Javascript', 'Ajax', 'Url'); ?>

<?php $module = $sf_context->getModuleName(); ?>
<?php $new = isset($new_document) ? $new_document : false ?>

<ul class="action_buttons">
  <li><?php

  // TODO highlight effect
  $js = "jQuery('#indicator').show();
  jQuery.post('" . url_for("$module/preview") . "', jQuery(this.form).serialize())
    .always(function() { jQuery('#indicator').hide(); jQuery('.goto_preview').show(); })
    .done(function(data) { jQuery('#preview').html(data).show(); jQuery('#form_buttons_up').show(); });
  return false;";

  echo c2c_button(__('Preview'), array('picto' => 'action_filter', 'class' => 'main_button'),
                            tag('input', array('type' => 'button',
                                               'name' => 'ajax_submit',
                                               'value' => __('Preview'),
                                               'class' => 'c2cui_btnr',
                                               'onclick' => $js))) ?></li>
  <li><?php echo c2c_submit_tag(__($new ? 'Create' : 'Update'), 
                                array('picto' => 'action_create')) ?></li>

  <?php
  $cancel_route = $new ? ("@default_index?module=$module") :
                  ("@document_by_id?module=$module&id=" . $document->get('id'));
  ?>
   <li><?php echo c2c_button(__('Cancel'), array('picto' => 'action_cancel'), button_to(__('Cancel'), $cancel_route, array('class' => 'c2cui_btnr'))); ?></li>
</ul>
