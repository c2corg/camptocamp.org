<?php
use_helper('Form', 'MyForm', 'Javascript');

$response = sfContext::getInstance()->getResponse()->addMeta('robots', 'noindex, nofollow');

echo tips_tag('widget generator help');

$parameters = $sf_request->getParameterHolder()->getAll();

$paramstring = '';
foreach ($parameters as $param => $value)
{
    if ($param != 'mod' && $param != 'module' && $param != 'action')
    {
        $paramstring .= '/' . $param . '/' . $value;
    }
}
?>
<form action="#">
<p>
<?php echo __('Widget title') . ' ' . input_tag('wgt_title', 'camptocamp.org'); ?>
</p>
<p>
<?php echo __('Widget code') . '<br />' . textarea_tag('wgt_code', '',
                                                array('rows' => 12, 'cols' => 80, 'onfocus' => 'select();')); ?>
</p>
</form>
<script type="text/javascript">
function updateWidgetCode() {
  var code = "&lt;div id=\"c2cwidget\" class=\"c2cwgt\"&gt;&lt;/div&gt;\n";
  code += "&lt;script src=\"http://www.camptocamp.org/static/js/widget.js\" type=\"text/javascript\"&gt;&lt;/script&gt;\n";
  code += "&lt;script type=\"text/javascript\"&gt;\n";
  code += "showC2CWidget({\n";
  code += "  div : \"c2cwidget\",\n";
  code += "  title : \"" + $('wgt_title').value + "\",\n";
  code += "  module : \"<?php echo $sf_request->getParameter('mod'); ?>\",\n";
  code += "  params : \"<?php echo $paramstring; ?>\"\n";
  code += "});\n";
  code += "&lt;/script&gt;";
  $('wgt_code').setValue(code.unescapeHTML());
}
$('wgt_title').observe('keyup', function(event) {
  updateWidgetCode();
});
updateWidgetCode();
</script>
<ul class="action_buttons">
  <li><?php echo button_tag('Close', __('Close'), array('onclick' => 'Modalbox.hide();',
                                                        'class' => 'picto action_cancel',
                                                        'title' => null)); ?></li>
</ul>