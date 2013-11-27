<?php

/*
// Determine what locale to use
switch (PHP_OS)
{
	case 'WINNT':
	case 'WIN32':
		$locale = 'italian';
		break;

	case 'FreeBSD':
	case 'NetBSD':
	case 'OpenBSD':
		$locale = 'it_IT.UTF-8';
		break;

	default:
		$locale = 'it_IT';
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
'lang_multibyte'		=>	false,
'meta_language'                 =>      'it',

// Meta data in HTML header
'meta_description'      =>  'la comunità della montagna',
'meta_keywords'         =>  'forum, montagna, neve, alpinismo, arrampicata, escursionismo, trekking, ciaspole, scialpinismo, sci-alpinismo, sci, skirando, escursionismo, racchette, racchette da neve, rifugi, GPS, immagini, foto, svizzera, alpi, pirenei, appennino, neve, ghiaccio, misto, goulotte, couloir, cascata, roccia, falesia',

// Notices
'Bad request'			=>	'Richiesta non valida. Il link che hai seguito non &egrave; valido oppure &egrave; scaduto.',
'No view'				=>	'Non hai il permesso di visualizzare questo forum.',
'No permission'			=>	'Non hai il permesso di accere a questa pagina.',
'Bad referrer'			=>	'HTTP_REFERER non valido. Sei stato indirizzato a questa pagina da una fonte non autorizzata. Se il problema persiste per favore assicurati che l\'indirizzo di base\' sia correttamente impostato nelle Amministrazione/Opzioni e che tu stia navigando nel forum con quell\'URL. Altre informazioni al riguardo possono essere reperite nella documentazione di PunBB.',

// Topic/forum indicators
'New icon'				=>	'Ci sono nuovi messaggi',
'Normal icon'			=>	'<!-- -->',
'Closed icon'			=>	'Questa discussione &egrave; chiusa',
'Redirect icon'			=>	'Forum reindirizzato',

// Miscellaneous
'Announcement'			=>	'Annuncio',
'Options'				=>	'Opzioni',
'Actions'				=>	'Azioni',
'Submit'				=>	'Invia',	// "name" of submit buttons
'Submit and topic'		=>	'Invia e tornare alla la discussione',
'Submit and forum'		=>	'Invia e tornare alla lista',
'Preview'				=>	'Anteprima',	// submit button to preview message
'Ban message'			=>	'Sei interdetto da questo forum.',
'Ban message 2'			=>	'L\'interdizione scade alla fine di',
'Ban message 3'			=>	'L\'amministratore o il moderatore che ti hanno interdetto ha lasciato il seguente messaggio:',
'Ban message 4'			=>	'Per favore inoltra ogni informazione all\'amministratore a',
'Ban message 5'			=>	'The forums of camptocamp.org currently facing trouble with some types of Internet connections. To remedy this, please contact the administrator at',
'Never'					=>	'Mai',
'Today'					=>	'Oggi',
'Yesterday'				=>	'Ieri',
'Info'					=>	'Info',		// a common table header
'Go back'				=>	'Torna indietro',
'Maintenance'			=>	'Manutenzione',
'Redirecting'			=>	'Reindirizzamento',
'Click redirect'		=>	'Clicca qui se non vuoi pi&ugrave; aspettare (o se il tuo browser non ti indirizza automaticamente)',
'on'					=>	'attivato',		// as in "BBCode is on"
'off'					=>	'disattivato',
'Invalid e-mail'		=>	'L\'indirizzo e-mail che hai inserito non &egrave; valido.',
'required field'		=>	'&egrave; richiesto in questo forum.',	// for javascript form validation
'Last post'				=>	'Ultimo messaggio',
'by'					=>	'di',	// as in last post by someuser
'New posts'				=>	'Nuovi',	// the link that leads to the first new post (use &nbsp; for spaces)
'New posts info'		=>	'Vai al primo nuovo messaggio di questa discussione.',	// the popup text for new posts links
'Username'				=>	'Nome utente',
'Password'				=>	'Password',
'E-mail'				=>	'E-mail',
'Send e-mail'			=>	'Invia e-mail',
'Moderated by'			=>	'Moderato da',
'Registered'			=>	'Registrato',
'Subject'				=>	'Oggetto',
'Message'				=>	'Messaggio',
'Topic'					=>	'Argomento',
'topic'					=>	'argomento',
'Forum'					=>	'Categoria',
'forum'					=>	'categoria',
'Posts'					=>	'Messaggi',
'Replies'				=>	'Risposte',
'Author'				=>	'Autore',
'Pages'					=>	'Pagine',
'B button help'     =>  'formatare il testo selezionato per renderlo in grassetto',
'I button help'     =>  'formatare il testo per renderlo corsivo',
'U button help'     =>  'per sottolineare il testo',
'S button help'     =>  'per barrare il testo',
'C button help'     =>  'monospace',
'http button help'    =>  'inserire un link sul testo selezionato o attivare l\'url selezionato',
'@ button help'     =>  'inserire un indirizzo e-mail sul testo selezionato o attivare l\'indirizzo selezionato',
'Img button help'   =>  'inserire un\' imaggine accessibile sul net',
'Img wizard title' => 'Inserire un\' imaggine',
'Code button help'    =>  'codice',
'Quote button help'   =>  'citazione',
'Help'					=>	'Aiuto',
'Reduce the text box'	=>	'Ridurre questo blocco',
'Enlarge the text box'	=>	'Ingrandire questo blocco',
'BBCode'				=>	'BBCode',	// You probably shouldn't change this
'img tag'				=>	'tag [img]',
'Smilies'				=>	'Faccine',
'and'					=>	'e',
'Image link'			=>	'immagine',	// This is displayed (i.e. <image>) instead of images when "Show images" is disabled in the profile
'wrote'					=>	'ha scritto',	// For [quote]'s
'Code'					=>	'Codice',		// For [code]'s
'Mailer'				=>	'Mailer',	// As in "MyForums Mailer" in the signature of outgoing e-mails
'Important information'	=>	'Informazione importante',
'Write message legend'	=>	'Scrivi il tuo messaggio ed invia',
'all'                   =>  'tutte',

// Title
'Title'					=>	'Titolo',
'Member'				=>	'Membro',	// Default title
'Moderator'				=>	'Moderatore',
'Administrator'			=>	'Amministratore',
'Banned'				=>	'Interdetto',
'Guest'					=>	'Ospite',

// Stuff for include/parser.php
'BBCode error'			=>	'La sintassi BBCode nel messaggio non &egrave; valida.',
'BBCode error 1'		=>	'Tag d\'inizio di [/quote] omesso.',
'BBCode error 2'		=>	'Tag di chiusura di [code] omesso.',
'BBCode error 3'		=>	'Tag d\'inizio di [/code] omesso.',
'BBCode error 4'		=>	'Uno o pi&ugrave; tag di chiusura di [quote] omessi.',
'BBCode error 5'		=>	'Uno o pi&ugrave; tag d\'inizio di [/quote] omessi.',
'Original image'		=>	'Clicca qui per visualizzare il formato originale.',

// Stuff for the navigator (top of every page)
'Index'					=>	'Indice',
'User list'				=>	'Lista utenti',
'Rules'					=>  'Regolamento',
'Search'				=>  'Ricerca',
'Register'				=>  'Registrati',
'Login'					=>  'Accedi',
'Not logged in'			=>  'Non hai eseguito l\'accesso.',
'Profile'				=>	'Profilo',
'Logout'				=>	'Uscita',
'Logged in as'			=>	'Connesso come',
'Admin'					=>	'Amministrazione',
'Last visit'			=>	'Ultima visita',
'Show new posts'		=>	'Nuovi messaggi dall\'ultima visita',
'Mark all as read'		=>	'Segna tutte le discussioni come lette',
'Mark forum as read'	=>	'Segna questo forum come lette',
'Link separator'		=>	'',	// The text that separates links in the navigator
'Top'				    =>	'Alto della pagina',
'Bottom'				=>	'Basso della pagina',

// Stuff for the page footer
'Board footer'			=>	'Footer forum',
'Search links'			=>	'Link ricerca',
'Show recent posts'		=>	'Messaggi recenti',
'Show unanswered posts'	=>	'Discussioni senza risposta',
'Show your posts'		=>	'Mostra i tuoi messaggi',
'Show your topics'		=>	'Mostra le tue discussioni',
'Show subscriptions'	=>	'Mostra le tue discussioni sottoscritte',
'Jump to'				=>	'Vai a',
'Go'					=>	' Vai ',		// submit button in forum jump
'Move topic'			=>  'Sposta discussione',
'Open topic'			=>  'Apri discussione',
'Close topic'			=>  'Chiudi discussione',
'Unstick topic'			=>  'Disevidenzia discussione',
'Stick topic'			=>  'Evidenzia discussione',
'Moderate forum'		=>	'Modera categoria',
'Delete posts'			=>	'Cancella messaggi multipli',
'Debug table'			=>	'Informazione Debug',
'Move posts'			=>	'Déplacer plusieurs messages',

// For extern.php RSS feed
'RSS Desc Active'		=>	'Le discussioni nuove pi&ugrave; attive a ',	// board_title will be appended to this string
'RSS Desc New'			=>	'La discussione pi&ugrave; nuova a',					// board_title will be appended to this string
// CaptchaBox entries
'captchabox post tip'		=>  'Click in the dark area of the image to send your post.',
'captchabox reg tip'		=>  'Click in the dark area of the image to register.',
'captchabox failed'		=>  'You failed the Captcha test. Try again.',
'captchabox denied'		=>  'You have used all your tries for the Captcha. Try again later.',
'captchabox img title'		=>  'Click the box for next step'
);
