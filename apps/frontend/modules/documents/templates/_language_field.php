<?php use_helper('Object', 'Language'); ?>

<?php echo start_group_tag() ?>
  <?php echo label_tag('culture', 'lang') ?>
  <?php
  if ($new_document)
  {   
      echo select_language_c2c_tag();
  }   
  else
  {   
      echo format_language_c2c($document->getCulture()) . '&nbsp;(' .
           link_to(__('choose an other language'),
                   '@document_by_id_lang?module=' . $sf_context->getModuleName() . '&id=' . $sf_params->get('id') .
                   '&lang=' . $sf_params->get('lang')) . ')' . 
                   '<input type="text" name="culture" id="culture" style="display: none;"/>';
                   // last line is useful to prevent an XHTML validation error (lacking 'culture' id field).
}   
?>  
<?php echo end_group_tag() ?>
