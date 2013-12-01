<?php
use_helper('Form', 'MyForm', 'Javascript');
// cotometre from BLMS (http://paleo.blms.free.fr/cotometre/cotometre.php)
echo javascript_tag('
function cotometre_rating(slope, height, skiability) {
  var inter = Math.tan(Math.PI * slope/180) + 0.1 * Math.log(height);
  inter += skiability * (inter - 1);
  
  if (inter <1.32) return "≤3.3";
  if (inter >=1.32 & inter <1.42) return "4.1";
  if (inter >=1.42 & inter <1.5) return "4.2";
  if (inter >=1.5 & inter <1.575) return "4.3";
  if (inter >=1.575 & inter <1.67) return "5.1";
  if (inter >=1.67 & inter <1.745) return "5.2";
  if (inter >=1.745 & inter <1.81) return "5.3";
  if (inter >=1.81 & inter <1.95) return "5.4";
  if (inter >=1.95 & inter <2.09) return "5.5";
  if (inter >=2.09 & inter <2.25) return "5.6";
  if (inter >=2.25 & inter <2.4) return "5.7";
  return "≥5.8";
}

function cotometre_technical_grade() {
  var skiability = parseFloat($("input[name=skiability]:checked").first().val());
  var slope = parseFloat($("#slope").val());
  var height = parseFloat($("#height").val());
  
  if (isNaN(slope) || slope < 20.0 || slope > 80.0) {
    alert("' . __('pente limites') . '");
    return false;
  }
  
  if (isNaN(height) || height < 50.0 || height > 3000.0) {
    alert("' . __('deniv limites') . '");
    return false;
  }

  var rating = cotometre_rating(slope, height, skiability);
  
  $("#cotometre_result").html(\'<div id="cotometre_result">' . __('proposed grade') . '<br /><span class="cotometre_result">\' + rating + " </span></div>");
}');
?>
<div id="fake_div">
<p class="tips">
<?php echo __('cotometre tips') ?>
</p>
<div id="toolform" class="cotometre_form">
<p><?php
echo label_tag('skiability', __('skiabilite'), false, array('class' => 'fieldname')), '<br />',
     radiobutton_tag('skiability', '0', true), ' ',
     label_for('skiability_0', __('skiabilite0')),
     '<br />',
     radiobutton_tag('skiability', '0.1', false), ' ',
     label_for('skiability_0.1', __('skiabilite01')),
     '<br />',
     radiobutton_tag('skiability', '0.2', false), ' ',
     label_for('skiability_0.2', __('skiabilite02'));
?></p><p>
<?php
echo label_tag('slope', __('pentemoyenne'), false, array('class' => 'fieldname')),
     input_tag('slope', '', array('class' => 'short_input'));
?></p><p>
<?php
echo label_tag('height', __('denivele'), false, array('class' => 'fieldname')), 
     input_tag('height', '', array('class' => 'short_input'));
?></p><p>
<?php
echo c2c_submit_tag(__('compute technical grade'), array('onclick' => 'cotometre_technical_grade(); return false;'));
?>
</p>
</div>
<div id="cotometre_result"><span class="cotometre_result"> </span></div>
