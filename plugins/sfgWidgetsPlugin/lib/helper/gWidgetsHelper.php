<?php

/**
 * @package sfgWidgetsPlugin
 * 
 * @author Gerald Estadieu <gestadieu@gmail.com>
 * @since  15 Apr 2007
 * @version 1.0.0
 * 
 */

/**
 * Returns a javascript to specify parameters for gWidgets (optional)
 *
 * @param array  $options   Options of gWidgets
 * 
 * @author Gerald Estadieu <gestadieu@gmail.com>
 * @since  15 Apr 2007
 *
 */
function gwidgets_js($options=array())
{
	_loadRessources();
	$opt = _options_for_javascript($options);

	return javascript_tag("
	var gWidget_Options = $opt;
	");
}

/**
 * Returns a link with a gTip (tooltip) attached to it
 *
 * @param string $content content to show inside the a tag (tooltip controller)
 * @param string $content_tip     content to show inside the tooltip if not ajax (tooltip container)
 * @param array  $options   Options of the a link and tooltip
 * 
 * @author Gerald Estadieu <gestadieu@gmail.com>
 * @since  15 Apr 2007
 *
 */
function gtip( $content, $content_tip='', $options=array() )
{
	_loadRessources();
	$html_options = _parse_attributes($options);
	$html_options['class'] = _get_option($options,'class','') . ' gtip';
	$tooltip = '';
	$href = _get_option($options,'href',''); 
	if (isset($options['id'])) { 
		$tooltip = content_tag('div',$content_tip,array('id'=>$options['id'], 'style' => 'display:none;'));
		unset($html_options['id']);
		$html_options['query_string'] = ($options['query_string'])?'gtip='.$options['id'].'&'.$options['query_string']:'gtip='.$options['id'];
	} 
	return link_to($content,$href,$html_options) . $tooltip;
}

/**
 * Returns a link with a gExpander (toggle visibility content) attached to it
 *
 * @param string $content content to show inside the a tag
 * @param string $href url (inline/ajax) to show/hide
 * @param array  $options   Options of the link
 * 
 * @author Gerald Estadieu <gestadieu@gmail.com>
 * @since  15 Apr 2007
 *
 */
function gexpander( $content, $href = '', $options = array() )
{
	_loadRessources();
	$html_options = _parse_attributes($options);
	$html_options['class'] = (isset($options['class'] ))?$options['class'] . ' gexpander':'gexpander';
	return link_to($content,$href,$html_options);
}

/**
 * Returns a link with a gBox widget attached
 *
 * @param string $content content to show inside the a tag
 * @param string $href url (inline/ajax) to show in the gbox
 * @param array  $options   Options of the link
 * 
 * @author Gerald Estadieu <gestadieu@gmail.com>
 * @since  15 Apr 2007
 *
 */
function gbox($content, $href = '', $options = array())
{
	_loadRessources();
	$html_options = _parse_attributes($options);
	$html_options['class'] = (isset($options['class'] ))?$options['class'] . ' gbox':'gbox';
	return link_to($content,$href,$html_options);
}

/**
 * Returns a gwidget
 *
 * @param string $widget_type specify the type of widget: 'gtip', 'gexpander', 'gbox'
 * @param string $content     content to show 
 * @param array  $options   Options of the link
 * 
 * @author Gerald Estadieu <gestadieu@gmail.com>
 * @since  15 Apr 2007
 *
 */
function gwidget($widget_type, $content, $options)
{
	
}

function _loadRessources()
{
	// Prototype & scriptaculous
  $response = sfContext::getInstance()->getResponse();
  $response->addJavascript(sfConfig::get('sf_prototype_web_dir'). '/js/prototype');

  $response->addJavascript('/sfgWidgetsPlugin/js/base');
  $response->addJavascript('/sfgWidgetsPlugin/js/gwidgets');
	$response->addStylesheet('/sfgWidgetsPlugin/css/gwidgets');
}

_loadRessources();