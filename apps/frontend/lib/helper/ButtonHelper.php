<?php
/**
 * Button helpers
 * Provide shortcuts to buttons for nav and nav4lists
 *
 * @version $Id: ButtonHelper.php 2531 2007-12-19 15:32:44Z alex $
 */
  
function button_create($module)
{
    return link_to(__("Create new $module"),
                   "@document_edit?module=$module&id=&lang=",
                   array('title' => __("Create new $module"),
                         'class' => 'action_create nav_edit'));
}

function button_changes($module)
{
    return link_to(__('Recent changes'),
                   "@module_whatsnew?module=$module",
                   array('title' => __('View latest changes for this kind of documents'),
                         'class' => 'action_list nav_edit'));
}

function button_rss($module, $lang, $id=null, $mode=null)
{
    if ($id)
    {
        return link_to(__('RSS feed'),
                       "@document_feed?module=$module&id=$id&lang=$lang",
                       array('title' => __('Subscribe to this document-s latest editions'),
                             'class' => 'action_rss nav_edit',
                             'rel' => 'nofollow'));
    }
    else
    {
        switch ($mode)
        {      
            case 'creations':
                return link_to(__('RSS feed creations'),
                               "@creations_feed?module=$module&lang=$lang",
                               array('title' => __("Subscribe to latest $module creations"),
                                     'class' => 'action_rss_new nav_edit',
                                     'rel' => 'nofollow'));
        
            default:
                return link_to(__('RSS feed'),
                               "@feed?module=$module&lang=$lang",
                               array('title' => __("Subscribe to latest $module editions"),
                                     'class' => 'action_rss nav_edit',
                                     'rel' => 'nofollow'));
        }
    }
}

function get_rsslist_path($module)
{
    $request = sfContext::getInstance()->getRequest();
    $path = $request->getPathInfo();
    if (substr($path, 1) == $module)
    {
        $path .= '/rss';
    }
    else
    {
        $path = str_replace('list', 'rss', $path);
    }
    return $request->getUriPrefix() . $path;
}

function button_rsslist($module)
{
    return link_to(__('RSS list'), get_rsslist_path($module),
                   array('title' => __('Get current list in RSS format'),
                         'class' => 'action_rss nav_edit',
                         'rel' => 'nofollow'));
}

function button_search($module)
{
    return link_to(__('Search'),
                   "@filter?module=$module",
                   array('title' => __("Search a $module"), 'class' => 'action_query nav_edit'));
}

function button_delete($module, $id)
{
    return link_to(__('Delete'),
                   "@doc_delete?module=$module&id=$id",
                   array('title' => __('Delete this document'),
                         'class' => 'action_delete nav_edit',
                         'post' => true,
                         'confirm' => __('Are you sure you want to delete this document in every language?')));
}

function button_rotate($degrees, $id)
{
    return link_to(__("Rotate $degrees"),
                   "@default?module=images&action=rotate&id=$id&degrees=$degrees",
                   array('title' => __("Rotate the image by $degrees"),
                         'class' => "action_rotate_$degrees action_rotate nav_edit",
                         'post' => true,
                         'confirm' => __('Are you sure you want to rotate the image?')));
}

function button_delete_culture($module, $id, $culture)
{
    return link_to(__('Delete culture'),
                   "@culture_delete?module=$module&id=$id&lang=$culture",
                   array('title' => __('Delete this document culture'),
                         'class' => 'action_delete nav_edit',
                         'post' => true,
                         'confirm' => __('Are you sure you want to delete this document in '.$culture)));
}

function button_delete_geom($module, $id)
{
    return link_to(__('Delete geometry'),
                   "@geom_delete?module=$module&id=$id",
                   array('title' => __('Delete this documents geometry'),
                         'class' => 'action_delete nav_edit',
                         'post' => true,
                         'confirm' => __('Are you sure you want to delete this documents geometry?')));
}

function button_clear_cache($module, $id)
{
    return link_to(__('Clear document cache (short)'),
                   "@cache_clear?module=$module&id=$id",
                   array('title' => __('Clear document cache'),
                         'class' => 'action_description nav_edit',
                         'post' => true));
}

function button_refresh_geo_associations($module, $id)
{
    return link_to(__('Refresh geoassociations'),
                   "@doc_geoass_refresh?module=$module&id=$id",
                   array('title' => __('Refresh the geoassociations of this document'),
                         'class' => 'picto_maps nav_edit',
                         'post' => true));
}

function button_associations_history($module, $id)
{
    return link_to(__('Associations history'),
                   "@latestassociations_doc?module=$module&id=$id",
                   array('title' => __('View the associations history of the document'),
                         'class' => 'picto_add nav_edit'));
}

function button_merge($module, $id) 
{
    use_helper('ModalBox');
    return m_link_to(__('Merge'),
                     "@doc_merge?module=$module&from_id=$id&to_id=0",
                     array('title' => __('Merge this document into another one'),
                           'class' => 'action_merge nav_edit',
                           'post' => true),
                     array('width' => 600));
}

function button_protect($module, $id, $document_is_protected)
{
    use_helper('Javascript');
    
    $protect = ucfirst(__('protect')); 
    $unprotect = ucfirst(__('deprotect')); 
    $protect_title = __('Protect this document');
    $unprotect_title = __('Unprotect this document');

    $msg = $document_is_protected ? $unprotect : $protect;
    $class = $document_is_protected ? 'action_unprotect' : 'action_protect';
    $title = $document_is_protected ? $unprotect_title : $protect_title;

    $js = "var btn = $(this), indicator = $('#indicator');
indicator.show();
$.ajax('" . url_for("@doc_protect?module=$module&id=$id") . "')
  .done(function(data) {
    var tmp =  btn.text();
    btn.text(btn.attr('data-alt-content'))
      .attr({
        'class': btn.attr('data-alt-class'),
        title: btn.attr('data-alt-title'),
        'data-alt-class': btn.attr('class'),
        'data-alt-title': btn.attr('title'),
        'data-alt-content': tmp
      });
    C2C.showSuccess(data);})
  .fail(function(data) {
    C2C.showFailure(data);})
  .always(function() {
    indicator.hide();})";

    return link_to_function($msg, $js,
                            array('title' => $title,
                                  'class' => $class . ' nav_edit',
                                  'id' => 'protect_btn',
                                  'data-alt-class' => ($document_is_protected ? 'action_protect' : 'action_unprotect') . ' nav_edit',
                                  'data-alt-content' => $document_is_protected ? $protect : $unprotect,
                                  'data-alt-title' =>  $title = $document_is_protected ? $protect_title : $unprotect_title));
}

function button_back($module)
{
    return link_to(__("$module list"),
                   "@default_index?module=$module",
                   array('title' => __("Back to $module list"), 
                         'class' => "picto_$module nav_edit"));
}

function button_prev($module, $current_id)
{
    $modname = strtolower(c2cTools::module2model($module));
    return link_to(__("previous $modname"),
                   "@goto_prev?module=$module&id=$current_id",
                   array('title' => __("Go to previous $modname"),
                         'class' => 'action_back nav_edit'));
}

function button_next($module, $current_id)
{
    $modname = strtolower(c2cTools::module2model($module));
    return link_to(__("next $modname"),
                   "@goto_next?module=$module&id=$current_id",
                   array('title' => __("Go to next $modname"),
                         'class' => 'action_next nav_edit'));
}

function button_mail($id)
{
    use_helper('Forum');
    return f_link_to(__('Send mail'), "misc.php?email=$id",
                     array('title' => __('Send a mail to this user'),
                           'class' => 'action_contact nav_edit'));
}

function button_pm($id)
{
    use_helper('Forum');
    return f_link_to(__('Private message'), "message_send.php?id=$id&uid=$id",
                     array('title' => __('Send a private message to this user'),
                           'class' => 'action_contact nav_edit'));
}

function button_profile($id)
{
    use_helper('Forum');
    return f_link_to(__('User profile'), "profile.php?section=personality&id=$id",
                     array('title' => __('View user profile'),
                           'class' => 'action_list nav_edit'));
}

function button_add_route($id)
{
    return link_to(__('New route'),
                   "routes/edit?link=$id",
                   array('title' => __('Associate new route'),
                         'class' => 'action_create nav_edit'));
}

function button_add_outing($id)
{
    return link_to(__('New outing'),
                   "outings/edit?link=$id",
                   array('title' => __('Associate new outing'),
                         'class' => 'action_create nav_edit'));
                         
}

function button_wizard($options = array())
{
    use_helper('ModalBox');

    if (!isset($options['title']))
    {
        $options['title'] = __('Create new outing with some help');
    }

    if (isset($options['url']))
    {
        $url = $options['url'];
        unset($options['url']);
    }
    else
    {
        $url = 'outings/wizard';
    }

    if (!empty($html_options))
    {
        $options = array_merge($options, $html_options);
    }
    return m_link_to(__('Create new outings'), $url, $options, array('width' => 600));
}

function button_print()
{
    use_helper('Javascript');
    return link_to_function(__('Print'),
                            'window.print()',
                            array('title' => __('Print current document'),
                                  'class' => 'action_print nav_edit'));
}

function button_report()
{
    use_helper('Forum');
    $mod_user_id = (sfContext::getInstance()->getUser()->getCulture() == 'it')
                    ? sfConfig::get('app_moderator_it_user_id') : sfConfig::get('app_moderator_user_id');
    return f_link_to(__('Report problem'),
                     'misc.php?email=' . $mod_user_id . '&doc=' . urlencode($_SERVER['REQUEST_URI']),
                     array('title' => __('Report problem'),
                           'class' => 'action_report nav_edit'));
}

function button_help($id = 'help_guide')
{
    return link_to(__('Help'),
                   getMetaArticleRoute($id),
                   array('title' => __('Get help'),
                         'class' => 'action_help nav_edit'));
}

function button_anchor($label, $anchor, $class)
{
    return '<a href="#'.$anchor.'" title="'.__($label).'" class="'.$class.' link_nav_anchor">'.__($label).'</a>';
}

function button_know_more()
{
    return link_to(__('Know more'), getMetaArticleRoute('know_more'));
}

function button_share()
{
    // addthis / analytics integration with asynchronous snippet
    // see http://support.addthis.com/customer/portal/articles/381260-google-analytics-integration
    sfContext::getInstance()->getResponse()->setParameter('addthis', true, 'helper/asset/addthis');
    $addthis_js = '<script type="text/javascript">
var addthis_config = {services_exclude: "print, favorites",ui_header_color: "#000000",ui_header_background: "#d2cabc",
data_ga_property: "'.sfConfig::get('app_ganalytics_key').'",data_ga_social: true};
var addthis_localize = {share_caption:"'.__('Bookmark & Share').'",more:"'.__('More...').'"};
</script>';
    return $addthis_js.link_to('<span class="share_bookmark '.__('meta_language') .'"></span>',
                               'http://www.addthis.com/bookmark.php',
                                array('class' => 'addthis_button'));
}

function button_widget($parameters)
{
    $paramstring = '';
    foreach ($parameters as $param => $value)
    {
        if ($param != 'module' && $param != 'action')
        {
            $paramstring .= '&' . $param . '=' . $value;
        }
    }
    return link_to(__('Generate widget'), '@widget_generator',
                   array('title' => __('Generate widget'),
                         'class' => 'picto_tools nav_edit',
                         'query_string' => 'mod=' . $parameters['module'] . $paramstring,
                         'onclick' => "$.modalbox.show({remote:'" . url_for('@widget_generator') . '?mod=' . $parameters['module'] . $paramstring 
                                      . "',title:this.title,width:710});return false;",
                         'rel' => 'nofollow'));
}

function buttons_facebook_twitter_c2c()
{
    return '<a href="http://www.facebook.com/pages/Camptocamporg/175865161596" '
           . 'title="' . __('Camptocamp on Facebook') . '">'
           . '<span id="facebook_logo"></span></a>&nbsp;'
           . '<a href="http://twitter.com/camptocamporg" '
           . 'title="' . __('Camptocamp on Twitter') . '">'
           . '<span id="twitter_logo"></span></a>&nbsp;'
           . '<a href="https://plus.google.com/104270548458536561874/" '
           . 'title="' . __('Camptocamp on Google+') . '" rel="publisher">'
           . '<span id="googleplus_logo"></span></a>';
}

function button_map($module)
{
    return content_tag('a', __('Map tool'),
                   array('href' => "/map?layerNodes=$module",
                         'title' => __('Map tool'),
                         'class' => 'picto_maps nav_edit',
                         'rel' => 'nofollow'));
}

function getMetaArticleRoute($name, $use_lang = false, $anchor = null)
{
    if (is_int($name))
    {
        $meta_article_id = $name;
    }
    else
    {
        $meta_article_id = sfConfig::get("app_meta_articles_$name");
        if (empty($meta_article_id))
        {
            return '@homepage';
        }
    }
    
    if ($use_lang)
    {
        $lang = sfContext::getInstance()->getUser()->getCulture();
        return empty($anchor) ? "@document_by_id_lang?module=articles&id=$meta_article_id&lang=$lang"
                              : "@document_by_id_lang?module=articles&id=$meta_article_id&lang=$lang#$anchor";
    }
    else
    {
        return empty($anchor) ? "@document_by_id?module=articles&id=$meta_article_id"
                              : "@document_by_id?module=articles&id=$meta_article_id#$anchor";
    }
}
