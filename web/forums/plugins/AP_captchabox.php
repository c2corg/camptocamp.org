<?php
/***********************************************************************
  Author: Mathias Michel
  Date: 2006-05-22
  Description: Plugin to add a captcha for either guest posting or 
    registering, or both.
    Derived from a work of Kai Blankenhorn, botproof email v3.1

  Copyright (C) 2006  Mathias Michel (mmichel@chez.com)
  
************************************************************************
  This file is part of PunBB.

  PunBB is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  PunBB is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
  MA  02111-1307  USA

************************************************************************
CHANGELOG:
2006-05-22 : corrected descriptive strings.
2006-05-09 : initial release 
************************************************************************/


// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;

// Tell admin_loader.php that this is indeed a plugin and that it is loaded
define('PUN_PLUGIN_LOADED', 1);

//
// The rest is up to you!
//

// If the "Save changes" button was clicked
if (isset($_POST['set_captchabox_options']))
{
  if ($_POST['regs_captchabox'] != $pun_config['o_regs_captchabox'] ) {
    $query= 'UPDATE '.$db->prefix."config SET conf_value='".$_POST['regs_captchabox']."' 
          WHERE conf_name='o_regs_captchabox'";
  	$db->query($query) or error('Unable to update board config reg',__FILE__, __LINE__, $db->error());
  			$updated=true;
  };
  
  if ($_POST['guest_post_captchabox'] != $pun_config['o_guest_post_captchabox'] ) {
  	$query = 'UPDATE '.$db->prefix."config SET conf_value='".$_POST['guest_post_captchabox']."'  
          WHERE conf_name='o_guest_post_captchabox'";
    $db->query($query) or error('Unable to update board config post '. print_r($db->error()),__FILE__, __LINE__, $db->error());
          $updated=true;
  }

  if ($updated) { 
  	// Regenerate the config cache
  	require_once PUN_ROOT.'include/cache.php';
  	generate_config_cache();
  	redirect($_SERVER['REQUEST_URI'], 'CaptchaBox Options updated. Redirecting &hellip;');
  }

};			
	// Display the admin navigation menu
	generate_admin_menu($plugin);

?>
	<div id="captchabox" class="blockform">
		<h2><span>CaptchaBox plugin</span></h2>
		<div class="box">
    <div class="inbox">
				<p>This plugin is used to replace submit buttons by image buttons.</p>
				<p>This normally would eradicate most of spam on your forums.</p>
		</div>
		</div>
		
    <h2 class="block2"><span>Where to use Captcha Box</span></h2>
		<div class="box">
	  <form id="example" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>&amp;cfg=captcha">
		  <fieldset>
      	<div class="infldset">
						<table class="aligntop">
							<tr>
								<th scope="row">For guest posts</th>
								<td>
									<input type="radio" name="guest_post_captchabox" value="1" 
									<?php if ($pun_config['o_guest_post_captchabox'] == '1') echo ' checked="checked"' ?>  tabindex="1"/>
                  &nbsp;<strong>Yes</strong>&nbsp;&nbsp;&nbsp;
                  <input type="radio" name="guest_post_captchabox" value="0"
                  <?php if ($pun_config['o_guest_post_captchabox'] == '0') echo 'checked="checked"' ?>  tabindex="2"/>
                  &nbsp;<strong>No</strong>
		              <span>When enabled, guest users (unsigned) should click in a specific area of an image in order to post.
                  This is an effective way of avoiding bot spams.</span>
								</td>
							</tr>
							<tr>
								<th scope="row">For new registrations</th>
								<td>
									<input type="radio" name="regs_captchabox" value="1" 
									<?php if ($pun_config['o_regs_captchabox'] == '1') echo ' checked="checked"' ?>  tabindex="3"/>
                  &nbsp;<strong>Yes</strong>&nbsp;&nbsp;&nbsp;
                  <input type="radio" name="regs_captchabox" value="0"
                  <?php if ($pun_config['o_regs_captchabox'] == '0') echo 'checked="checked"' ?>  tabindex="4"/>
                  &nbsp;<strong>No</strong>
		              <span>When enabled, guest users should click in a specific area of an image in order to register.
                  This is an effective way of avoiding spam bots to register.</span>								</td>
							</tr>
						</table>
						<p></p>
						<div><input type="submit" name="set_captchabox_options" value="Save changes" tabindex="5" />
						</div>
				</div>
				</fieldset>
			</form>
		</div>
	</div>
<?php


// Note that the script just ends here. The footer will be included by admin_loader.php.
