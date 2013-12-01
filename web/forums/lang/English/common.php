<?php

/*
// Determine what locale to use
switch (PHP_OS)
{
	case 'WINNT':
	case 'WIN32':
		$locale = 'english';
		break;

	case 'FreeBSD':
	case 'NetBSD':
	case 'OpenBSD':
		$locale = 'en_US.UTF-8';
		break;

	default:
		$locale = 'en_US';
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
'meta_language'                 =>      'en',

// Meta data in HTML header
'meta_description'      =>  'Mountain Community',
'meta_keywords'         =>  'forum, mountain, ski touring and mountaineering, alpinism, rock, ice and mixed climbing, scrambling, hiking, trekking, snow-shoeing',

// Notices
'Bad request'			=>	'Bad request. The link you followed is incorrect or outdated.',
'No view'				=>	'You do not have permission to view these forums.',
'No permission'			=>	'You do not have permission to access this page.',
'Bad referrer'			=>	'Bad HTTP_REFERER. You were referred to this page from an unauthorized source. If the problem persists please make sure that \'Base URL\' is correctly set in Admin/Options and that you are visiting the forum by navigating to that URL. More information regarding the referrer check can be found in the PunBB documentation.',

// Topic/forum indicators
'New icon'				=>	'There are new posts',
'Normal icon'			=>	'<!-- -->',
'Closed icon'			=>	'This topic is closed',
'Redirect icon'			=>	'Redirected forum',

// Miscellaneous
'Announcement'			=>	'Announcement',
'Options'				=>	'Options',
'Actions'				=>	'Actions',
'Submit'				=>	'Submit',	// "name" of submit buttons
'Submit and topic'		=>	'Submit and see the topic',
'Submit and forum'		=>	'Submit and see the list',
'Preview'				=>	'Preview',	// submit button to preview message
'Ban message'			=>	'You are banned from this forum.',
'Ban message 2'			=>	'The ban expires at the end of',
'Ban message 3'			=>	'The administrator or moderator that banned you left the following message:',
'Ban message 4'			=>	'Please direct any inquiries to the forum administrator at',
'Ban message 5'			=>	'The forums of camptocamp.org currently facing trouble with some types of Internet connections. To remedy this, please contact the administrator at',
'Never'					=>	'Never',
'Today'					=>	'Today',
'Yesterday'				=>	'Yesterday',
'Info'					=>	'Info',		// a common table header
'Go back'				=>	'Go back',
'Maintenance'			=>	'Maintenance',
'Redirecting'			=>	'Redirecting',
'Click redirect'		=>	'Click here if you do not want to wait any longer (or if your browser does not automatically forward you)',
'on'					=>	'on',		// as in "BBCode is on"
'off'					=>	'off',
'Invalid e-mail'		=>	'The e-mail address you entered is invalid.',
'required field'		=>	'is a required field in this form.',	// for javascript form validation
'Last post'				=>	'Last post',
'by'					=>	'by',	// as in last post by someuser
'New posts'				=>	'New',	// the link that leads to the first new post (use &nbsp; for spaces)
'New posts info'		=>	'Go to the first new post in this topic.',	// the popup text for new posts links
'Username'				=>	'Username',
'Password'				=>	'Password',
'E-mail'				=>	'E-mail',
'Send e-mail'			=>	'Send e-mail',
'Moderated by'			=>	'Moderated by',
'Registered'			=>	'Registered',
'Subject'				=>	'Subject',
'Message'				=>	'Message',
'Topic'					=>	'Topic',
'topic'					=>	'topic',
'Forum'					=>	'Forum',
'forum'					=>	'forum',
'Posts'					=>	'Posts',
'Replies'				=>	'Replies',
'Author'				=>	'Author',
'Pages'					=>	'Pages',
'B button help'     =>  'Bold',
'I button help'     =>  'Italic',
'U button help'     =>  'Underline',
'S button help'     =>  'Strikethrough',
'C button help'     =>  'Monospaced font',
'http button help'    =>  'Insert Link',
'@ button help'     =>  'Email address',
'Img button help'   =>  'Image from web',
'Img wizard title' => 'Insert an image',
'Code button help'    =>  'Code',
'Quote button help'   =>  'Quote',
'Help'					=>	'Help',
'Reduce the text box'	=>	'Reduce the text box',
'Enlarge the text box'	=>	'Enlarge the text box',
'BBCode'				=>	'BBCode',	// You probably shouldn't change this
'img tag'				=>	'[img] tag',
'Smilies'				=>	'Smilies',
'and'					=>	'and',
'Image link'			=>	'image',	// This is displayed (i.e. <image>) instead of images when "Show images" is disabled in the profile
'wrote'					=>	'wrote',	// For [quote]'s
'Code'					=>	'Code',		// For [code]'s
'Mailer'				=>	'Mailer',	// As in "MyForums Mailer" in the signature of outgoing e-mails
'Important information'	=>	'Important information',
'Write message legend'	=>	'Write your message and submit',
'all'                   =>  'all',
'Previous'              =>  'Previous',
'Next'                  =>  'Next',
'see embedded'          =>  '[show]',
'close embedded'        =>  '[hide]',

// Title
'Title'					=>	'Title',
'Member'				=>	'Member',	// Default title
'Moderator'				=>	'Moderator',
'Administrator'			=>	'Administrator',
'Banned'				=>	'Banned',
'Guest'					=>	'Guest',

// Stuff for include/parser.php
'BBCode error'			=>	'The BBCode syntax in the message is incorrect.',
'BBCode error 1'		=>	'Missing start tag for [/quote].',
'BBCode error 2'		=>	'Missing end tag for [code].',
'BBCode error 3'		=>	'Missing start tag for [/code].',
'BBCode error 4'		=>	'Missing one or more end tags for [quote].',
'BBCode error 5'		=>	'Missing one or more start tags for [/quote].',
'Original image'		=>	'Click here to see the original format.',

// Stuff for the navigator (top of every page)
'Index'					=>	'Index',
'User list'				=>	'User list',
'Rules'					=>  'Rules',
'Search'				=>  'Search',
'Register'				=>  'Register',
'Login'					=>  'Login',
'Not logged in'			=>  'You are not logged in.',
'Profile'				=>	'Profile',
'Logout'				=>	'Logout',
'Logged in as'			=>	'Logged in as',
'Admin'					=>	'Administration',
'Last visit'			=>	'Last visit',
'Show new posts'		=>	'New posts since last visit',
'Mark all as read'		=>	'Mark all topics as read',
'Mark forum as read'	=>	'Mark this forum as read',
'Link separator'		=>	'',	// The text that separates links in the navigator
'Top'				    =>	'Top',
'Bottom'				=>	'Bottom',

// Stuff for the page footer
'Board footer'			=>	'Board footer',
'Search links'			=>	'Search links',
'Show recent posts'		=>	'Recent posts',
'Show unanswered posts'	=>	'Unanswered posts',
'Show your posts'		=>	'Your posts',
'Show your topics'		=>	'Topics you\'ve started',
'Show subscriptions'	=>	'Your subscribed topics',
'Jump to'				=>	'Jump to',
'Go'					=>	' Go ',		// submit button in forum jump
'Move topic'			=>  'Move topic',
'Open topic'			=>  'Open topic',
'Close topic'			=>  'Close topic',
'Unstick topic'			=>  'Unstick topic',
'Stick topic'			=>  'Stick topic',
'Moderate forum'		=>	'Moderate forum',
'Delete posts'			=>	'Delete multiple posts',
'Debug table'			=>	'Debug information',
'Move posts'			=>	'Déplacer plusieurs messages',


// For extern.php RSS feed
'RSS Desc Active'		=>	'The most recently active topics at',	// board_title will be appended to this string
'RSS Desc New'			=>	'The newest topics at',					// board_title will be appended to this string
// CaptchaBox entries
'captchabox post tip'		=>  'Click in the dark area of the image to send your post.',
'captchabox reg tip'		=>  'Click in the dark area of the image to register.',
'captchabox failed'		=>  'You failed the Captcha test. Try again.',
'captchabox denied'		=>  'You have used all your tries for the Captcha. Try again later.',
'captchabox img title'		=>  'Click the box for next step'
);
