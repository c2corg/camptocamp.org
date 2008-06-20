<?php
use_helper('Ajax', 'AutoComplete', 'Form');
echo ajax_feedback();
?>
<span class="article_title"><?php echo __('Outing Wizard'); ?></span>

<div id="indicator2" style="display: none;"><?php echo __(' loading...'); ?></div>

<div id="outing_wizard">

<hr />
<h4><?php echo __('Step 1: choose a summit'); ?></h4>
<div id="wizard_summit">
<?php 
$updated_failure = sfConfig::get('app_ajax_feedback_div_name_failure');
echo form_tag();
echo input_hidden_tag('summit_id', '0');
echo __('Summit:');
echo input_auto_complete_tag('summits_name', 
                            '', // default value in text field 
                            "summits/autocomplete", 
                            array('size' => '20'), 
                            array(  'after_update_element' => "function (inputField, selectedItem) { 
                                                                $('summit_id').value = selectedItem.id;Element.show('indicator2');Element.hide('wizard_no_route');". 
                                                                remote_function(array(
                                                                                        'update' => array(
                                                                                                        'success' => 'divRoutes', 
                                                                                                        'failure' => $updated_failure),
                                                                                        'url' => 'summits/getroutes',
                                                                                        'indicator' => 'indicator2', // does not work for an unknown reason.
                                                                                        'with' => "'summit_id=' + $('summit_id').value + '&div_id=routes'",
                                                                                        'complete' => "Element.hide('indicator2');getWizardRouteRatings();",
                                                                                        'success'  => "Element.hide('wizard_no_route');Element.show('wizard_route');Element.show('last_ok');",
                                                                                        'failure'  => "Element.show('$updated_failure');Element.show('wizard_no_route');" . 
                                                    visual_effect('fade', $updated_failure, array('delay' => 2, 'duration' => 3)))) ."}",
                                    'min_chars' => sfConfig::get('app_autocomplete_min_chars'), 
                                    'indicator' => 'indicator'));
?>
<p id="wizard_summit_create" class="wizard_tip"><?php echo __('No summit matching your search?') . ' ' . 
link_to(__('Add your summit'), '@document_edit?module=summits&id=&lang='); ?></p>
</form>
</div> <!-- wizard_summit -->

<div id="wizard_no_route" style="display: none">
<hr />
<h4><?php echo __('Step 2: Create a route')  ?></h4>
<p class="wizard_tip">
<a href="" onclick="window.open('/routes/edit/link/' + $('summit_id').value);"><?php echo __('Add your route') ?></a>
</p>
</div>

<div id="wizard_route" style="display: none">
<hr />
<h4><?php echo __('Step 2: choose a route')  ?></h4>
<p> <!-- sub_wizard_route -->
<?php echo __('Route:') ?>
<span id="divRoutes" name="divRoutes"></span></p>
<!-- sub_wizard_route -->

<p id="wizard_route_descr" style="display: none">
<?php echo __('Short description: '); ?>
<span id="route_descr"><?php echo __('not available'); ?></span>
</p> <!-- wizard_route_descr -->
<p class="wizard_tip"><?php echo __('No route matching your search?') . ' '; ?>
<a href="#" onclick="window.location.href='/routes/edit/link/' + $('summit_id').value; return false;"><?php echo __('Add your route') ?></a></p>

<div id="last_ok" style="display: none;">
<hr />
<h4><?php echo __('Step 3: confirm to create outing')  ?></h4>
<?php 
echo form_tag('outings/edit', 'method=get');
echo input_hidden_tag('link', '0');
echo submit_tag(__('New outing'), array(
                                    'onclick' => '$("link").value = $("routes").options[$("routes").selectedIndex].value',
                                    'title' => __('Add your outing'),
                                    //'class' => 'action_create',
                                    'style' =>  'padding-left: 20px; 
                                                padding-right: 5px; 
                                                background: url(/static/images/picto/add.png) no-repeat 3px center;'
                                    )); 
?>
</form>
</div> <!-- last_ok -->

</div> <!-- wizard_route -->


