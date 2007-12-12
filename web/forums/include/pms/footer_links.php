<?php
if ($footer_style == 'message_list')
{
	if (!isset($_GET['box']))
		$_GET['box'] = 0;
	if (!isset($_GET['p']))
		$_GET['p'] = 0;
?>
			<dl id="searchlinks" class="conl">
				<dt><strong>PM links</strong></dt>
<?php
if ($new_messages)
	echo "\t\t\t\t\t\t".'<dd><a href="message_list.php?action=markall&amp;box='.$_GET['box'].'&amp;p='.$_GET['p'].'">'.$lang_pms['Mark all'].'</a></dd>'."\n";
if ($messages_exist)
	echo "\t\t\t\t\t\t".'<dd><a href="message_list.php?action=multidelete&amp;box='.$_GET['box'].'&amp;p='.$_GET['p'].'">'.$lang_pms['Multidelete'].'</a></dd>'."\n";
?>
			</dl>
<?php
}
