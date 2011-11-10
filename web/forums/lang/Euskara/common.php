<?php

/*
// Determine what locale to use
switch (PHP_OS)
{
	case 'WINNT':
	case 'WIN32':
		$locale = 'euskara';
		break;

	case 'FreeBSD':
	case 'NetBSD':
	case 'OpenBSD':
		$locale = 'eu_EU.UTF-8';
		break;

	default:
		$locale = 'eu_EU';
		break;
}

// Attempt to set the locale
setlocale(LC_CTYPE, $locale);
*/

// Language definitions for frequently used strings
 
$lang_common = array(
// Text orientation and encoding
'lang_direction'		=>	'ltr',	// ltr (Left-To-Right) or rtl (Right-To-Left)
'lang_encoding'			=>	'UTF-8',
'lang_multibyte'		=>	true,
'meta_language'                 =>      'eu',
 
// Meta data in HTML header
'meta_description'      =>  'Mendi elkartea',
'meta_keywords'         =>  'foroa, mendia, mendi eskia, alpinismoa, eskalada, mendi martxa, raketak',

// Notices
'Bad request'			=>	'Akatsa. Jarraitu duzun lotura ez da zuzena, aldatu egin da edo ez duzu baimenik bertara sartzeko.',
'No view'				=>	'Foro honetara sartzeko baimenik ez duzu',
'No permission'			=>	'Orrialde hau ikusteko baimenik ez duzu.',
'Bad referrer'			=>	'HTTP_REFERER okerra. Orrialde honetara helbide debekatu edo ezezagun batetik heldu zara. Arazoak jarraitu ezkero, zihurtatu \'URL de base\' zuzena dela Admin/Options orrialdean eta foroa URL berdina erabiliz bisitatzen duzula. Informazio gehiago PunBB-ren dokumentazioan aurkitu ahal da.',
 
// Topic/forum indicators
'New icon'				=>	'Mezu berriak daude',
'Normal icon'			=>	'<!-- -->',
'Closed icon'			=>	'Eztabaida hau itxita dago',
'Redirect icon'			=>	'Foro berri batera bidalia',
 
// Miscellaneous
'Announcement'			=>	'Iragarkia',
'Options'				=>	'Aukerak',
'Actions'				=>	'Ekintzak',
'Submit'				=>	'Bidali',	// "name" of submit buttons
'Submit and topic'		=>	'Bidali - Eztabaida',
'Submit and forum'		=>	'Bidali - Foroa',
'Preview'				=>	'Aurre-ikusi',	// submit button to preview message
'Ban message'			=>	'Zure erabiltzaile kontua foro honetan ezin da gehiago erabili.',
'Ban message 2'			=>	'Hondorengo datan berriro sartu ahal izango zara',
'Ban message 3'			=>	'Kontua moztu duen administratzaile edo moderatzaileak hurrengo mezua bidali du&#160;:',
'Ban message 4'			=>	'Edozain galdera badzu, administratzailea kontaktatu',
'Ban message 5'			=>	'The forums of camptocamp.org currently facing trouble with some types of Internet connections. To remedy this, please contact the administrator at',
'Never'					=>	'Inoiz ez',
'Today'					=>	'Gaur',
'Yesterday'				=>	'Atzo',
'Info'					=>	'Info',		// a common table header
'Go back'				=>	'Itzuli',
'Maintenance'			=>	'Mantenketa',
'Redirecting'			=>	'Berbideratu',
'Click redirect'		=>	'Ez baduzu itxoin nahi (edo zure nabigatzaileak ez bazaitu zuzenean eraman) egin klik hemen.',
'on'					=>	'aktibo',		// as in "BBCode is on"
'off'					=>	'aktibatu gabe',
'Invalid e-mail'		=>	'Idatzi duzun helbide elektronikoa ez da egokia.',
'required field'		=>	'Eremua betetzea ezinbestekoa da formulario honetan.',	// for javascript form validation
'Last post'				=>	'Azken mezua',
'by'					=>	'idazlea:',	// as in last post by someuser
'New posts'				=>	'Mezu&#160;berriak',	// the link that leads to the first new post (use &#160; for spaces)
'New posts info'		=>	'Eztabaida hontako mezu berrietatik lehenengora joan.',	// the popup text for new posts links
'Username'				=>	'Erabiltzaile izena',
'Password'				=>	'Pasahitza',
'E-mail'				=>	'E-mail',
'Send e-mail'			=>	'E-mail bat bidali',
'Moderated by'			=>	'Moderatzailea',
'Registered'			=>	'Inskribitutako data',
'Subject'				=>	'Gaia',
'Message'				=>	'Mezua',
'Topic'					=>	'Eztabaida',
'topic'					=>	'eztabaida',
'Forum'					=>	'Foroa',
'forum'					=>	'foroa',
'Posts'					=>	'Mezuak',
'Replies'				=>	'Erantzunak',
'Author'				=>	'Egilea',
'Pages'					=>	'Orrialdeak',
'Help'					=>	'Laguntza',
'Reduce the text box'	=>	'Réduire la zone de texte',
'Enlarge the text box'	=>	'Agrandir la zone de texte',
'BBCode'				=>	'BBCode',	// You probably shouldn't change this
'img tag'				=>	'[img] ikurrak',
'Smilies'				=>	'Smilie-ak',
'and'					=>	'eta',
'Image link'			=>	'irudia',	// This is displayed (i.e. <image>) instead of images when "Show images" is disabled in the profile
'wrote'					=>	'-k esan du',	// For [quote]'s
'Code'					=>	'kodigoa',		// For [code]'s
'Mailer'				=>	'E-mail autmoatikoa',	// As in "MyForums Mailer" in the signature of outgoing e-mails
'Important information'	=>	'Informazio garrantzitsua',
'Write message legend'	=>	'Zure mezua idatzi eta bidali ezazu',
'all'                   =>  'guztiak',

// Title
'Title'					=>	'Izenburua',
'Member'				=>	'Kidea',	// Default title
'Moderator'				=>	'Moderatzailea',
'Administrator'			=>	'Administratzailea',
'Banned'				=>	'Debekatua',
'Guest'					=>	'Gonbidatua',

// Stuff for include/parser.php
'BBCode error'			=>	'BBCode sintaxia ez da zuzena.',
'BBCode error 1'		=>	'[/quote]-ren irekiera ***baliza*** falta da.',
'BBCode error 2'		=>	'[code]-ren itxiera ***baliza*** falta da.',
'BBCode error 3'		=>	'[/code]-ren irekiera ***baliza*** falta da.',
'BBCode error 4'		=>	'[quote]-ren itxiera ***baliza*** bat edo gehiago faltan dira.',
'BBCode error 5'		=>	'[/quote]-ren irekiera ***baliza*** bat edo gehiago falta dira.',
'Original image'		=>	'Egin klik oinarrizko itxura ikusteko.',

// Stuff for the navigator (top of every page)
'Index'					=>	'Foroen Harrera',
'User list'				=>	'Kide zerrenda',
'Rules'					=>  'Arauak',
'Search'				=>  'Bilaketa',
'Register'				=>  'Izena eman',
'Login'					=>  'Identifikatu',
'Not logged in'			=>  'Ez zaude identifikaturik.',
'Profile'				=>	'Perfila',
'Logout'				=>	'Deskonektatu',
'Logged in as'			=>	'Konektaturik zauden kontua',
'Admin'					=>	'Administrazioa',
'Last visit'			=>	'Azken bisita',
'Show new posts'		=>	'Azken bisitaz geroztik egon diren mezuak erakutsi',
'Mark all as read'		=>	'Eztabaida guztiak irakurririk dituzula adierazi',
'Mark forum as read'	=>	'Foro hau irakurri duzula adierazi',
'Link separator'		=>	'',	// The text that separates links in the navigator
'Top'				    =>	'Orrialdean gora',
'Bottom'				=>	'Orrialdean behera',

// Stuff for the page footer
'Board footer'			=>	'Foroen oin-oharra',
'Search links'			=>	'Bilaketarako loturak',
'Show recent posts'		=>	'Azken mezuak erakutsi',
'Show unanswered posts'	=>	'Erantzun gabeko mezuak erakutsi',
'Show your posts'		=>	'Zure mezuak erakutsi',
'Show subscriptions'	=>	'Harpideturik zauden eztabaidak erakutsi',
'Jump to'				=>	'Foroz aldatu',
'Go'					=>	' Joan ',		// submit button in forum jump
'Move topic'			=>  'Eztabaida mugitu',
'Open topic'			=>  'Eztabaida ireki',
'Close topic'			=>  'Eztabaida itxi',
'Unstick topic'			=>  'Eztabaida askatu',
'Stick topic'			=>  'Eztabaida elkartu',
'Moderate forum'		=>	'Foroa moderatu',
'Delete posts'			=>	'Mezu batzu ezabatu',
'Debug table'			=>	'Debog informazioa',
'Move posts'			=>	'Déplacer plusieurs messages',

// For extern.php RSS feed
'RSS Desc Active'		=>	'Azken eztabaida aktiboak',	// board_title will be appended to this string
'RSS Desc New'			=>	'Azken eztabaidak',					// board_title will be appended to this string
// Entrees pour CaptchaBox 
'captchabox post tip'		=>  'Mezua bidaltzeko egin klik irudiaren eremu ilunean.',
'captchabox reg tip'		=>  'Inskribatzeko egin klik irudiaren eremu ilunean.',
'captchabox failed'		=>  'Ez duzu Captcha azterketa gainditu. Saia ezazu berriro.',
'captchabox denied'		=>  'Captcha saiaera guztiak agortu dituzu. Saia ezazu beranduago.',
'captchabox img title'		=>  'Laukian klik egin aurrera jarraitzeko'
);
 
