<?php
/**
 * users module actions.
 *
 * @package    c2corg
 * @subpackage users
 * @version    $Id: actions.class.php 2537 2007-12-19 21:34:13Z alex $
 */

class usersActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'User';    
    
    /**
     * Nb of dimensions for geom column
     */   
    protected $geom_dims = 2; 
    // by default, all documents are 3D (X, Y, Z)
    // exceptions are : 
    //      - users and areas : 2D (X, Y)
    //      - outings : 4D (X, Y, Z, T in traces)
    
    /**
     * Executes view action : displays the user profile
     */
    public function executeView()
    {
        $id = $this->getRequestParameter('id');

        parent::executeView();
        $hasPublicProfile = UserPrivateData::hasPublicProfile($id);

        if (!$this->getUser()->isConnected() && !$hasPublicProfile)
        {
            // page owner has not allowed anonymous users to access his personal page
            $this->setTemplate('login');
        }
        else
        {

            if (!$this->document->isArchive() && $this->document['redirects_to'] == NULL)
            {
                if ($hasPublicProfile)
                {
                    $this->getResponse()->addMeta('robots', 'index, follow');
                }
                else
                {
                    $this->getResponse()->addMeta('robots', 'noindex, nofollow');
                }
                

                // get associated outings
                $associated_outings = array();
                $nb_outings = count(Association::countAllLinked(array($id), 'uo'));
                $nb_outings_limit = 0;
                if ($nb_outings > 0)
                {
                    $outing_params = array('users' => $id);
                    $nb_outings_limit = sfConfig::get('app_users_outings_limit');
                    $associated_outings = Outing::listLatest($nb_outings_limit, array(), array(), array(), $outing_params, false, false);
                    $associated_outings = Language::getTheBest($associated_outings, 'Outing');
                }
                $this->nb_outings = $nb_outings;
                $this->nb_outings_limit = $nb_outings_limit;
                $this->associated_outings = $associated_outings;
                

                $forum_nickname = Punbb::getNickname($id);
                $this->forum_nickname = $forum_nickname[0]['username'];

                // check if user is forum and / or topoguide moderator
                $this->forum_moderator = UserPrivateData::isForumModerator($id);

                $user_permissions = Doctrine_Query::create()
                                      ->from('User.groups.permissions, User.permissions')
                                      ->where('User.id = ?', $id)
                                      ->execute(array(), Doctrine::FETCH_ARRAY);
                $topoguide_moderator = false;
                $moderator_credential = sfConfig::get('app_credentials_moderator');
                foreach ($user_permissions[0]['groups'] as $group)
                {
                    foreach ($group['permissions'] as $permission)
                    {
                         if ($permission['name'] == $moderator_credential)
                         {
                             $topoguide_moderator = true;
                             break 2;
                         }
                    }
                }
                foreach($user_permissions[0]['permissions'] as $permission)
                {
                     if ($permission['name'] == $moderator_credential)
                     {
                         $topoguide_moderator = true;
                         break;
                     }
                }
                $this->topoguide_moderator = $topoguide_moderator;
            }
            else
            {
                // only moderators and user itself should see archive versions of user docs
                $this->filterAuthorizedPeople($id);
            }
        }
    }

    protected function redirectIfSlugMissing($document, $id, $lang, $module = null)
    {
        // users URL do not contain slug, but we need to add lang if not present
        $this->redirect("@document_by_id_lang?module=users&id=$id&lang=$lang", 301);
    }

    public function executeDiff()
    {
        $id = $this->getRequestParameter('id');
        $this->filterAuthorizedPeople($id);
        parent::executeDiff();
    }

    public function executeHistory()
    {
        $id = $this->getRequestParameter('id');
        $this->filterAuthorizedPeople($id);
        parent::executeHistory();
    }

    /**
     * Executes secure action.
     * Executed when a user hasn't enough credentials
     */
    public function executeSecure()
    {
        $this->setErrorAndRedirect('You do not have enough credentials to perform this operation',
                                   $this->getRequest()->getReferer());
    }

    /**
     * Executes savefilters action
     *
     */
    public function executeSavefilters()
    {
        if ($this->getUser()->isConnected())
        {
            $user_id = $this->getUser()->getId();
        }
        else
        {
            $user_id = null;
        }

        $filters = array();

        // language filter preferences
        $filtered_languages = $this->getRequestParameter('language_filter');
        $filters[sfConfig::get('app_personalization_cookie_languages_name')] = $filtered_languages;
        
        // activity filter preferences
        $filtered_activities = $this->getRequestParameter('activities_filter');
        $filters[sfConfig::get('app_personalization_cookie_activities_name')] = $filtered_activities;
        
        // ranges filter preferences
        $filtered_places = $this->getRequestParameter('places_filter');
        $filters[sfConfig::get('app_personalization_cookie_places_name')] = $filtered_places;
        $filtered_places_type = $this->getRequestParameter('places_filter_type');
        $filters[sfConfig::get('app_personalization_cookie_places_type_name')] = array($filtered_places_type);

        // save filter preferences
        c2cPersonalization::saveFilters($filters, $user_id);

        sfLoader::loadHelpers(array('Javascript', 'Tag'));
        $js = javascript_tag("window.location.reload();");
        
        // if a new filter has been created, activate it :
        $this->getUser()->setFiltersSwitch(true);
        
        return $this->setNoticeAndRedirect('filter successfully saved', '@homepage', '', $js);
    }

    /**
     * Executes login action.
     */
    public function executeLogin()
    {
        $user = $this->getUser();
        $redirect_param = $this->getRequestParameter('redirect', '');
    
        if ($user->isConnected())
        {
            if (!empty($redirect_param))
            {
                $redirect_uri = $redirect_param;
            }
            else
            {
                $redirect_uri = '@homepage';
            }
            $this->setNoticeAndRedirect('You are already connected !', $redirect_uri);
        }
        
        if ($this->getRequest()->getMethod() != sfRequest::POST )
        {
            // display login form

            // if we came here from a redirection due to insufficient credentials, display a flash info :
            $uri = $this->getRequest()->getUri();
            if (strstr($uri, 'login') === false)
            {
                if (strstr($uri, 'edit'))
                {
                    if (strstr($uri, 'edit/')) // there is an id in the URI => page edition
                    {
                        $this->setWarning('You need to login to edit this page', NULL, false);
                    }
                    else // page creation
                    {
                        $this->setWarning('You need to login to create this page', NULL, false);
                    }
                }
                else
                {
                    $this->setWarning('You need to login to access to this page', NULL, false);
                }
            }
            elseif (!empty($redirect_param))
            {
                $this->setWarning('You need to login to access to this page', NULL, false);
            }
            
            $this->redirect_param = $redirect_param;
        }
        else
        {
            // control if the user exists
            $login_name = strtolower(trim($this->getRequestParameter('login_name')));
            $password = trim($this->getRequestParameter('password'));

            if (!$user->signIn($login_name, $password, $this->getRequestParameter('remember'), false))
            {
                // if not error message
                $this->setError('Username and password do not match, please try again');
                $this->statsdIncrement('failure');
            }
            else
            {
                // session is opened, user interface personalization
                $i18n_vars = array('%1%' => $user->getUsername());
                $this->setNotice('Welcome %1%', $i18n_vars);
                $this->statsdIncrement('success');
            }

            // redirect to requested page
            $referer = $this->getRequest()->getReferer();
            if (!empty($redirect_param)) // explicit redirection after login action
            {
                $redirect_uri = $redirect_param;
                
            }
            elseif ($referer && !empty($referer) && $referer !== $this->getController()->genUrl('@login', true))
            {
                $redirect_uri = $referer;
            }
            else
            {
                $redirect_uri = '@homepage';
            }
            
            $this->redirect($redirect_uri);
        }
    }

    /**
     * Executes logout action.
     */
    public function executeLogout()
    {
        $this->getUser()->signOut();
        // back to referer
        $this->setNoticeAndRedirect('Successfully logged out', $this->getRequest()->getReferer());
    }

    public function executeSignUp()
    {
        $redirect_param = $this->getRequestParameter('redirect', '');
        
        if ($this->getUser()->isConnected())
        {
            // user is connected thus doesn't need to signup
            $referer = $this->getRequestParameter('referer');
            if (!empty($redirect_param))
            {
                $redirect_uri = str_replace('_', '/', $redirect_param);
            }
            elseif ($referer && !empty($referer))
            {
                $redirect_uri = $referer;
            }
            else
            {
                $redirect_uri = '@homepage';
            }
            $this->setNoticeAndRedirect('You are already connected !', $redirect_uri);
        }
        else
        {
            // user isn't connected

            if ($this->getRequest()->getMethod() == sfRequest::POST)
            {
                $login_name = strtolower(trim($this->getRequestParameter('login_name')));
                $email = trim($this->getRequestParameter('email'));

                // generate a new password
                $password = UserPrivateData::generatePwd();

                if ($this->getUser()->signUp($login_name, $password, $email))
                {
                    // sign up is OK
                    $this->getRequest()->setAttribute('password', $password);
                    $this->getRequest()->setAttribute('login_name', $login_name);
                    $this->getRequest()->setAttribute('redirect', $redirect_param);

                    // send a confirmation email
                    $this->sendC2cEmail($this->getModuleName(), 'messageSignupPassword',
                                        $this->__('signup email title'), $email);

                    // display a confirmation message
                    $msg = 'Thanks for signing up. You should receive an email with your password soon';
                    $referer = $this->getRequest()->getReferer();
                    $redirect = (strstr($referer,'signUp')) ? '@homepage' : $referer;
                    $this->statsdIncrement('success');
                    return $this->setNoticeAndRedirect($msg, $redirect);
                }
                else
                {
                    $this->statsdIncrement('failure');
                    if (empty($redirect_param))
                    {
                        $redirect_uri = '@signUp';
                    }
                    else
                    {
                        $redirect_uri = url_for('@signUp', true).'?redirect='.$redirect_param;
                    }
                    return $this->setErrorAndRedirect('Sign up failed, please try again', $redirect_uri);
                }
            }
            else
            {
                // display form
                $this->redirect_param = $redirect_param;
                
                $g = new Captcha();
                $this->getUser()->setAttribute('captcha', $g->generate());
                $this->statsdIncrement('captcha');
                
                $this->setPageTitle($this->__('Signup'));
            }
        }
    }

    // not that we use a special field (password_tmp) because we don't want to override the legitimate password
    // we don't know if the user requesting a new password is the legitimate one!
    public function executeLostPassword()
    {
        if ($this->getRequest()->getMethod() == sfRequest::GET )
        {
            // display a form to send a new passord to the user
        }
        else
        {
            $loginNameOrEmail = trim($this->getRequestParameter('loginNameOrEmail'));

            // attend to retrieve user
            $user_private_data = UserPrivateData::retrieveByLoginNameOrEmail($loginNameOrEmail);

            // set success or error
            if ($user_private_data)
            {
                // successfuly retrieved
                $newpwd = UserPrivateData::generatePwd();
                $user_private_data->setPassword_tmp($newpwd);
                $user_private_data->save();

                $this->getRequest()->setAttribute('password', $newpwd);
                $this->getRequest()->setAttribute('login_name', $user_private_data->getLoginName());

                $this->sendC2cEmail($this->getModuleName(),
                                    'messageResetPassword',
                                    $this->__('lost password email title'),
                                    $user_private_data->getEmail());

                $this->statsdIncrement('reset');
                return $this->setNoticeAndRedirect('Your password has been reset, check your email',
                                                   '@homepage');
            }
            else
            {
                // failed
                $this->statsdIncrement('notfound');
                return $this->setErrorAndRedirect('User not found, please retry',
                                                  'users/lostPassword');
            }
        }
    }

    public function executeMessageResetPassword()
    {
        // some code for template if needed
        $this->password = $this->getRequest()->getAttribute('password');
        $this->login_name = trim($this->getRequest()->getAttribute('login_name'));
    }

    public function executeMessageSignupPassword()
    {
        // some code for template if needed
        $this->password = $this->getRequest()->getAttribute('password');
        $this->login_name = trim($this->getRequest()->getAttribute('login_name'));
        $this->redirect = trim($this->getRequest()->getAttribute('redirect'));
    }

    public function executeMyPage()
    {
        // redirect to user page if connected
        if($this->getUser()->isConnected())
        {
            $user_id = $this->getUser()->getId();
            $this->redirect('@document_by_id?module=users&action=view&id='.$user_id);
        }
        else
        {
            $this->redirect('@login');
        }
    }

    /**
     * Executes Edit action for user private data.
     */
    public function executeEditPrivateData()
    {
        $user_id = $this->getUser()->getId(); // logged user id
        if (!$user_private_data = UserPrivateData::find($user_id)) // logged user db object
        {
            $this->setNotFoundAndRedirect();
        }


        if ($this->getRequest()->getMethod() == sfRequest::POST)
        {
            // user private data update
            $email = trim($this->getRequestParameter('email'));
            $password = trim($this->getRequestParameter('password'));
            $nickname = trim($this->getRequestParameter('edit_nickname'));
            $nickname = preg_replace('#\s+#', ' ', $nickname);
            $toponame = trim($this->getRequestParameter('edit_topo_name'));
            $toponame = preg_replace('#\s+#', ' ', $toponame);
            $is_profile_public = $this->getRequestParameter('is_profile_public');

            $conn = sfDoctrine::Connection();
            try
            {
                if (!empty($password)) // a new password has been set
                {
                    $user_private_data->setPassword($password);
                }
    
                if (!is_null($email))
                {
                    $old_email = $user_private_data->getEmail();
                    if ($old_email != $email)
                    {
                        Sympa::updateEmail($old_email, $email);
                        $user_private_data->setEmail($email);
                    }
                }
    
                if ($nickname != $user_private_data->getUsername())
                {
                    $user_private_data->setUsername($nickname);
                }
    
                if ($toponame != $user_private_data->getTopoName())
                {
                    $user_private_data->setTopoName($toponame);
                }

                $user_private_data->setIsProfilePublic(!empty($is_profile_public));
    
                $user_private_data->save();
            
                $conn->commit();
                $this->statsdIncrement('success');

                // update cache
                $this->clearCache('users', $user_id, false, 'view');
            }
            catch (Exception $e)
            {
                $conn->rollback();
                $this->statsdIncrement('failure');
            }

            // update user session
            $this->getUser()->setAttribute('username', $user_private_data->get('topo_name'));

            // little js update
            if($this->isAjaxCall())
            {
                sfLoader::loadHelpers(array('Javascript', 'Tag'));
                // update the name to use (after the welcome)
                $js = javascript_tag("$('#name_to_use').html('" . $user_private_data->get('topo_name') . "')");
            }
            else
            {
                $js = "";
            }
            
            if (!empty($password))
            {
                Punbb::signIn($user_private_data->getId(), $user_private_data->password); // TODO
            }

            $lang = $this->getUser()->getCulture();
            return $this->setNoticeAndRedirect('Your private information have been successfully updated',
                                        "@document_by_id_lang?module=users&id=$user_id&lang=$lang", null, $js);
        }
        else
        {
            // display form
            //$this->user = $user;
            $this->user_private_data = $user_private_data;
            $this->setPageTitle($this->__('User account update') );
        }
    }

    // need a special handleError because there is data to set, else it use handleError from c2cActions
    public function handleErrorEditPrivateData()
    {
        if($this->isAjaxCall())
        {
            return $this->handleErrorForAjax();
        }
        else
        {
            $user_id = $this->getUser()->getId();
            $this->user_private_data = UserPrivateData::find($user_id);
            return sfView::SUCCESS;
        }
    }

    /**
     * This ajax function update user->private_data->prefered_language
     * It put a list, separated by "," this is used to know wich language
     * to display first
     */
    public function executeSortPreferedLanguages()
    {
        if($this->getRequest()->getMethod() == sfRequest::POST)
        {
            $this->getUser()->savePreferedLanguageList($this->getRequestParameter('order'));
            return $this->renderText($this->__('Prefered language successfully saved'));
        }
    }

    /**
     * Executes setCultureInterface action
     */
    public function executeSetCultureInterface()
    {
        // get lang
        $lang = $this->getRequestParameter('lang');

        // get referer URL
        $url_from = $this->getRequest()->getReferer();
        $url_to_redirect = empty($url_from) ? '@homepage' : $url_from;

        // set the user culture
        $user = $this->getUser();
        $user->setCulture($lang);

        // if user is connected, save his prefered language
        $user->setPreferedLanguage($lang);
        if (!$user->isConnected())
        {
            $expire = time() + 31536000; // FIXME: good value?
            setcookie('language', Language::translateForPunBB($lang), $expire, '/forums/');
        }

        // redirect
        $this->redirect($url_to_redirect);
    }

    public function executeCustomize()
    {
        //$prefered_cultures = $this->getUser()->getCulturesForDocuments();
        $area_type = intval($this->getRequest()->getCookie(sfConfig::get('app_personalization_cookie_places_type_name'), 1));
        $this->ranges = $this->getAreas($area_type, false);
        $this->area_type = $area_type;
    }

    public function executeManageimages()
    {
        if (!$this->getUser()->isConnected())
        {
            $referer = $this->getRequest()->getReferer();
            $this->setErrorAndRedirect('You need to login to access this page', $referer);
        }
        $user_id = $this->getUser()->getId(); // logged user id
        $this->pager = new c2cDoctrinePager('Image', (c2cTools::mobileVersion() ? sfConfig::get('app_list_mobile_maxline_number')
                                                                                 : sfConfig::get('app_list_maxline_number')));
        $q = $this->pager->getQuery();
        $q->select('i.id, i.filename, i.image_type, ii.name, ii.culture')
          ->from('Image i')
          ->leftJoin('i.associations a ON i.id = a.linked_id')
          ->leftJoin('i.ImageI18n ii')
          ->leftJoin('i.versions v')
          ->leftJoin('v.history_metadata hm');
        $where = 'i.image_type = 2 AND v.version = 1 AND hm.user_id = ?';
        
        $document_type = $this->getRequestParameter('dtyp');
        if (!empty($document_type))
        {
            if ($document_type <= 1)
            {
                $types = array('ai', 'mi', 'bi', 'hi', 'pi', 'ri', 'ti', 'si');
            }
            else
            {
                $types = array('oi', 'ui');
            }
            $where .= " AND a.type IN ( '" . implode("', '", $types) . "' )";
        }
        else
        {
            $document_type = $this->getRequestParameter('ctyp');
            if (!empty($document_type))
            {
                $q->leftJoin('a.Article c');
                if ($document_type <= 1)
                {
                    $document_type = 1;
                }
                else
                {
                    $document_type = 2;
                }
                $where .= " AND a.type = 'ci' AND c.article_type = $document_type";
            }
        }
        
        $q->where($where, array($user_id));
        $q->orderBy('i.id DESC');
        $page = $this->getRequestParameter('page', 1);
        $this->pager->setPage($page);
        $this->pager->init();
        $this->page = $page;

        if ($this->getRequest()->getMethod() == sfRequest::POST)
        {
            // images management
            $switch = $this->getRequestParameter('switch');
            $lang = $this->getUser()->getCulture();
            if (empty($switch))
            {
                return $this->setNoticeAndRedirect('No image has been edited',
                                                   "/users/manageimages?module=users&page=$page");
            }
            $conn = sfDoctrine::Connection();
            $conn->beginTransaction();

            $history_metadata = new HistoryMetadata();
            $history_metadata->setComment('Switch to collaborative license');
            $history_metadata->set('is_minor', true);
            $history_metadata->set('user_id', $user_id);
            $history_metadata->save();
            foreach ($switch as $image_id)
            {
                // verify id corresponds to an image created by the user
                $img = Doctrine_Query::create()
                       ->select('i.id')
                       ->from('Image i')
                       ->leftJoin('i.versions v')
                       ->leftJoin('v.history_metadata hm')
                       ->where('v.version = 1 AND hm.user_id = ? AND i.id = ?', array($user_id, $image_id))
                       ->execute();
                if (empty($img))
                {
                  $conn->rollback();
                  return $this->setNoticeAndRedirect('You do not have the right to change the license of theses images',
                                                     "/users/manageimages?module=users&page=$page");
                }
                $db_doc = Document::find('Image', $image_id);
                $db_doc->set('image_type', 1);
                $db_doc->save();
                // clear cache
                $this->clearCache('images', $image_id, false, 'view');
                $associated_docs = Association::findAllAssociatedDocs($image_id, array('id', 'module'));
                foreach ($associated_docs as $doc)
                {
                    // clear their view cache
                    $this->clearCache($doc['module'], $doc['id'], false, 'view');
                }
            }

            // apply modifications if everything went fine
            $conn->commit();

            return $this->setNoticeAndRedirect('Your images have been successfully updated',
                                               "/users/manageimages?module=users&page=$page");
        }
        else
        {
            // display form
            $this->setPageTitle($this->__('User image management') );
        }
    }

    /**
     * Filter for people who have the right to edit current document 
     * (linked people for outings, original editor for articles...).
     * Overrides the one in parent class.
     */
    protected function filterAuthorizedPeople($id)
    {
        // we know here that document $id exists and that its model is the current one (User). 
        $user = $this->getUser();
        if ($user->getId() != $id && !$user->hasCredential('moderator'))
        {
            $referer = $this->getRequest()->getReferer();
            $this->setErrorAndRedirect('You cannot edit another user personal data', $referer);
        }
    }
    
    /**
     * Handle a page that enables users to registers to various mailing lists.
     */
    public function executeMailinglists()
    {
        $user_id = $this->getUser()->getId();
        $this->user_private_data = UserPrivateData::find($user_id);
        $this->email = $this->user_private_data->get('email');

        $lists = sfConfig::get('mod_users_mailinglists_values');
        
        if ($this->getRequest()->getMethod() == sfRequest::POST)
        {
            $listname = $this->getRequestParameter('listname');
            if ($this->getRequestParameter('reason') == 'sub')
            {
                 Sympa::subscribe($listname, $this->email);
                 $this->statsdIncrement("$listname.subscribe");
            }
            else
            {
                 Sympa::unsubscribe($listname, $this->email);
                 $this->statsdIncrement("$listname.unsubscribe");
            }
        }

        $subscribedLists = Sympa::getSubscribedLists($this->email);
        $ml_list_subscribed = array();
        $ml_list_available = array();
        foreach ($lists as $list)
        {
            if (in_array($list, $subscribedLists))
            {
                $ml_list_subscribed[] = $list;
            }
            else {
                $ml_list_available[] = $list;
            }
        }

        $this->available_lists = $ml_list_available;
        $this->subscribed_lists = $ml_list_subscribed;

        $this->setPageTitle($this->__('mailing lists'));
    }
    
    public function executeMerge()
    {
        $referer = $this->getRequest()->getReferer();
        $this->setErrorAndRedirect('Users merging is prohibited', $referer);
    }

    public function executeSavePref()
    {
        if (!$this->hasRequestParameter('name') ||
            !$this->hasRequestParameter('value') ||
            !$this->getUser()->isConnected())
        {
            return $this->ajax_feedback('');
        }

        $pref_name = $this->getRequestParameter('name');
        $pref_value = $this->getRequestParameter('value');

        // if not forum categories, it is a section
        // check if section exists
        if ($pref_name != sfConfig::get('app_personalization_cookie_forum_categories_name')) {
            $valid_prefs = sfConfig::get('app_personalization_cookie_fold_positions');
            if (!in_array(substr($pref_name, 0, -12), $valid_prefs))
            {
                return $this->ajax_feedback('');
            }
        }

        c2cPersonalization::saveFilter($pref_name, $pref_value,
                                       $this->getUser()->getId(),
                                       false); // cannot save cookie in ajax, done via js

        return $this->renderText('');
    }

    public function executePopup()
    {
        $id = $this->getRequestParameter('id');

        // if user is not connected, we don't want to display user's popup
        if (!$this->getUser()->isConnected() && !UserPrivateData::hasPublicProfile($id))
        {
            $this->raw = $this->getRequestParameter('raw', false);
            if ($this->raw)
            {
                $this->setLayout(false);
            }

            // deactivate automatic inclusion of js and css files by symfony
            $response = $this->getResponse();
            $response->setParameter('javascripts_included', true, 'symfony/view/asset');
            $response->setParameter('stylesheets_included', true, 'symfony/view/asset');
            $this->setCacheControl();

            // we call users/popupError template
            return sfView::ERROR;
        }
        else
        {
            parent::executePopup();
        }
    }

    protected function filterSearchParameters()
    {
        $out = array();

        $this->addListParam($out, 'areas');
        $this->addAroundParam($out, 'uarnd');
        $this->addNameParam($out, 'unam');
        $this->addNameParam($out, 'ufnam');
        $this->addNameParam($out, 'utfnam');
        $this->addListParam($out, 'act');
        $this->addListParam($out, 'ucat');
        $this->addParam($out, 'geom');

        return $out;
    }

}
