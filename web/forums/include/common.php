<?php
/***********************************************************************

  Copyright (C) 2002-2005  Rickard Andersson (rickard@punbb.org)

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

************************************************************************/

// Enable DEBUG mode by removing // from the following line
//define('PUN_DEBUG', 1);

// This displays all executed queries in the page footer.
// DO NOT enable this in a production environment!
//define('PUN_SHOW_QUERIES', 1);

if (!defined('PUN_ROOT'))
	exit('The constant PUN_ROOT must be defined and point to a valid PunBB installation root directory.');

// include symfony
require PUN_ROOT.'include/user/symfony_start.php';

// set form factor
require PUN_ROOT.'include/user/symfony_formfactor.php';

// Load the functions script
require PUN_ROOT.'include/functions.php';

// Reverse the effect of register_globals
if (@ini_get('register_globals'))
        unregister_globals();

@include PUN_ROOT.'config.php';

// If PUN isn't defined, config.php is missing or corrupt
if (!defined('PUN'))
	exit('The file \'config.php\' doesn\'t exist or is corrupt. Please run <a href="install.php">install.php</a> to install PunBB first.');

// Record the start time (will be used to calculate the generation time for the page)
list($usec, $sec) = explode(' ', microtime());
$pun_start = ((float)$usec + (float)$sec);

// Make sure PHP reports all errors except E_NOTICE. PunBB supports E_ALL, but a lot of scripts it may interact with, do not.
error_reporting(E_ALL ^ E_NOTICE);

// Seed the random number generator
mt_srand((double)microtime()*1000000);

// If a cookie name is not specified in config.php, we use the default (punbb_cookie)
if (empty($cookie_name))
	$cookie_name = 'punbb_cookie';

// Define a few commonly used constants
define('PUN_UNVERIFIED', 32000);
define('PUN_ADMIN', 1);
define('PUN_MOD', 2);
define('PUN_GUEST', 3);
define('PUN_MEMBER', 4);
define('PUN_ASSOCIATION', '1, 2, 5, 6, 8, 13, 15, 21, 22, 24, 26');
define('PUN_V6', '1, 2, 5, 6, 8, 13, 15, 21, 22, 24, 26, 27');
define('COMMENTS_FORUM', 1);
define('C2C_BOARD_FORUM', 34);
define('ALL_NEWS_FORUMS', '18, 85, 86, 87, 89, 48, 90, 91, 92, 93, 94, 100');
define('PUB_FORUMS', '26');
define('LOVE_FORUMS', '19');
define('PARTNER_FORUMS', '75, 76, 77, 96');
define('BUYSELL_FORUMS', '12, 13, 14, 15, 16, 17, 78');
define('ASSOCIATION_FORUMS', '3, 33, 34, 35, 36, 37, 38, 40, 53, 73, 74, 84, 95, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112');
define('V6_FORUMS', '102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112');
define('MODO_FR_FORUM', '39');
define('MODO_IT_FORUM', '53');

// Load DB abstraction layer and connect
require PUN_ROOT.'include/dblayer/common_db.php';

// Start a transaction
$db->start_transaction();

// Load cached config
@include PUN_ROOT.'cache/cache_config.php';
if (!defined('PUN_CONFIG_LOADED'))
{
	require PUN_ROOT.'include/cache.php';
	generate_config_cache();
	require PUN_ROOT.'cache/cache_config.php';
}

// Enable output buffering
if (!defined('PUN_DISABLE_BUFFERING'))
{
	// For some very odd reason, "Norton Internet Security" unsets this
	$_SERVER['HTTP_ACCEPT_ENCODING'] = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';

	// Should we use gzip output compression?
	if ($pun_config['o_gzip'] && extension_loaded('zlib') && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false || strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') !== false))
		ob_start('ob_gzhandler');
	else
		ob_start();
}

// Check/update/set cookie and fetch user info
$pun_user = array();
check_cookie($pun_user);

// Attempt to load the common language file
@include PUN_ROOT.'lang/'.$pun_user['language'].'/common.php';
if (!isset($lang_common))
	exit('There is no valid language pack \''.pun_htmlspecialchars($pun_user['language']).'\' installed. Please reinstall a language of that name.');

// Check if we are to display a maintenance message
if ($pun_config['o_maintenance'] && $pun_user['g_id'] > PUN_ADMIN && !defined('PUN_TURN_OFF_MAINT'))
	maintenance_message();

// Load cached bans
@include PUN_ROOT.'cache/cache_bans.php';
if (!defined('PUN_BANS_LOADED'))
{
	require_once PUN_ROOT.'include/cache.php';
	generate_bans_cache();
	require PUN_ROOT.'cache/cache_bans.php';
}

// Check if current user is banned
if (!isset($show_ban_message))
{
    $show_ban_message = true;
}

check_bans($show_ban_message);

// Update online list
update_users_online();
