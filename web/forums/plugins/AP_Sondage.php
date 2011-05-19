<?php
/***********************************************************************

  Caleb Champlin (med_mediator@hotmail.com)

************************************************************************/

// Tell admin_loader.php that this is indeed a plugin and that it is loaded
define('PUN_PLUGIN_LOADED', 1);


// Did someone just hit "Submit"
if (isset($_POST['form_sent']))
{
	$options = intval(pun_trim($_POST['max_options']));
	$db->query('UPDATE '.$db->prefix.'config SET conf_value='.$options.' WHERE conf_name=\'poll_max_fields\'') or error('Unable to update configuration', __FILE__, __LINE__, $db->error());
	$d = dir(PUN_ROOT.'cache');
	while (($entry = $d->read()) !== false)
	{
		if (substr($entry, strlen($entry)-4) == '.php')
			@unlink(PUN_ROOT.'cache/'.$entry);
	}
	redirect('admin_loader.php?plugin=AP_Sondage.php', 'Options des sondages mises à jour.');
}
else if (isset($_POST['save']))
{
	// Permission Updating Code here
}
else	// If not, we show the "Show text" form
{
	// Display the admin navigation menu
	generate_admin_menu($plugin);

?>
	<div id="pollplugin" class="blockform">
		<h2><span>Easy Poll +</span></h2>
		<div class="box">
			<div class="inbox">
				<p>Ce plugin permet de configurer le système de sondage suivant vos besoins.</p>
			</div>
		</div>
		<h2 class="block2"><span>Options</span></h2>
		<div class="box">
		<form id="post" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
				<div class="inform">
					<fieldset>
						<legend>General</legend>
						<div class="infldset">
						<table class="aligntop">
							<tr>
								<th scope="row">Nombre d'options<div><input type="submit" name="form_sent" value="Enregistrer" tabindex="2" /></div></th>
								<td>
									<input type="text" name="max_options" size="4" value="<?php echo $pun_config['poll_max_fields'] ?>" tabindex="1" />
									<span>Nombre d'options maximum  pour un nouveau sondage.</span>
								</td>
							</tr>
						</table>
						</div>
					</fieldset>
				</div>
			</form>
		</div> 		
</div>
<?php

}

// Note that the script just ends here. The footer will be included by admin_loader.php.
