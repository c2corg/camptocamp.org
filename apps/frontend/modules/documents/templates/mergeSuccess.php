<?php 
use_helper('Form', 'MyForm', 'Javascript', 'Ajax', 'Viewer');
$module = $sf_context->getModuleName();
echo ajax_feedback(); 

echo display_title(__('Merge'));
?>
<div id="merge_wizard">
<?php
echo form_tag("$module/merge");
echo tips_tag('Which document would you like to redirect the current one to?');
echo input_hidden_tag('from_id', $sf_params->get('from_id'));
?>  

<div id="ac_form" style="float: left; margin-left: 10px; height: 250px; width: 300px;">
<?php // this div will be updated after page loading, via ajax. ?>
</div>

<script type="text/javascript"> 
//<![CDATA[
function au()
{
    new Ajax.Updater('ac_form', '/<?php echo $module ?>module/getautocomplete', {asynchronous:true, evalScripts:true, onComplete:function(request, json){Element.hide('indicator')}, onLoading:function(request, json){Element.show('indicator')}, parameters:'module_name=<?php echo $module ?>&button=0'})
}

if (window.addEventListener) { 
    window.addEventListener('load', au(), false); 
} else if (window.attachEvent) { 
    window.attachEvent('onload', au()); 
} 
//]]>
</script>

<p><?php echo submit_tag(__('Merge')) ?></p>
</form>

<div>
<?php echo __('Notice: associations will also be merged'); ?>
</div>
</div> <!-- merge_wizard -->
