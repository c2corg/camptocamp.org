<?php

/*
// Determine what locale to use
switch (PHP_OS)
{
	case 'WINNT':
	case 'WIN32':
		$locale = 'spanish';
		break;

	case 'FreeBSD':
	case 'NetBSD':
	case 'OpenBSD':
		$locale = 'es_ES.UTF-8';
		break;

	default:
		$locale = 'es_ES';
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
'meta_language'                 =>      'es',

// Meta data in HTML header
'meta_description'      =>  'La comunidad de montaña',
'meta_keywords'         =>  'foro, montaña, esquí de montaña, raquetas, alpinismo, escalada, roca, hielo, miexto, dry, paredes, senderismo, trekking, andinismo',

// Notices
'Bad request'			=>	'Solicitud errónea. El enlace seguido es incorrecto o ha caducado.',
'No view'				=>	'Careces de permisos para ver estos foros.',
'No permission'			=>	'Careces de permisos para acceder a esta página',
'Bad referrer'			=>	'HTTP_REFERER erróneo. Has estado dirigido a esta página desde una fuente no autorizada. Si el problema continua por favor asegúrate que la \'URL base\' está correctamente configurada en Admin/Options y que estás visitando el foro desde esta URL. Puedes encontrar más información sobre este tema en la documentación de PunB.',

// Topic/forum indicators
'New icon'				=>	'Hay mensajes nuevos',
'Normal icon'			=>	'<!-- -->',
'Closed icon'			=>	'Este tema está cerrado',
'Redirect icon'			=>	'Foro redirigido',

// Miscellaneous
'Announcement'			=>	'Aviso',
'Options'				=>	'Opciones',
'Actions'				=>	'Acciones',
'Submit'				=>	'Enviar',	// "name" of submit buttons
'Submit and topic'		=>	'Enviar - Tema',
'Submit and forum'		=>	'Enviar - Foro',
'Preview'				=>	'Previsualización',	// submit button to preview message
'Ban message'			=>	'Estás expulsado de este foro. ',
'Ban message 2'			=>	'La expulsión expira al final de',
'Ban message 3'			=>	'El administrador o moderador que te ha expulsado ha dejado el siguiente mensaje:',
'Ban message 4'			=>	'Por favor dirigir cualquier pregunta al administrador del foro en',
'Ban message 5'			=>	'The forums of camptocamp.org currently facing trouble with some types of Internet connections. To remedy this, please contact the administrator at',
'Never'					=>	'Nunca',
'Today'					=>	'Hoy',
'Yesterday'				=>	'Ayer',
'Info'					=>	'Info',		// a common table header
'Go back'				=>	'Volver atrás',
'Maintenance'			=>	'Mantenimiento',
'Redirecting'			=>	'Redirigiendo',
'Click redirect'		=>	'Hacer un clic aquí si no quieres esperar más (o si tu explorador no te reenvía automáticamente)',
'on'					=>	'activo',		// as in "BBCode is on"
'off'					=>	'inactivo',
'Invalid e-mail'		=>	'La dirección de correo que has entrado no es válida.',
'required field'		=>	'es un campo requerido en este formulario.',	// for javascript form validation
'Last post'				=>	'Ultimo mensaje',
'by'					=>	'por',	// as in last post by someuser
'New posts'				=>	'Mensajes&nbsp;nuevos',	// the link that leads to the first new post (use &nbsp; for spaces)
'New posts info'		=>	'Ir al primer mensaje nuevo de este tema.',	// the popup text for new posts links
'Username'				=>	'Nombre de usuario (seudónimo)',
'Password'				=>	'Contraseña',
'E-mail'				=>	'E-mail',
'Send e-mail'			=>	'Envia e-mail',
'Moderated by'			=>	'Moderado por',
'Registered'			=>	'Registrado',
'Subject'				=>	'Asunto',
'Message'				=>	'Mensaje',
'Topic'					=>	'Tema',
'topic'					=>	'tema',
'Forum'					=>	'Foro',
'forum'					=>	'foro',
'Posts'					=>	'Mensajes',
'Replies'				=>	'Respuestas',
'Author'				=>	'Autor',
'Pages'					=>	'Páginas',
'B button help'     =>  'pon en negrita el texto seleccionado',
'I button help'     =>  'pon en cursiva el texto seleccionado',
'U button help'     =>  'subrayar el texto seleccionado',
'S button help'     =>  'tacha el texto seleccionado',
'C button help'     =>  'caracteres de ancho fijo',
'http button help'    =>  'inserta un enlace en el texto seleccionado o activa el enlace seleccionado',
'@ button help'     =>  'inserta un email en el texto seleccionado o activa el email seleccionado',
'Img button help'   =>  'insertar una imagen a partir del enlace a una imagen en internet',
'Img wizard title' => 'Insertar una imagen',
'Code button help'    =>  'poner el texto seleccionado en un recuadro a caracteres de ancho fijo',
'Quote button help'   =>  'poner el texto seleccionado en un recuadro de citación',
'Help'					=>	'Ayuda',
'Reduce the text box'	=>	'Disminuir la zona de texto',
'Enlarge the text box'	=>	'Aumentar la zona de texto',
'BBCode'				=>	'BBCode',	// You probably shouldn't change this
'img tag'				=>	'Marcador [img]',
'Smilies'				=>	'Smileys',
'and'					=>	'y',
'Image link'			=>	'imagen',	// This is displayed (i.e. <image>) instead of images when "Show images" is disabled in the profile
'wrote'					=>	'dijo',	// For [quote]'s
'Code'					=>	'Código',		// For [code]'s
'Mailer'				=>	'Administrador de correo',	// As in "MyForums Mailer" in the signature of outgoing e-mails
'Important information'	=>	'Información importante',
'Write message legend'	=>	'Escribe tu mensaje y envíalo',
'all'                   =>  'todos',

// Title
'Title'					=>	'Título',
'Member'				=>	'Miembro',	// Default title
'Moderator'				=>	'Moderador',
'Administrator'			=>	'Administrador',
'Banned'				=>	'Expulsado',
'Guest'					=>	'Invitado',

// Stuff for include/parser.php
'BBCode error'			=>	'La sintaxis del BBCode en este mensaje es errónea.',
'BBCode error 1'		=>	'Falta el marcador de inicio para un [/quote].',
'BBCode error 2'		=>	'Falta el marcador de fin para un [code].',
'BBCode error 3'		=>	'Falta el marcador de inicio para un [/code].',
'BBCode error 4'		=>	'Falta uno o más marcadores de fin para un [quote].',
'BBCode error 5'		=>	'Falta uno o más marcadores de inicio para un [/quote].',
'Original image'		=>	'Hacer un clic aquí para ver el formato original.',

// Stuff for the navigator (top of every page)
'Index'					=>	'Inicio',
'User list'				=>	'Lista de usuarios',
'Rules'					=>  'Reglas',
'Search'				=>  'Busca',
'Register'				=>  'Regístrate',
'Login'					=>  'Entrar',
'Not logged in'			=>  'No te has registrado',
'Profile'				=>	'Perfil',
'Logout'				=>	'Salir',
'Logged in as'			=>	'Identificado como',
'Admin'					=>	'Administración',
'Last visit'			=>	'Ultima visita',
'Show new posts'		=>	'Muestra mensajes nuevos desde la última visita',
'Mark all as read'		=>	'Marca todos los temas como leídos',
'Mark forum as read'	=>	'Marca este foro como leídos',
'Link separator'		=>	'',	// The text that separates links in the navigator
'Top'				    =>	'Arriba',
'Bottom'				=>	'Abajo',

// Stuff for the page footer
'Board footer'			=>	'Pie del foro',
'Search links'			=>	'Busca enlaces',
'Show recent posts'		=>	'Muestra mensajes recientes',
'Show unanswered posts'	=>	'Muestra mensajes sin respuesta',
'Show your posts'		=>	'Muestra mis mensajes',
'Show your topics'		=>	'Muestra mis temas',
'Show subscriptions'	=>	'Muestra mis temas suscritos',
'Jump to'				=>	'Ir a',
'Go'					=>	' Ir',		// submit button in forum jump
'Move topic'			=>  'Mueve tema',
'Open topic'			=>  'Abre tema',
'Close topic'			=>  'Cierra tema',
'Unstick topic'			=>  'Desmarca permanente',
'Stick topic'			=>  'Marca como permanente',
'Moderate forum'		=>	'Moderar el foro',
'Delete posts'			=>	'Borra mensajes múltiples',
'Debug table'			=>	'Información de depuración',
'Move posts'			=>	'Déplacer plusieurs messages',

//For extern.php RSS feed
'RSS Desc Active'		=>	'Ultimos temas activos en',	// board_title will be appended to this string
'RSS Desc New'			=>	'Ultimos temas en',	// board_title will be appended to this string
// CaptchaBox entries
'captchabox post tip'		=>  'Click in the dark area of the image to send your post.',
'captchabox reg tip'		=>  'Click in the dark area of the image to register.',
'captchabox failed'		=>  'You failed the Captcha test. Try again.',
'captchabox denied'		=>  'You have used all your tries for the Captcha. Try again later.',
'captchabox img title'		=>  'Click the box for next step'
);
