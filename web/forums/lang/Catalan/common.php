<?php

/*
// Determine what locale to use
switch (PHP_OS)
{
	case 'WINNT':
	case 'WIN32':
		$locale = 'catalan';
		break;

	case 'FreeBSD':
	case 'NetBSD':
	case 'OpenBSD':
		$locale = 'ca_ES.UTF-8';
		break;

	default:
		$locale = 'ca_ES';
		break;
}

// Attempt to set the locale (required for fulltext indexing to work correctly)
setlocale(LC_CTYPE, $locale);
*/

// Language definitions for frequently used strings

$lang_common = array(
// Text orientation and encoding
'lang_direction'		=>	'ltr',	// ltr (Left-To-Right) or rtl (Right-To-Left)
'lang_encoding'			=>	'UTF-8',
'lang_multibyte'		=>	false,
'meta_language'                 =>      'ca',

// Meta data in HTML header
'meta_description'      =>  'La comunitat de muntanya',
'meta_keywords'         =>  'fòrum, muntanya, esquí, alpinisme, escalada, excursionisme, raquetes',

// Notices
'Bad request'			=>	'Sol·licitud errònia. L\'enllaç seguit és incorrecte o ha caducat.',
'No view'				=>	'No teniu permís per veure aquests fòrums.',
'No permission'			=>	'No teniu permís per accedir a aquesta pàgina.',
'Bad referrer'			=>	'HTTP_REFERER erroni. Heu estat dirigit a aquesta pàgina des d\'una font no autoritzada. Si el problema continua, si us plau assegureu-vos que la \'URL base\' està correctament configurada a Admin/Options i que esteu visitant el fòrum a partir d\'aquesta URL. Podeu trobar més informació al voltant d\'aquest tema a la documentació de PunBB.',

// Topic/forum indicators
'New icon'				=>	'Hi ha missatges nous',
'Normal icon'			=>	'<!-- -->',
'Closed icon'			=>	'Aquest tema està tancat',
'Redirect icon'			=>	'Fòrum redirigit',

// Miscellaneous
'Announcement'			=>	'Avís',
'Options'				=>	'Opcions',
'Actions'				=>	'Accions',
'Submit'				=>	'Envia',	// "name" of submit buttons
'Submit and topic'		=>	'Envia - Tema',
'Submit and forum'		=>	'Envia - Fòrum',
'Preview'				=>	'Previsualitza',	// submit button to preview message
'Ban message'			=>	'Heu estat expulsat d\'aquest fòrum.',
'Ban message 2'			=>	'L\'expulsió expira a la fi de',
'Ban message 3'			=>	'L\'administrador o moderador que us ha expulsat ha deixat el següent missatge:',
'Ban message 4'			=>	'Si us plau, adreceu qualsevol pregunta a l\'administrador del fòrum a',
'Ban message 5'			=>	'Els fòrums de camptocamp.org estan experimentant problemes amb alguns tipus de connexions d\'Internet. Per posar-hi remei, contacteu si us plau l\'administrador a',
'Never'					=>	'Mai',
'Today'					=>	'Avui',
'Yesterday'				=>	'Ahir',
'Info'					=>	'Info',		// a common table header
'Go back'				=>	'Torna enrere',
'Maintenance'			=>	'Manteniment',
'Redirecting'			=>	'Redirigint',
'Click redirect'		=>	'Premeu aquí si no voleu esperar-vos més (o si el vostre explorador no us reenvia automàticament)',
'on'					=>	'actiu',		// as in "BBCode is on"
'off'					=>	'inactiu',
'Invalid e-mail'		=>	'L\'adreça de correu que heu proporcionat no és vàlida.',
'required field'		=>	'és un camp requerit en aquest formulari.',	// for javascript form validation
'Last post'				=>	'Últim missatge',
'by'					=>	'per',	// as in last post by someuser
'New posts'				=>	'Missatges&nbsp;nous',	// the link that leads to the first new post (use &nbsp; for spaces)
'New posts info'		=>	'Vés al primer missatge nou d\'aquest tema.',	// the popup text for new posts links
'Username'				=>	'Nom d\'Usuari',
'Password'				=>	'Contrasenya',
'E-mail'				=>	'E-mail',
'Send e-mail'			=>	'Envia e-mail',
'Moderated by'			=>	'Moderat per',
'Registered'			=>	'Registrat',
'Subject'				=>	'Assumpte',
'Message'				=>	'Missatge',
'Topic'					=>	'Tema',
'topic'					=>	'tema',
'Forum'					=>	'Fòrum',
'forum'					=>	'fòrum',
'Posts'					=>	'Missatges',
'Replies'				=>	'Respostes',
'Author'				=>	'Autor',
'Pages'					=>	'Pàgines',
'B button help'     =>  'fica en negreta el text seleccionat',
'I button help'     =>  'fica en cursiva el text seleccionat',
'U button help'     =>  'subratlla el text seleccionat',
'S button help'     =>  'tatxa el text seleccionat',
'C button help'     =>  'lletres a amplada fixa',
'http button help'    =>  'inserir un enllaç en el text seleccionat o activar l\'enllaç seleccionat',
'@ button help'     =>  'inserir un email en el text seleccionat o activar l\'email seleccionat',
'Img button help'   =>  'inserir una imatge a partir de l\'enllaç cap a una imatge d\'internet',
'Img wizard title' => 'Inserir una imatge',
'Code button help'    =>  'ficar el text seleccionat en un marc a caràcters d\'amplada fixa',
'Quote button help'   =>  'ficar el text seleccionat en un marc de citació',
'Help'					=>	'Ajuda',
'Reduce the text box'	=>	'Redueix la zona de text',
'Enlarge the text box'	=>	'Engrandeix la zona de text',
'BBCode'				=>	'BBCode',	// You probably shouldn't change this
'img tag'				=>	'Marcador [img]',
'Smilies'				=>	'Smilies',
'and'					=>	'i',
'Image link'			=>	'imatge',	// This is displayed (i.e. <image>) instead of images when "Show images" is disabled in the profile
'wrote'					=>	'escrigué',	// For [quote]'s
'Code'					=>	'Codi',		// For [code]'s
'Mailer'				=>	'Administrador de correu',	// As in "MyForums Mailer" in the signature of outgoing e-mails
'Important information'	=>	'Informació important',
'Write message legend'	=>	'Escriviu el vostre missatge i envieu-lo',
'all'                   =>  'tots',

// Title
'Title'					=>	'Títol',
'Member'				=>	'Membre',	// Default title
'Moderator'				=>	'Moderador',
'Administrator'			=>	'Administrador',
'Banned'				=>	'Expulsat',
'Guest'					=>	'Visitant',

// Stuff for include/parser.php
'BBCode error'			=>	'La sintaxi del BBCode en aquest missatge és errònia.',
'BBCode error 1'		=>	'Falta el marcador d\'inici per a [/quote].',
'BBCode error 2'		=>	'Falta el marcador de fi per a [code].',
'BBCode error 3'		=>	'Falta el marcador d\'inici per a [/code].',
'BBCode error 4'		=>	'Falta un o més marcadors de fi per a [quote].',
'BBCode error 5'		=>	'Falta un o més marcadors d\'inici per a [/quote].',
'Original image'		=>	'Cliqueu aquí per veure el format original.',

// Stuff for the navigator (top of every page)
'Index'					=>	'Inici',
'User list'				=>	'Llista d\'usuaris',
'Rules'					=>  'Regles',
'Search'				=>  'Cerca',
'Register'				=>  'Registre',
'Login'					=>  'Entreu',
'Not logged in'			=>  'No esteu identificat.',
'Profile'				=>	'Perfil',
'Logout'				=>	'Sortiu',
'Logged in as'			=>	'Identificat com',
'Admin'					=>	'Administració',
'Last visit'			=>	'Última visita',
'Show new posts'		=>	'Mostra missatges nous des de l\'última visita',
'Mark all as read'		=>	'Marca tots els temes com a llegits',
'Mark forum as read'	=>	'Marca el fòrum com a llegit',
'Link separator'		=>	'',	// The text that separates links in the navigator
'Top'				    =>	'Amunt',
'Bottom'				=>	'Avall',

// Stuff for the page footer
'Board footer'			=>	'Peu del fòrum',
'Search links'			=>	'Cerca enllaços',
'Show recent posts'		=>	'Mostra missatges recents',
'Show unanswered posts'	=>	'Mostra missatges sense resposta',
'Show your posts'		=>	'Mostra els meus missatges',
'Show your topics'		=>	'Mostra els meus temes',
'Show subscriptions'	=>	'Mostra els meus temes subscrits',
'Jump to'				=>	'Vés a',
'Go'					=>	' Vés-hi ',		// submit button in forum jump
'Move topic'			=>  'Mou tema',
'Open topic'			=>  'Obre tema',
'Close topic'			=>  'Tanca tema',
'Unstick topic'			=>  'Desmarca permanent',
'Stick topic'			=>  'Marca com a permanent',
'Moderate forum'		=>	'Modereu el fòrum',
'Delete posts'			=>	'Esborra missatges múltiples',
'Debug table'			=>	'Informació de depuració',
'Move posts'			=>	'Desplaça múltiples missatges',


// For extern.php RSS feed
'RSS Desc Active'		=>	'Últims temes actius a',	// board_title will be appended to this string
'RSS Desc New'			=>	'Últims temes a',	// board_title will be appended to this string
// CaptchaBox entries
'captchabox post tip'		=>  'Cliqueu a l\'àrea fosca de la imatge per enviar el vostre missatge.',
'captchabox reg tip'		=>  'Cliqueu a l\'àrea fosca de la imatge per registrar-ho.',
'captchabox failed'		=>  'No heu passat el test Captcha. Torneu-ho a provar.',
'captchabox denied'		=>  'Heu esgotat el número d\'intents del test Captcha. Proveu-ho de nou més tard.',
'captchabox img title'		=>  'Cliqueu la casella per al següent pas'
);
