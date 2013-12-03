<?php
/**
 * Link helpers
 * Provide shortcuts to link pages with specials parameters
 *
 * @version $Id: LinkHelper.php 2338 2007-11-14 14:04:44Z alex $
 */
 
use_helper('ModalBox', 'WikiTabs');
 
function signup_link_to()
{
    return m_link_to(__('Signup'), '@signUp',
                     array('title' => __('Signup Form')),
                     array('width' => 600));
}

function login_link_to()
{
    return m_link_to(__('Login'), '@login', 
                     array('title' => __('Log in Camptocamp.org')));
}

function forgot_link_to()
{
    return m_link_to(__('password forgotten?'), 'users/lostPassword',
                     array('title' => __('Retrieve Forgotten Password')));
}

function customize_link_to()
{
    return m_link_to(__('Customize'), 'users/customize',
                     array('title' => __('Customize the site')),
                     array('width' => 700));
}

function personal_preferences_link_to()
{
    return m_link_to(__('My preferences'), '@user_edit',
                     array('title' => __('My preferences')),
                     array('width' => 700));
}

function language_preferences_link_to()
{
    return m_link_to(__('Set languages preferences'), 'users/sortPreferedLanguages',
                     array('title' => __('Set your prefered language order')),
                     array('width' => 700));
}

function customization_nav($active_tab)
{
    use_helper('Forum');
    $context = sfContext::getInstance();
    $id = $context->getUser()->getId();
    
    if($context->getUser()->isConnected() && $context->getRequest()->isXmlHttpRequest())
    {
        return '<ul class="tabs">' .
               '  <li ' . setActiveIf('customize', $active_tab). '>' . customize_link_to() . '</li>' .
               '  <li ' . setActiveIf('personal', $active_tab). '>'. personal_preferences_link_to() .'</li>' . 
               '  <li ' . setActiveIf('langpref', $active_tab). '>'. language_preferences_link_to() . '</li>' .
               '  <li>' . f_link_to(__('User profile'), "profile.php?section=personality&id=$id") . '</li>' .
               '</ul>';
    }
}

function absolute_link($url = '', $static = false)
{
    if ($static)
    {
        $static_base_url = sfConfig::get('app_static_url');
        if (!empty($static_base_url))
        {
            return $static_base_url . $url;
        }
    }

    $request = sfContext::getInstance()->getRequest();
    $http = $request->isSecure() ? 'https://' : 'http://';
    return $http . $request->getHost() . $url;
}

function absolute_link_to($name, $url = null, $html_options = null)
{
    $url = absolute_link($url);
    return link_to($name, $url, $html_options);
}

// TODO this can probably be enhanced
function phone_link($phone = '')
{
    if (!empty($phone) && c2cTools::mobileVersion())
    {
        $simple_phone = preg_replace('/\(0\)/', '', str_replace(array(' ', '.'), '', $phone));

        // if number is not only digits,+,- do not try to present it as a link
        if (!ereg('[0-9\+-]+', $simple_phone)) return $phone;

        $link = content_tag('a', $phone, array('href' => 'tel:'.$simple_phone));
        return $link;
    }
    else
    {
        return $phone;
    }
}

function generate_path()
{
    $sf_context = sfContext::getInstance();
    $module = $sf_context->getModuleName();
    $action = $sf_context->getActionName();
    $forum = (bool)(strstr($sf_context->getRequest()->getUri(), 'forums'));

    if ($action == 'home')
    {
        return '';
    }

    $path = __('Context:') . ' ' . link_to(__('Home'), '@homepage');

    if ($forum)
    {
        use_helper('Forum');
        $path .= ' &gt; ' . f_link_to(__('Forum'), '?lang='. $sf_context->getUser()->getCulture());
    }
    elseif ($module != 'documents')
    {
        $path .= ' &gt; ' . link_to(ucfirst(__($module)), "@default_index?module=$module");
    }

    return '<nav ' . ($forum ? '' : 'itemprop="breadcrumb" ') . 'id="path">' . $path . '</nav>';
}

function list_link($item, $module, $prefix = null)
{
    return link_to(isset($prefix) ? $prefix . __('&nbsp;:') . ' ' . $item['name'] : $item['name'],
                     "@document_by_id_lang_slug?module=$module&id=" . $item['id']
                   . '&lang=' . $item['culture'] . '&slug=' . make_slug(isset($prefix) ? $prefix . '-' . $item['name'] : $item['name']),
                   array('hreflang' => $item['culture']));
}

function jsonlist_url($item, $module, $prefix = null)
{
    return absolute_link(url_for("@document_by_id_lang_slug?module=$module&id=" . $item['id']
               . '&lang=' . $item['culture'] . '&slug=' . make_slug(isset($prefix) ? $prefix . '-' . $item['name'] : $item['name'])));
}
