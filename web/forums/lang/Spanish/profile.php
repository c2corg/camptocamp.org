<?php

// Language definitions used in profile.php
$lang_profile = array(

// Navigation and sections
'Profile menu'				=>	'Menú de Perfil',
'Section essentials'		=>	'Esenciales',
'Section personal'			=>	'Personal',
'Section messaging'			=>	'Mensajería',
'Section personality'		=>	'Personalidad',
'Section display'			=>	'Visualización',
'Section privacy'			=>	'Privado',
'Section admin'				=>	'Administración',

// Miscellaneous
'Username and pass legend'	=>	'Introduce nombre de usuario y contraseña',
'Personal details legend'	=>	'Introduce los detalles personales',
'Contact details legend'	=>	'Introduce los datos de mensajería',
'Options display'			=>	'Especifica las opciones de visualización',
'Options post'				=>	'Especifica las opciones de visualización de mensajes',
'User activity'				=>	'Actividad del usuario',
'Paginate info'				=>	'Introduce el número de temas y mensajes que quieres ver en cada página.',

// Password stuff
'Pass key bad'				=>	'La clave de activación de tu contraseña es incorrecta o ha expirado. Por favor, solicita una contraseña nueva. Si falla, contacta con el administrador del foro en',
'Pass updated'				=>	'Tu contraseña ha sido actualizada. Ahora puedes entrar con tu contraseña nueva.',
'Pass updated redirect'		=>	'Contraseña actualitzada. Redirigiendo &hellip;',
'Wrong pass'				=>	'Contraseña antigua errónea.',
'Change pass'				=>	'Cambiar contraseña',
'Change pass legend'		=>	'Introduce y confirma la nueva contraseña',
'Old pass'					=>	'Contraseña antigua',
'New pass'					=>	'Contraseña nueva',
'Confirm new pass'			=>	'Confirma contraseña nueva',

// E-mail stuff
'E-mail key bad'			=>	'La clave de activación especificada es incorrecta o ha caducado. Por favor vuelve a solicitar el cambio de dirección electrónica. Si ésto falla, contacta con el administrador en',
'E-mail updated'			=>	'Tu dirección electrónica ha sido actualizada.',
'Activate e-mail sent'		=>	'Se ha enviado un correo a la dirección especificada con instrucciones de cómo activar la nueva dirección de correo electrónico. Si no lo recibes puedes contactar con el administrador en',
'E-mail legend'				=>	'Introduce la nueva dirección de correo electrónico',
'E-mail instructions'		=>	'Se enviará un correo a la nueva dirección con un enlace en el correo para activar la nueva dirección.',
'Change e-mail'				=>	'Cambia la dirección de correo electrónico',
'New e-mail'				=>	'Nuevo correo',

// Avatar upload stuff
'Avatars disabled'			=>	'El administrador ha deshabilitado el uso de avatares.',
'Too large ini'				=>	'El fichero seleccionado es demasiado grande. El servidor no permite subir el fichero.',
'Partial upload'			=>	'El fichero seleccionado ha sido subido parcialmente. Por favor vuelve a intentarlo.',
'No tmp directory'			=>	'PHP ha sido incapaz de guardar el fichero enviado en la ubicación temporal.',
'No file'					=>	'No has seleccionado ningún fichero para subirlo.',
'Bad type'					=>	'El fichero que has intentado subir es de un tipo no permitido. Los formatos permitidos son gif, jpeg y ipng.',
'Too wide or high'			=>	'El fichero que has intentado subir es más ancho y/o alto que el máximo permitido',
'Too large'					=>	'El fichero que has intentado subir es de más tamaño que el máximo permitido',
'pixels'					=>	'pixels',
'bytes'						=>	'bytes',
'Move failed'				=>	'El servidor no ha podido guardar el fichero subido. Por favor contacta con el administrador del foro en',
'Unknown failure'			=>	'Ha ocurrido un error desconocido. Por favor vuelve a intentarlo.',
'Avatar upload redirect'	=>	'Avatar subido. Redirigiendo &hellip;',
'Avatar deleted redirect'	=>	'Avatar borrado. Redirigiendo &hellip;',
'Avatar desc'				=>	'Un avatar es una imagen pequeña que se visualiza con tu nombre de usuario en tus mensajes. No tiene que ser mayor que',
'Upload avatar'				=>	'Subir avatar',
'Upload avatar legend'		=>	'Introduce un fichero para subir',
'Delete avatar'				=>	'Borra avatar',	// only for admins
'File'						=>	'Fichero',
'Upload'					=>	'Subir',	// submit button

// Form validation stuff
'Dupe username'				=>	'Ya hay alguien registrado con este nombre. Por favor vuelve atrás y prueba con un nombre de usuario diferente.',
'Forbidden title'			=>	'El título que has introducido contiene una palabra no permitida. Tienes que escoger un título diferente.',
'Profile redirect'			=>	'Perfil actualizado. Redirigiendo &hellip;',

// Profile display stuff
'Not activated'				=>	'Este usuario todavía no ha activado su cuenta. La cuenta se activa cuando el/ella se identifica por primera vez.',
'Unknown'					=>	'(Desconocido)',	// This is displayed when a user hasn't filled out profile field (e.g. Location)
'Private'					=>	'(Privado)',	// This is displayed when a user does not want to receive e-mails
'No avatar'					=>	'(Sin avatar)',
'Show posts'				=>	'Muestra todos los mensajes',
'Realname'					=>	'Nombre real',
'Location'					=>	'Ubicación',
'Website'					=>	'Página Web',
'Jabber'					=>	'mac.com',
'ICQ'						=>	'ICQ',
'MSN'						=>	'MSN Messenger',
'AOL IM'					=>	'AOL IM',
'Yahoo'						=>	'Yahoo! Messenger',
'Avatar'					=>	'Avatar',
'Signature'					=>	'Firma',
'Sig max length'			=>	'Max longitud',
'Sig max lines'				=>	'Max líneas',
'Avatar legend'				=>	'Especifica las opciones de visualización de la avatar',
'Avatar info'				=>	'Una avatar es una imagen pequeña que se visualizará en todos tus mensajes. Puedes subir una avatar haciendo un click sobre el enlace que hay abajo. La ventana de verificación \'Usar avatar\' de abajo tiene que estar marcada para que la avatar siga visible en tus mensajes.',
'Change avatar'				=>	'Cambiar avatar',
'Use avatar'				=>	'Usar avatar.',
'Signature legend'			=>	'Crea tu firma',
'Signature info'			=>	'Una firma es una parte pequeña de texto que se adjunta en los mensajes. En ella, puedes poner lo que quieras. Quizás quieras poner tu cita favorita o tu signo del zodíaco. ¡Es cosa tuya! En la firma puedes usar el  BBCode si lo permite este forum. Puedes ver las opciones que son permitidas/habilitadas listadas abajo cuando quieras editar tu firma.',
'Sig preview'				=>	'Previsualización de la firma actual:',
'No sig'					=>	'No hay ninguna firma configurada actualmente.',
'Topics per page'			=>	'Temas',
'Topics per page info'		=>	'Este parámetro controla el número de temas a mostrar por página a la hora de ver un foro. Si no estás seguro de qué poner, puedes dejarlo en blanco y se usará el parámetro por defecto.',
'Posts per page'			=>	'Mensajes',
'Posts per page info'		=>	'Este parámetro controla el número de mensajes a mostrar por página a la hora de ver el foro. Si no estás seguro de qué poner, puedes dejarlo en blanco y el foro usará el parámetro por defecto.',
'Leave blank'				=>	'Dejar en blanco para usar el valor predetermiando.',
'Notify full'				=>	'Incluir el mensaje en los correos de suscripción.',
'Notify full info'			=>	'Con esto habilitado, se incluye un texto en los correos de notificación de suscripción.',
'Show smilies'				=>	'Muestra smileys como iconos gráficos',
'Show smilies info'			=>	'Si habilitas esta opción, aparecen pequeñas imágenes en lugar de smileys de texto.',
'Show images'				=>	'Muestra imágenes en los mensajes.',
'Show images info'			=>	'Deshabilita esto si no deseas ver imagenes en los mensajes (por ejemplo imagenes mostradascon el marcador [img]) ',
'Show images sigs'			=>	'Muestra imagenes en las firmas del usuario.',
'Show images sigs info'		=>	'Deshabilitar esto si no deseas ver imagenes en las firmas (por ej. imagenes insertadas con el marcador [img])',
'Show avatars'				=>	'Mostrar en los mensajes los avatares de los usuarios',
'Show avatars info'			=>	'Esta opción cambia entre ver o no los avatares en los mensajes.',
'Show sigs'					=>	'Muestra las firmas de los usuarios',
'Show sigs info'			=>	'Habilitar si quieres ver las firmas de los usuarios.',
'Style legend'				=>	'Selecciona el estilo que prefieres ',
'Style info'				=>	'Si quieres puedes usar un estilo visual diferente para el foro.',
'Admin note'				=>	'Nota del Administrador',
'Pagination legend'			=>	'Introduce las opciones de paginación',
'Post display legend'		=>	'Especifica tus opciones para ver mensajes',
'Post display info'			=>	'Si tienes una conexión lenta, deshabilita estas opciones,  especialmente mostrar imágenes  en los mensajes y firmas, hará que las páginas se carguen más rápidamente.',
'Instructions'				=>	'Cuando actualices el perfil, serás redirigido de nuevo a esta página.',

// Administration stuff
'Group membership legend'	=>	'Selecciona un grupo de usuarios',
'Save'						=>	'Guardar',
'Set mods legend'			=>	'Especifica el acceso del moderador',
'Moderator in'				=>	'Moderador en',
'Moderator in info'			=>	'Selecciona los foros que este usuario tendría que moderar. Nota: Esto sólo se aplica a los moderadores. Los administradores siempre tienen todos los permisos en todos los foros.',
'Update forums'				=>	'Actualiza los foros',
'Delete ban legend'			=>	'Elimina (sólo administradores) o expulsa usuario',
'Delete user'				=>	'Elimina usuario',
'Ban user'					=>	'Expulsa usuario',
'Confirm delete legend'		=>	'Importante: leer antes de eliminar un usuario',
'Confirm delete user'		=>	'Confirma usuario eliminado',
'Confirmation info'			=>	'Por favor confirma que quieres eliminar el usuario',	// the username will be appended to this string
'Delete warning'			=>	'¡Aviso! Los usuarios y/o mensajes eliminados no se pueden recuperar. Si eliges no eliminar los mensajes hechos por este usuario, después los mensajes sólo podrán ser eliminados manualmente.',
'Delete posts'				=>	'Elimina cualquier mensaje y tema hecho por este usuario.',
'Delete'					=>	'Eliminar',		// submit button (confirm user delete)
'User delete redirect'		=>	'Usuario eliminado. Redirigiendo &hellip;',
'Group membership redirect'	=>	'Grupo de usuarios guardado. Redirigiendo &hellip;',
'Update forums redirect'	=>	'Derechos de moderador del foro actualizados. Redirigiendo &hellip;',
'Ban redirect'				=>	'Redirigiendo &hellip;'

);
