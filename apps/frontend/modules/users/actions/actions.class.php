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
        parent::executeView();

        if (!$this->document->isArchive())
        {
            $this->getResponse()->addMeta('robots', 'noindex, follow');

            $whattoselect = 'd.document_id, d.culture, d.version, d.nature, d.created_at, ' .
                            'i.name, a.module, ' .
                            'h.comment, h.is_minor';
            $this->contribs = Document::listRecent('Document', 10,
                                                   $this->document->get('id'), null, null, 'editions',
                                                   false, null, $whattoselect, null, false);
                                                    
            // FIXME: put limit in query instead of slicing results
            $associated_outings = array_slice(array_reverse(array_filter($this->associated_docs, 
                                                                         array('c2cTools', 'is_outing')
                                                                         ), 
                                                            true
                                                            ), 
                                              0, 
                                              sfConfig::get('app_users_outings_limit')
                                              );
                                              
            $this->associated_outings = Document::fetchAdditionalFieldsFor(
                                                $associated_outings, 
                                                'Outing', 
                                                array('date', 'activities', 'height_diff_up'));
        }
        else
        {
            $this->updateDocName($this->document, $this->getRequestParameter('id'));
        }
    }

    /**
     * Set document name with user "prefered name to use" info.
     */
    protected function updateDocName($document, $id)
    {
        $document->set('name', UserPrivateData::find($id)->getSelectedName());
    }

    /**
     * Executes secure action.
     * Executed when a user hasn't enough credentials
     */
    public function executeSecure()
    {
        $this->setErrorAndRedirect('You do not have the credentials to execute this action',
                                   $this->getRequest()->getReferer());
    }

    /**
     * Executes savefilters action
     *
     */
    public function executeSavefilters()
    {
        // save language filter preferences
        $filtered_languages = $this->getRequestParameter('language_filter');
        c2cPersonalization::saveFilter(sfConfig::get('app_personalization_cookie_languages_name'), $filtered_languages);
        
        // save activity filter preferences
        $filtered_activities = $this->getRequestParameter('activities_filter');
        c2cPersonalization::saveFilter(sfConfig::get('app_personalization_cookie_activities_name'), $filtered_activities);
        
        // save ranges filter preferences
        $filtered_places = $this->getRequestParameter('places_filter');
        c2cPersonalization::saveFilter(sfConfig::get('app_personalization_cookie_places_name'), $filtered_places);
        
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
        if ($user->isConnected())
        {
            $this->setNoticeAndRedirect('You are already connected !', '@homepage');
        }
    
        if ($this->getRequest()->getMethod() != sfRequest::POST )
        {
            // display login form

            // if we came here from a redirection due to insufficient credentials, display a flash info :
            $uri = $this->getRequest()->getUri();
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
        }
        else
        {
            // control if the user exists
            $login_name = strtolower($this->getRequestParameter('login_name'));
            $password = $this->getRequestParameter('password');

            if (!$user->signIn($login_name, $password, $this->getRequestParameter('remember'), false))
            {
                // if not error message
                $this->setError('Username and password do not match, please try again');
            }
            else
            {
                // session is opened, user interface personalization
                $i18n_vars = array('%1%' => $user->getUsername());
                $this->setNotice('Welcome %1%', $i18n_vars);
            }

            // redirect to requested page
            $this->redirect($this->getRequest()->getReferer());
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
        if ($this->getUser()->isConnected())
        {
            // user is connected thus doesn't need to signup
            $this->redirect($this->getRequest()->getReferer());
        }
        else
        {
            // user isn't connected

            if ($this->getRequest()->getMethod() == sfRequest::POST)
            {
                $login_name = $this->getRequestParameter('login_name');
                $email = $this->getRequestParameter('email');

                // generate password
                $password = UserPrivateData::generatePwd();

                if ($this->getUser()->signUp($login_name, $password, $email))
                {
                    // sign up is OK
                    $this->getRequest()->setAttribute('password', $password);
                    $this->getRequest()->setAttribute('login_name', strtolower($login_name));

                    // send a confirmation email
                    $this->sendC2cEmail($this->getModuleName(), 'messageSignupPassword',
                                        $this->__('signup email title'), $email);

                    // display a confirmation message
                    $msg = 'Thanks for signing up. You should receive an email with your password soon';
                    $referer = $this->getRequest()->getReferer();
                    $redirect = (strstr($referer,'signUp')) ? '@homepage' : $referer;
                    return $this->setNoticeAndRedirect($msg, $redirect);
                }
                else
                {
                    return $this->setErrorAndRedirect('Sign up failed, please try again', '@signUp');
                }
            }
            else
            {
                // display form
                $g = new Captcha();
                $this->getUser()->setAttribute('captcha', $g->generate());
                $this->setPageTitle($this->__('Signup'));
            }
        }
    }

    public function executeLostPassword()
    {
        if ($this->getRequest()->getMethod() == sfRequest::GET )
        {
            // display a form to send a new passord to the user
        }
        else
        {
            $loginNameOrEmail = $this->getRequestParameter('loginNameOrEmail');

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

                return $this->setNoticeAndRedirect('Your password has been reset, check your email',
                                                   '@homepage');
            }
            else
            {
                // failed
                return $this->setErrorAndRedirect('User not found, please retry',
                                                  'users/lostPassword');
            }
        }
    }

    public function executeMessageResetPassword()
    {
        // some code for template if needed
        $this->password = $this->getRequest()->getAttribute('password');
        $this->login_name = $this->getRequest()->getAttribute('login_name');
    }

    public function executeMessageSignupPassword()
    {
        // some code for template if needed
        $this->password = $this->getRequest()->getAttribute('password');
        $this->login_name = $this->getRequest()->getAttribute('login_name');
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
            $email = $this->getRequestParameter('email');
            $password = $this->getRequestParameter('password');
            $nickname = $this->getRequestParameter('edit_nickname');
            $fullname = $this->getRequestParameter('edit_full_name');

            $conn = sfDoctrine::Connection();
            try
            {
                if (!empty($password))
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
    
                if ($fullname != $user_private_data->getPrivateName())
                {
                    $user_private_data->setPrivateName($fullname);
                }
    
                // set the name to use in guidebook
                $user_choice = $this->getRequestParameter('name_to_use');
                $user_private_data->setNameToUse($user_choice[0]);
    
                $user_private_data->save();
            
                $conn->commit();
            }
            catch (Exception $e)
            {
                $conn->rollback();
            }

            // update user session
            $this->getUser()->setAttribute('username', $user_private_data->get($user_choice[0]));

            // little js update
            if($this->isAjaxCall())
            {
                sfLoader::loadHelpers(array('Javascript', 'Tag'));
                // update the name to use (after the welcome)
                $js = javascript_tag( "$('name_to_use').update('" .
                                      $user_private_data->get($user_private_data->getNameToUse()) .
                                      "')"
                      );
            }
            else
            {
                $js = "";
            }
            
            if (!empty($password))
            {
                Punbb::signIn($user_private_data->getId(), $user_private_data->password);
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
        $url_to_redirect = $url_from;

        // set the user culture
        $user = $this->getUser();
        $user->setCulture($lang);

        // if user is connected, save his prefered language
        if($user->isConnected())
        {
            $user->setPreferedLanguage($lang);
        }
        else
        {
            $expire = time() + 31536000; // FIXME: good value?
            setcookie('language', Language::translateForPunBB($lang), $expire, '/');
        }

        // redirect
        $this->redirect($url_to_redirect);
    }

    public function executeCustomize()
    {
        $prefered_cultures = $this->getUser()->getCulturesForDocuments();
        $ranges = Area::getRegions(1, $prefered_cultures); // array('1' => 'vercors', '2' => 'bauges');
        asort($ranges);
        $this->ranges = $ranges;
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
            }
            else
            {
                 Sympa::unsubscribe($listname, $this->email);
            }
        }

        $subscribedLists = Sympa::getSubscribedLists($this->email);
        $ml_list = array();
        foreach ($lists as $list)
        {
            $ml_list[$list] = in_array($list, $subscribedLists);
        }

        $this->lists = $ml_list;
    }
    
    public function executeMerge()
    {
        $referer = $this->getRequest()->getReferer();
        $this->setErrorAndRedirect('Users merging is prohibited', $referer);
    }

    protected function getSortField($orderby)
    {   
        switch ($orderby)
        {
            case 'unam': return 'mi.search_name';
            case 'anam': return 'ai.name';
            default: return NULL;
        }
    }

    protected function getListCriteria()
    {
        $conditions = $values = array();

        if ($areas = $this->getRequestParameter('areas'))
        {
            Document::buildListCondition($conditions, $values, 'ai.id', $areas);
        }

        if ($uname = $this->getRequestParameter('unam'))
        {
            $conditions[] = 'mi.search_name LIKE remove_accents(?)';
            $values[] = "%$uname%";
        }

        if ($geom = $this->getRequestParameter('geom'))
        {
            Document::buildGeorefCondition($conditions, $geom);
        }

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }

    protected function filterSearchParameters()
    {
        $out = array();

        $this->addListParam($out, 'areas');
        $this->addNameParam($out, 'unam');
        $this->addParam($out, 'geom');

        return $out;
    }
}
