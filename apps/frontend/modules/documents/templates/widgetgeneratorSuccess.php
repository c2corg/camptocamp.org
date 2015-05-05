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
(function() {
  
  function updateWidgetCode() {
    var code = "&lt;div id=\"c2cwidget\" /&gt;\n";
    code += "&lt;script type=\"text/javascript\"&gt;\n";
    code += "(function(d, t, w) {\n";
    code += "  w.c2cwgt = w.c2cwgt || {};\n";
    code += "  w.c2cwgt.params = {\n";
    code += "    div : \"c2cwidget\",\n",
    code += "    title : \"" + $('#wgt_title').val() + "\",\n";
    code += "    module : \"<?php echo $sf_request->getParameter('mod'); ?>\",\n";
    code += "    params : \"<?php echo $paramstring; ?>\"\n";
    code += "  };\n";
    code += "  var js = d.createElement(t), fjs = d.getElementsByTagName(t)[0];\n";
    code += "  js.async = 1; js.src = 'https://s.camptocamp.org/static/js/widget.js';\n";
    code += "  fjs.parentNode.insertBefore(js, fjs);\n"
    code += "} (document, 'script', window));\n";
    code += "&lt;/script&gt;";

    $('#wgt_code').val($('textarea').html(code).text());
  }

  $('#wgt_title').keyup(updateWidgetCode);
  updateWidgetCode();
})();
</script>
<ul class="action_buttons">
  <li><?php echo button_tag('Close', __('Close'), array('onclick' => '$.modalbox.hide();',
                                                        'class' => 'picto action_cancel',
                                                        'title' => null)); ?></li>
</ul>
