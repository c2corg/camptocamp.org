<?php

/*
// FESTLEGUNGEN, DIE LOKAL GEBRAUCHT WERDEN
switch (PHP_OS)
{
	case 'WINNT':
	case 'WIN32':
		$locale = 'german';
		break;

	case 'FreeBSD':
	case 'NetBSD':
	case 'OpenBSD':
		$locale = 'de_DE.UTF-8';
		break;

	default:
		$locale = 'de_DE';
		break;
}

// FESTLEGUNG DES LOKALEN PFADES (WIRD BENOETIGT DAMIT DIE TEXTERSCHLIESSUNG RICHTIG ARBEITET)
setlocale(LC_CTYPE, $locale);
*/

// SPRACHDEFINITIONEN FUER HAEUFIG GEBRAUCHTE ZEICHENKETTEN

$lang_common = array(
// TEXTORIENTIERUNG UND VERSCHLUESSELUNG
'lang_direction'		=>	'ltr',	// ltr (Left-To-Right) ODER rtl (Right-To-Left)
'lang_encoding'			=>	'UTF-8',
'lang_multibyte'		=>	false,
'meta_language'                 =>      'de',

// Meta data in HTML header
'meta_description'      =>  'Die Berggemeindschaft',
'meta_keywords'         =>  'Forum, Berg, Skitouren, Hochtouren, Klettern, Wandern, Schneeschuhe',

// NACHRICHTEN
'Bad request'			=>	'Ung&uuml;ltige Anfrage. Der Link dem Sie gefolgt sind ist ung&uuml;ltig oder veraltet.',
'No view'				=>	'Sie haben keine Berechtigung diese Seite zu betrachten.',
'No permission'			=>	'Sie haben keine Berechtigung f&uuml;r den Zugriff auf diese Seite.',
'Bad referrer'			=>	'Ung&uuml;ltiger HTTP_REFERER. Sie wurden von einer ung&uuml;ltigen Quelle auf dieses Forum weitergeleitet. Bitte gehen Sie zur&uuml;ck und versuchen Sie es noch einmal. Wenn dieses Problem weiter besteht kontrollieren Sie bitte die \'Base URL\' Variable unter Admin/Options und stellen Sie sicher, dass Sie dieses Forum &uuml;ber diese URL ansteuern. F&uuml;r weitere Informationen &uuml;ber den Verweis-Check entnehmen Sie bitte der PunBB-Dokumentation.',

// THEMA/FORUMSHINWEISE
'New icon'				=>	'Es gibt neue Beitr&auml;ge',
'Normal icon'			=>	'<!-- -->',
'Closed icon'			=>	'Dieses Thema ist geschlossen',
'Redirect icon'			=>	'Umadressiertes Forum',

// VERSCHIEDENES
'Announcement'			=>	'Ank&uuml;ndigung',
'Options'				=>	'Beitragsoptionen',
'Actions'				=>	'Aktionen',
'Submit'				=>	'Absenden',	// "NAME" DES SUBMIT BUTTONS
'Submit and topic'		=>	'Absenden - Thema',
'Submit and forum'		=>	'Absenden - Forum',
'Preview'				=>	'Voransicht',	// submit button to preview message
'Ban message'			=>	'Sie sind in diesem Forum gesperrt.',
'Ban message 2'			=>	'Die Sperre l&auml;uft aus am',
'Ban message 3'			=>	'Der Administrator oder Moderator, der Sie gesperrt hat, hat folgende Nachricht hinterlassen:',
'Ban message 4'			=>	'Wenn Sie Fragen haben kontaktiren Sie bitte die Administratoren unter',
'Ban message 5'			=>	'The forums of camptocamp.org currently facing trouble with some types of Internet connections. To remedy this, please contact the administrator at',
'Never'					=>	'Nie',
'Today'					=>	'Heute',
'Yesterday'				=>	'Gestern',
'Info'					=>	'Info',		// ALLGEMEINE TABELLENKOPFZEILE
'Go back'				=>	'Zur&uuml;ck',
'Maintenance'			=>	'Wartung',
'Redirecting'			=>	'Leite weiter',
'Click redirect'		=>	'Klicken Sie hier, wenn Sie nicht l&auml;nger warten wollen (oder Ihr Browser Sie nicht weiterleitet)',
'on'					=>	'an',		// ERSCHEINT WIE "BBCode ist an"
'off'					=>	'aus',
'Invalid e-mail'		=>	'Die angegebene E-Mail Adresse ist ung&uuml;ltig.',
'required field'		=>	'ist erforderlich in diesem Forumlar.',	// GUELTIGKEITSPRUEFUNG FUER JAVASCRIPT-FORMULARE
'Last post'				=>	'Letzter Beitrag',
'by'					=>	'von:',	// WIE IN "LETZTER BEITRAG VON" IRGENDEINEMBENUTZER
'New posts'				=>	'Neuer&nbsp;Beitrag',	// DER LINK DER ZUM NEUEN THEMA/BEITRAG FUEHRT (BENUTZE &nbsp; FUER LEERSTELLEN)
'New posts info'		=>	'Gehe zum ersten neuen Beitrag in diesem Thema.',	// DER POPUP TEXT FUER NEUE BEITRAGS LINKS
'Username'				=>	'Benutzername',
'Password'				=>	'Passwort',
'E-mail'				=>	'E-Mail',
'Send e-mail'			=>	'E-Mail senden',
'Moderated by'			=>	'Moderiert durch',
'Registered'			=>	'Registriert',
'Subject'				=>	'Betreff',
'Message'				=>	'Beitrag',
'Topic'					=>	'Thema',
'topic'					=>	'Thema',
'Forum'					=>	'Forum',
'forum'					=>	'Forum',
'Posts'					=>	'Beitr&auml;ge',
'Replies'				=>	'Antworten',
'Author'				=>	'Autor',
'Pages'					=>	'Seiten',
'B button help'     =>  'Fett',
'I button help'     =>  'Kursiv',
'U button help'     =>  'Unterstrichen',
'S button help'     =>  'Durchgestrichen',
'C button help'     =>  'Monospace',
'http button help'    =>  'Hyperlink',
'@ button help'     =>  'Email',
'Img button help'   =>  'Bild',
'Img wizard title' => 'Bild einfügen',
'Code button help'    =>  'Code'
'Quote button help'   =>  'Quotierung',
'Help'					=>	'Hilfe',
'Reduce the text box'	=>	'Reduce the text box',
'Enlarge the text box'	=>	'Enlarge the text box',
'BBCode'				=>	'BBCode',	// ES SOLLTE IHNEN BEKANNT SEIN DAS NICHT ZU VERAENDERN
'img tag'				=>	'[img] Tag',
'Smilies'				=>	'Smilies',
'and'					=>	'und',
'Image link'			=>	'Bild',	// This is displayed (i.e. <image>) instead of images when "Show images" is disabled in the profile
'wrote'					=>	'schrieb',	// FUER ZITATE [quote]
'Code'					=>	'Code',		// FUER CODE [code]
'Mailer'				=>	'Mailer',	// As in "MyForums Mailer" in the signature of outgoing e-mails
'Important information'	=>	'Wichtige Information',
'Write message legend'	=>	'Schreiben Sie hier Ihren Beitrag',
'all'                   =>  'alle',
'Previous'              =>  'Zurück',
'Next'                  =>  'Vorher',
'see embedded'          =>  '[zeigen]',
'close embedded'        =>  '[verstecken]',

// Title
'Title'					=>	'Titel',
'Member'				=>	'Mitglied',	// GRUNDEINSTELLUNG TITEL
'Moderator'				=>	'Moderator',
'Administrator'			=>	'Administrator',
'Banned'				=>	'Gesperrte',
'Guest'					=>	'Gast',

// Stuff for include/parser.php
'BBCode error'			=>	'Der BBCode in diesem Beitrag war falsch.',
'BBCode error 1'		=>	'Fehlender Start f&uuml;r [/quote].',
'BBCode error 2'		=>	'Fehlendes Ende f&uuml;r [code].',
'BBCode error 3'		=>	'Fehlender Start f&uuml;r [/code].',
'BBCode error 4'		=>	'Ein oder mehrere fehlende Enden f&uuml;r [quote].',
'BBCode error 5'		=>	'Ein oder mehrere fehlende Anf&auml;nge f&uuml;r [/quote].',
'Original image'		=>	'Klicken Sie hier, um das urspr&uuml;ngliche Format.',

// LINKS DIE SICH IN DER NAVIGATION BEFINDEN (OBEN AUF JEDER SEITE)
'Index'					=>	'Startseite',
'User list'				=>	'Mitgliederliste',
'Rules'					=>  'Forumregeln',
'Search'				=>  'Suche',
'Register'				=>  'Registrieren',
'Login'					=>  'Anmelden',
'Not logged in'			=>  'Sie sind nicht angemeldet.',
'Profile'				=>	'Benutzerprofil',
'Logout'				=>	'Abmelden',
'Logged in as'			=>	'Angemeldet als:',
'Admin'		            =>	'Adminverwaltung',
'Last visit'			=>	'Ihr letzter Besuch war',
'Show new posts'		=>	'Zeige Beitr&auml;ge seit dem letzten Besuch',
'Mark all as read'		=>	'Alle Foren als gelesen markieren',
'Mark forum as read'	=>	'Dieses Forum als gelesen markieren',
'Link separator'		=>	'',	// TEXT FUER SEPARATE LINKS IN DER NAVIGATION
'Top'				    =>	'Haut de page',
'Bottom'				=>	'Bas de page',

// LINKS DIE SICH IN DER FUSSZEILE BEFINDEN
'Board footer'			=>	'Brett Fu&szlig;zeile',
'Search links'			=>	'Such Links',
'Show recent posts'		=>	'K&uuml;rzlich geschriebene Beitr&auml;ge anzeigen',
'Show unanswered posts'	=>	'Zeige unbeantwortete Beitr&auml;ge',
'Show your posts'		=>	'Zeige meine Beitr&auml;ge',
'Show your topics'		=>	'Zeige meine Themen',
'Show subscriptions'	=>	'Zeige abonnierte Themen',
'Jump to'				=>	'Wechsel zu',
'Go'					=>	' Los ',		// DER TASTER IN FORUM JUMP
'Move topic'			=>  'Thema verschieben',
'Open topic'			=>  'Thema &ouml;ffnen',
'Close topic'			=>  'Thema schlie&szlig;en',
'Unstick topic'			=>  'Thema l&ouml;sen',
'Stick topic'			=>  'Thema fixieren',
'Moderate forum'		=>	'Forum moderieren',
'Delete posts'			=>	'Mehrere Beitr&auml;ge l&ouml;schen',
'Debug table'			=>	'Debug Information',
'Move posts'			=>	'Déplacer plusieurs messages',


// For extern.php RSS feed
'RSS Desc Active'		=>	'Das zuletzt aktive Thema aus:',	// board_title will be appended to this string
'RSS Desc New'			=>	'Das neueste Thema aus:',					// board_title will be appended to this string
// CaptchaBox entries
'captchabox post tip'		=>  'Click in the dark area of the image to send your post.',
'captchabox reg tip'		=>  'Click in the dark area of the image to register.',
'captchabox failed'		=>  'You failed the Captcha test. Try again.',
'captchabox denied'		=>  'You have used all your tries for the Captcha. Try again later.',
'captchabox img title'		=>  'Click the box for next step'
);

