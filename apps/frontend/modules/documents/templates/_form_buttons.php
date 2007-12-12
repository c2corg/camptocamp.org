<?php use_helper('Javascript', 'Ajax'); ?>

<?php $module = $sf_context->getModuleName(); ?>
<?php $new = isset($new_document) ? $new_document : false ?>

<ul class="action_buttons">
  <li><?php echo submit_tag(__($new ? 'Create' : 'Update'), 
                            array('class' => 'action_create')) ?></li>

  <li><?php echo submit_to_remote('ajax_submit', __('Preview'), 
                                  array('update' => 'preview',
                                        'url' => "$module/preview",
                                        'method' => 'post',
                                        'loading' => "Element.show('indicator')",
                                        'complete' => "Element.show('preview');" . 
                                                      visual_effect('highlight', 'preview') . 
                                                      "Element.hide('indicator');"),
                                  array('class' => 'action_filter')) ?></li>

  <?php
  $cancel_route = $new ? ("@default_index?module=$module") :
                  ("@document_by_id?module=$module&id=" . $document->get('id'));
  ?>
  <li><?php echo button_to(__('Cancel'), $cancel_route,
                           array('class' => 'action_cancel')) ?></li>
</ul>
<?php //echo ajax_feedback() // commented because this generates two times indicator id on same edit page ! hence not xhmtl compliant ?>