<?php
use_helper('JavascriptQueue', 'AutoComplete', 'Form', 'MyForm');
?>
<span class="article_title"><?php echo __('Outing Wizard'); ?></span>

<div id="fake_div">

<div id="outing_wizard">

<hr />
<h4><?php echo __('Step 1: choose a summit'); ?></h4>
<div>
<?php 
echo global_form_errors_tag();
echo select_tag('wizard_type', options_for_select(array('summits' => __('Summit:'), 'sites' => __('Site:')), 0));
echo input_tag('summits_name', '', array('size' => '35'));
echo input_tag('sites_name', '', array('size' => '35', 'style' => 'display:none'));
echo javascript_queue("var indicator = $('#indicator');
$('#wizard_type').change(function() {
  $('#summits_name, #sites_name').val('').toggle();
  $('#wizard_no_route, #wizard_route, #summit_link, #last_ok').hide();

  var summit = $(this).val() == 'summits';
  $('#wizard_routes_hints').toggle(summit);
  $('#wizard_sites_hints').toggle(!summit);
  
});

$('#summits_name').c2cAutocomplete({
  url: '" . url_for('summits/autocomplete') . "',
  minChars: " . sfConfig::get('app_autocomplete_min_chars') . "
}).on('itemselect', function(e, item) {
  indicator.show();
  $('#summit_id').val(item.id);
  $('#wizard_no_route, .wizard_hints').hide();
  $.get('" . url_for('summits/getroutes') . "',
             'summit_id=' + $('#summit_id').val() + '&div_name=routes')
    .always(function() { indicator.hide(); })
    .done(function(data) {
      $('#divRoutes').html(data);
      $('#last_ok h4').html($('#wizard_routes_hints h4').last().html());
      $('#wizard_no_route').hide();
      $('#summit_link, #wizard_route, #last_ok').show();
      C2C.getWizardRouteRatings('routes');
    })
    .fail(function(data) {
      $('#wizard_route, #wizard_hints, #wizard_route_descr, #last_ok').hide();
      $('#summit_link, #wizard_no_route').show();
      C2C.showFailure(data.responseText);
    })
});

$('#sites_name').c2cAutocomplete({
  url: '" . url_for('sites/autocomplete') . "',
  minChars: " . sfConfig::get('app_autocomplete_min_chars') . "
}).on('itemselect', function(e, item) {
  $('.wizard_hints').hide();
  $('#last_ok h4').html($('#wizard_sites_hints h4').last().html());
  $('#last_ok').show();
  $('#link').val(item.id);
});
");

echo form_tag('outings/wizard');
echo input_hidden_tag('summit_id', '0');
?>
<p id="summit_link" style="display: none">
<a href="#" onclick="window.open('/summits/' + $('#summit_id').val());"><?php echo __('Show the summit') ?></a>
</p>
<p id="wizard_summit_create" class="wizard_tip"><?php echo __('No summit matching your search?') . ' ' . 
link_to(__('Add your summit'), '@document_edit?module=summits&id=&lang='); ?></p>
</form>
</div>

<div id="wizard_routes_hints" class="wizard_hints">
<hr />
<h4><?php echo __('Step 2: choose a route')  ?></h4>
<h4><?php echo __('Step 3: confirm to create outing')  ?></h4>
</div>
<div id="wizard_sites_hints" class="wizard_hints" style="display: none">
<hr />
<h4><?php echo __('Step 2: confirm to create outing')  ?></h4>
</div>

<div id="wizard_no_route" style="display: none">
<hr />
<h4><?php echo __('Step 2: Create a route')  ?></h4>
<p class="wizard_tip">
<a href="#" onclick="window.open('/routes/edit/link/' + $('#summit_id').val());"><?php echo __('Add your route') ?></a>
</p>
</div>

<div id="wizard_route" style="display: none">
<hr />
<h4><?php echo __('Step 2: choose a route')  ?></h4>
<p> <!-- For some unknown reason, ie7&8 don't like spans here, they want divs... Else, AjaxUpdater get stuck for apparently no reason -->
<!--[if IE]> <![if !IE]> <![endif]--><span id="divRoutes"></span><!--[if IE]> <![endif]> <![endif]-->
<!--[if IE]><div id="divRoutes"></div><![endif]-->
</p>

<p id="wizard_routes_descr" style="display: none">
<?php echo __('Short description: '); ?>
<span id="routes_descr"><?php echo __('not available'); ?></span>
<br />
<a href="#" onclick="window.open('/routes/' + $('#routes').val();">
  <?php echo __('Show the route') ?>
</a>
</p> <!-- wizard_route_descr -->
<p class="wizard_tip"><?php echo __('No route matching your search?') . ' '; ?>
<a href="#" onclick="window.location.href='/routes/edit/link/' + $('#summit_id').val(); return false;"><?php echo __('Add your route') ?></a></p>
</div>

<div id="last_ok" style="display: none;">
<hr />
<h4><?php echo __('Step 3: confirm to create outing')  ?></h4>
<?php 
echo form_tag('outings/edit', 'method=get');
echo input_hidden_tag('link', '0');
echo c2c_submit_tag(__('New outing'), array('onclick' => 'if ($("#wizard_type") == "summits") $("#link").val($("#routes").val());',
                                            'title' => __('Add your outing'),
                                            'picto' => 'action_create'));
?>
</form>
</div> <!-- last_ok -->
</div> <!-- outing_wizard -->


