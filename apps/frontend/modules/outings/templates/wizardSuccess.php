<?php
use_helper('JavascriptQueue', 'AutoComplete', 'Form', 'MyForm');
?>
<span class="article_title"><?php echo __('Outing Wizard'); ?></span>

<div id="fake_div">

<div id="outing_wizard">

<hr />
<h4><?php echo __('Step 1: choose a summit'); ?></h4>
<div id="wizard_summit">
<?php 
echo global_form_errors_tag();
echo __('Summit:') . input_tag('summits_name', '', array('size' => '35'));
echo javascript_queue("var indicator = jQuery('#indicator');
jQuery('#summits_name').c2cAutocomplete({
  url: '" . url_for('summits/autocomplete') . "',
  minChars: " . sfConfig::get('app_autocomplete_min_chars') . ",
  onSelect: function() {
    jQuery('#summit_id').val(this.id);
    jQuery('#wizard_no_route, #wizard_hints').hide();
    jQuery.get('" . url_for('summits/getroutes') . "',
               'summit_id=' + jQuery('#summit_id').val() + '&div_name=routes')
      .always(function() { indicator.hide(); })
      .done(function(data) {
        jQuery('#divRoutes').html(data);
        jQuery('#wizard_no_route').hide();
        jQuery('#summit_link, #wizard_route, #last_ok').show();
        C2C.getWizardRouteRatings('routes');
      })
      .fail(function(data) {
        jQuery('#wizard_route, #wizard_hints, #wizard_route_descr').hide();
        jQuery('#summit_link, #wizard_no_route').show();
        C2C.showFailure(data.responseText);
      })
  }
});");

echo form_tag('outings/wizard');
echo input_hidden_tag('summit_id', '0');
?>
<p id="summit_link" style="display: none">
<a href="#" onclick="window.open('/summits/' + jQuery('#summit_id').val());"><?php echo __('Show the summit') ?></a>
</p>
<p id="wizard_summit_create" class="wizard_tip"><?php echo __('No summit matching your search?') . ' ' . 
link_to(__('Add your summit'), '@document_edit?module=summits&id=&lang='); ?></p>
</form>
</div> <!-- wizard_summit -->

<div id="wizard_hints">
<hr />
<h4><?php echo __('Step 2: choose a route')  ?></h4>
<h4><?php echo __('Step 3: confirm to create outing')  ?></h4>
</div>

<div id="wizard_no_route" style="display: none">
<hr />
<h4><?php echo __('Step 2: Create a route')  ?></h4>
<p class="wizard_tip">
<a href="#" onclick="window.open('/routes/edit/link/' + jQuery('#summit_id').val());"><?php echo __('Add your route') ?></a>
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
<a href="#" onclick="window.open('/routes/' + jQuery('routes').val();">
  <?php echo __('Show the route') ?>
</a>
</p> <!-- wizard_route_descr -->
<p class="wizard_tip"><?php echo __('No route matching your search?') . ' '; ?>
<a href="#" onclick="window.location.href='/routes/edit/link/' + jQuery('#summit_id').val(); return false;"><?php echo __('Add your route') ?></a></p>

<div id="last_ok" style="display: none;">
<hr />
<h4><?php echo __('Step 3: confirm to create outing')  ?></h4>
<?php 
echo form_tag('outings/edit', 'method=get');
echo input_hidden_tag('link', '0');
echo c2c_submit_tag(__('New outing'), array('onclick' => 'jQuery("#link").val(jQuery("#routes").val());',
                                            'title' => __('Add your outing'),
                                            'picto' => 'action_create'));
?>
</form>
</div> <!-- last_ok -->
</div> <!-- wizard_route -->
</div> <!-- outing_wizard -->


