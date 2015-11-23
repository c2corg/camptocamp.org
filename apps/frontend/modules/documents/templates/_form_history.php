<?php use_helper('MyForm'); ?>
<div class="clear"></div>
<h3><?php echo __('Revision data') ?></h3>
<?php
echo group_tag('Rev comment', 'rev_comment', 'input_tag', $sf_params->get('rev_comment'), array('class' => 'long_input'));
echo group_tag('Minor change?', 'rev_is_minor', 'checkbox_tag', $sf_params->get('rev_is_minor'));
?>
