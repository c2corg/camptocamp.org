<?php
/***********************************************************************

  Copyright (C) 2006  Bruno Laplace (blaplace@free.fr)

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

// Telquel ce fichier est conçu pour fonctionner avec PunBB et sans y triturer maladroitement le code.
// Ceci est la raison de la présence de l'entête ci-dessus, même s'il ne s'agit pas réellement d'un plugin.
// Juste une astuce, même un gadget pour m'amuser avec du javascript. ( Testé sur FF 2.0 et IE 6 )

if (!defined('PUN'))
	exit;
?>
<script type="text/javascript">
var pun_static_url = '<?php echo PUN_STATIC_URL; ?>';
</script>
<script type="text/javascript" src="<?php echo PUN_STATIC_URL; ?>/forums/js/dyncat/cookie.js"></script>
<script type="text/javascript" src="<?php echo PUN_STATIC_URL; ?>/forums/js/dyncat/common.js"></script>
