<?php
use_helper('Form', 'MyForm', 'Javascript');
// cotometre from BLMS (http://paleo.blms.free.fr/cotometre/cotometre.php)
echo javascript_tag('function calcul_rate(pente,longueur,skiabilite) {
  var mavar = Math.tan(3.1416 * pente/180) + 0.1 * Math.log(longueur);
  mavar = mavar + skiabilite*(mavar -1);
  return mavar;
}

function cot(pente,longueur,skiabilite) {
  var cot;
  var inter = calcul_rate(pente,longueur,skiabilite);
  if (inter <1.32) cot = "<= 3.3";
  if (inter >=1.32 & inter <1.42) cot = "4.1";
  if (inter >=1.42 & inter <1.5) cot = "4.2";
  if (inter >=1.5 & inter <1.575) cot = "4.3";
  if (inter >=1.575 & inter <1.67) cot = "5.1";
  if (inter >=1.67 & inter <1.745) cot = "5.2";
  if (inter >=1.745 & inter <1.81) cot = "5.3";
  if (inter >=1.81 & inter <1.95) cot = "5.4";
  if (inter >=1.95 & inter <2.09) cot = "5.5";
  if (inter >=2.09 & inter <2.25) cot = "5.6";
  if (inter >=2.25 & inter <2.4) cot = "5.7";
  if (inter >=2.4) cot = ">=5.8";
  return cot;
}

function compute_technical_grade() {
  var ski = parseFloat($$(\'input[name=skiabilite]:checked\').first().value);
  var pente = parseFloat($F(\'pentemoyenne\'));
  var deniv = parseFloat($F(\'denivele\'));
  if (isNaN(pente) || pente < 20.0 || pente > 80.0) {
    alert(\''.
__('pente limites').
'\');
    return false;
  }
  if (isNaN(deniv) || deniv < 50.0 || deniv > 3000.0) {
    alert(\''.
__('deniv limites').
'\');
    return false;
  }

  var cota = cot(pente, deniv, ski);
  $(\'cotometreresult\').replace(\'<span id="cotometreresult">'.
__('proposed grade').
'\'+cota+\'</span>\');
}');
?>
<div id="fake_div">
<p class="tips">
<?php echo __('cotometre tips') ?>
</p>
<div id="toolform">
<p><?php
echo label_tag('skiabilite', __('skiabilite'), false, array('class' => 'fieldname')), '<br />',
     radiobutton_tag('skiabilite', '0', true), ' ',
     label_for('skiabilite_0', __('skiabilite0')),
     '<br />',
     radiobutton_tag('skiabilite', '0.1', false), ' ',
     label_for('skiabilite_0.1', __('skiabilite01')),
     '<br />',
     radiobutton_tag('skiabilite', '0.2', false), ' ',
     label_for('skiabilite_0.2', __('skiabilite02'));
?></p><p>
<?php
echo label_tag('pentemoyenne', __('pentemoyenne'), false, array('class' => 'fieldname')),
     input_tag('pentemoyenne', '', array('class' => 'short_input'));
?></p><p>
<?php
echo label_tag('denivele', __('denivele'), false, array('class' => 'fieldname')), 
     input_tag('denivele', '', array('class' => 'short_input'));
?></p><p>
<?php
echo submit_tag(__('compute technical grade'), array('onclick' => 'compute_technical_grade(); return false;'));
?>
</p>
<span id="cotometreresult"></span>
</div>
