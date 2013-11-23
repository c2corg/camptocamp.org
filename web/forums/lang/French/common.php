<?php

/*
// Determine what locale to use
switch (PHP_OS)
{
	case 'WINNT':
	case 'WIN32':
		$locale = 'french';
		break;

	case 'FreeBSD':
	case 'NetBSD':
	case 'OpenBSD':
		$locale = 'fr_FR.UTF-8';
		break;

	default:
		$locale = 'fr_FR';
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
'meta_language'			=>	'fr',

// Meta data in HTML header
'meta_description'      =>  'La communauté montagne',
'meta_keywords'         =>  'forum, montagne, skirando, ski de rando, surfrando, surf, raquettes, alpinisme, neige, glace, mixte, goulotte, couloir, cascade, dry, rocher, escalade, falaise, grimpe, randonnée pédestre',

// Notices
'Bad request'			=>	'Erreur. Le lien que vous avez suivi est incorrect, ou périmé, ou vous n\'avez pas l\'autorisation.',
'No view'				=>	'Vous n\'avez pas l\'autorisation d\'accéder à ces forums.',
'No permission'			=>	'Vous n\'avez pas l\'autorisation d\'afficher cette page.',
'Bad referrer'			=>	'Mauvais HTTP_REFERER. Vous avez été renvoyé sur cette page par une source inconnue ou interdite. Si le problème persiste, assurez-vous que le champ \'URL de base\' de la page Admin/Options est correctement renseigné et que vous visitez ces forums en utilisant cette URL. Plus d\'informations pourront être trouvées dans la documentation de PunBB.',

// Topic/forum indicators
'New icon'				=>	'Il y a des nouveaux messages',
'Normal icon'			=>	'<!-- -->',
'Closed icon'			=>	'Cette discussion est fermée',
'Redirect icon'			=>	'Forum de redirection',

// Miscellaneous
'Announcement'			=>	'Annonce',
'Options'				=>	'Options',
'Actions'				=>	'Actions',
'Submit'				=>	'Envoyer',	// "name" of submit buttons
'Submit and topic'		=>	'Envoyer et voir la discussion',
'Submit and forum'		=>	'Envoyer et voir la liste',
'Preview'				=>	'Prévisualisation',	// submit button to preview message
'Ban message'			=>	'Votre compte utilisateur est exclu de ce forum.',
'Ban message 2'			=>	'L\'exclusion expire le',
'Ban message 3'			=>	'Raison de l\'exclusion&#160;:',
'Ban message 4'			=>	'Pour toute question, contactez l\'administrateur',
'Ban message 5'			=>	'Les forums de camptocamp.org rencontrent actuellement des problèmes avec certains types de connexions internet. Pour y remédier, veuillez contacter l\'administrateur',
'Never'					=>	'Jamais',
'Today'					=>	'Aujourd\'hui',
'Yesterday'				=>	'Hier',
'Info'					=>	'Info',		// a common table header
'Go back'				=>	'Retour',
'Maintenance'			=>	'Maintenance',
'Redirecting'			=>	'Redirection',
'Click redirect'		=>	'Cliquez ici si vous ne voulez pas attendre (ou si votre navigateur ne vous redirige pas).',
'on'					=>	'actif',		// as in "BBCode is on"
'off'					=>	'inactif',
'Invalid e-mail'		=>	'L\'adresse de courriel que vous avez saisie est invalide.',
'required field'		=>	'est un champ requis pour ce formulaire.',	// for javascript form validation
'Last post'				=>	'Dernier message',
'by'					=>	'par',	// as in last post by someuser
'New posts'				=>	'Nouveaux',	// the link that leads to the first new post (use &#160; for spaces)
'New posts info'		=>	'Allez au premier nouveau message de cette discussion.',	// the popup text for new posts links
'Username'				=>	'Nom d\'utilisateur',
'Password'				=>	'Mot de passe',
'E-mail'				=>	'Courriel',
'Send e-mail'			=>	'Envoyer un courriel',
'Moderated by'			=>	'Modéré par',
'Registered'			=>	'Date d\'inscription',
'Subject'				=>	'Sujet',
'Message'				=>	'Message',
'Topic'					=>	'Discussion',
'topic'					=>	'discussion',
'Forum'					=>	'Forum',
'forum'					=>	'forum',
'Posts'					=>	'Messages',
'Replies'				=>	'Réponses',
'Author'				=>	'Auteur',
'Pages'					=>	'Pages',
'B button help'			=>	'Formater le texte sélectionné en gras',
'I button help'			=>	'Formater le texte sélectionné en italique',
'U button help'			=>	'Souligner le texte sélectionné',
'S button help'			=>	'Barrer le texte sélectionné',
'C button help'			=>	'Utiliser une police à chasse fixe',
'http button help'		=>	'Insérer un lien sur le texte sélectionné, ou activer l\'url sélectionnée',
'@ button help'			=>	'Insérer une adresse email sur le texte sélectionné, ou activer l\'adresse sélectionnée',
'Img button help'		=>	'Insérer une image accessible sur le net',
'Img wizard title' => 'Insérer une image',
'Code button help'		=>	'Formater le texte sélectionné en boite de code (police à chasse fixe)',
'Quote button help'		=>	'Formater le texte sélectionné en boite de citation',
'Help'					=>	'Aide',
'Reduce the text box'	=>	'Réduire la zone de texte',
'Enlarge the text box'	=>	'Agrandir la zone de texte',
'BBCode'				=>	'BBCode',	// You probably shouldn't change this
'img tag'				=>	'Balise [img]',
'Smilies'				=>	'Émoticônes',
'and'					=>	'et',
'Image link'			=>	'image',	// This is displayed (i.e. <image>) instead of images when "Show images" is disabled in the profile
'wrote'					=>	'a écrit ',	// For [quote]'s
'Code'					=>	'Code ',	// For [code]'s
'Mailer'				=>	'Courriel automatique',	// As in "MyForums Mailer" in the signature of outgoing e-mails
'Important information'	=>	'Information importante',
'Write message legend'	=>	'Veuillez écrire votre message et l\'envoyer',
'all'                   =>  'tous',
'Previous'              =>  'Précédente',
'Next'                  =>  'Suivante',
'see embedded'          =>  '[afficher]',
'close embedded'        =>  '[fermer]',

// Title
'Title'					=>	'Titre',
'Member'				=>	'Membre',	// Default title
'Moderator'				=>	'Modérateur',
'Administrator'			=>	'Administrateur',
'Banned'				=>	'Banni',
'Guest'					=>	'Invité',

// Stuff for include/parser.php
'BBCode error'			=>	'La syntaxe BBCode est incorrecte.',
'BBCode error 1'		=>	'Il manque la balise d\'ouverture pour [/quote].',
'BBCode error 2'		=>	'Il manque la balise de fermeture pour [code].',
'BBCode error 3'		=>	'Il manque la balise d\'ouverture pour [/code].',
'BBCode error 4'		=>	'Il manque une ou plusieurs balises de fermeture pour [quote].',
'BBCode error 5'		=>	'Il manque une ou plusieurs balises d\'ouverture manquantes pour [/quote].',
'Original image'		=>	'Cliquez ici pour voir le format original.',

// Stuff for the navigator (top of every page)
'Index'					=>	'Index des forums',
'User list'				=>	'Liste des membres',
'Rules'					=>  'Règles',
'Search'				=>  'Recherche',
'Register'				=>  'Inscription',
'Login'					=>  'S\'identifier',
'Not logged in'			=>  'Vous n\'êtes pas identifié.',
'Profile'				=>	'Profil',
'Logout'				=>	'Déconnexion',
'Logged in as'			=>	'Connecté en tant que',
'Admin'					=>	'Administration',
'Last visit'			=>	'Dernière visite',
'Show new posts'		=>	'Messages non lus',
'Mark all as read'		=>	'Marquer toutes les discussions comme lues',
'Mark forum as read'	=>	'Marquer ce forum comme lu',
'Link separator'		=>	'',	// The text that separates links in the navigator
'Top'				    =>	'Haut de page',
'Bottom'				=>	'Bas de page',
'multilanguage'         =>  'multilingue',
'with pub'              =>  ' bistrot/p++',

// Stuff for the page footer
'Board footer'			=>	'Pied de page des forums',
'Search links'			=>	'Liens de recherche',
'Show recent posts'		=>	'Messages récents',
'Show unanswered posts'	=>	'Discussions sans réponse',
'Show your posts'		=>	'Discussions auxquelles vous avez participé',
'Show your topics'		=>	'Discussions que vous avez initiées',
'Show subscriptions'	=>	'Discussions auxquelles vous êtes abonné',
'Jump to'				=>	'Aller à',
'Go'					=>	' Aller ',		// submit button in forum jump
'Move topic'			=>  'Déplacer la discussion',
'Open topic'			=>  'Ouvrir la discussion',
'Close topic'			=>  'Fermer la discussion',
'Unstick topic'			=>  'Détacher la discussion',
'Stick topic'			=>  'Épingler la discussion',
'Moderate forum'		=>	'Modérer le forum',
'Delete posts'			=>	'Supprimer plusieurs messages',
'Debug table'			=>	'Informations de débogue',
'Move posts'			=>	'Déplacer plusieurs messages',


// For extern.php RSS feed
'RSS Desc Active'		=>	'Les discussions récemment actives de',	// board_title will be appended to this string
'RSS Desc New'			=>	'Les dernières discussions de',					// board_title will be appended to this string
// Entrees pour CaptchaBox 
'captchabox post tip'		=>  'Cliquez dans la zone sombre de l\'image pour envoyer votre message.',
'captchabox reg tip'		=>  'Cliquez dans la zone sombre de l\'image pour vous inscrire.',
'captchabox failed'		=>  'Vous avez raté le test du Captcha. Réessayez.',
'captchabox denied'		=>  'Vous avez utilisé tous vos essais pour le Captcha. Réessayez plus tard.',
'captchabox img title'		=>  'Cliquez sur le rectangle pour continuer'
);
 
