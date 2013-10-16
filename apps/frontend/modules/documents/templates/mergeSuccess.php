<?php 
use_helper('Form', 'MyForm', 'Javascript', 'Ajax', 'Viewer');
$module = $sf_context->getModuleName();

echo display_title(__('Merge'));
?>
<div id="merge_wizard">
<?php
echo form_tag("$module/merge");
echo tips_tag('Which document would you like to redirect the current one to?');
echo input_hidden_tag('from_id', $sf_params->get('from_id'));
?>  

<div id="ac_form" style="float: left; margin-left: 10px; height: 250px; width: 300px;"> <?php //FIXME find a way to avoid ugly blank ?>
<?php // this div will be updated after page loading, via ajax. ?>
</div>

<script type="text/javascript">
$('#indicator').show();
$.ajax('<?php echo url_for("/$module/getautocomplete?module_name=$module&button=0")?>')
  .always(function() { $('#indicator').hide(); })
  .done(function(data) { $('#ac_form').html(data); });
</script>
<p><?php echo c2c_submit_tag(__('Merge')) ?></p>
</form>

<div>
<?php echo __('Notice: associations will also be merged'); ?>
</div>
</div> <!-- merge_wizard -->
