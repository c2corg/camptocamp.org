<?php use_helper('MyForm', 'Javascript', 'Ajax', 'Url'); ?>

<?php $module = $sf_context->getModuleName(); ?>
<?php $new = isset($new_document) ? $new_document : false ?>

<ul class="action_buttons">
  <li><?php echo c2c_button(__('Preview'), array('picto' => 'action_filter', 'class' => 'main_button'),
                            submit_to_remote('ajax_submit', __('Preview'), 
                                             array('update' => 'preview',
                                                   'url' => "$module/preview",
                                                   'method' => 'post',
                                                   'loading' => "Element.show('indicator')",
                                                   'complete' => "Element.show('preview');Element.show('form_buttons_up'); $$('span.goto_preview').each(function(elem){elem.show()});" . 
                                                                 visual_effect('highlight', 'preview') . 
                                                                 "Element.hide('indicator');",
                                                   'after' => "$$('span.goto_preview').each(function(elem){elem.show()});"),
                                             array('class' => 'c2cui_btnr'))) ?></li>

  <li><?php echo c2c_submit_tag(__($new ? 'Create' : 'Update'), 
                                array('picto' => 'action_create')) ?></li>

  <?php
  $cancel_route = $new ? ("@default_index?module=$module") :
                  ("@document_by_id?module=$module&id=" . $document->get('id'));
  ?>
   <li><?php echo c2c_button(__('Cancel'), array('picto' => 'action_cancel'), button_to(__('Cancel'), $cancel_route, array('class' => 'c2cui_btnr'))); ?></li>
</ul>
<?php //echo ajax_feedback() // commented because this generates two times indicator id on same edit page ! hence not xhmtl compliant ?>
