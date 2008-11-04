<?php
/**
 * User management customization.
 * @version $Id: myUser.class.php 2476 2007-12-05 12:46:40Z fvanderbiest $
 */

class myUser extends sfBasicSecurityUser
{
    protected $dummy_culture = 'xx';

    /**
     * @see sfBasicSecurityUser::initialize()
     */
    public function initialize($context, $parameters = array())
    {
        // if "temp_remember" cookie is set, update lastRequest time to now to make sure
        // that user session is not considered too old
        if (sfContext::getInstance()->getRequest()->getCookie('temp_remember'))
        {
            $this->lastRequest = time();
        }

        // Dirty hack to avoid that too much code is executed in order to detect
        // the user culture at that point (in sfUser::initialize()) since a more
        // comprehensive detection is performed at the end of this method.

        $storage = $context->getStorage();

        // Saves current culture retrieved from session (if any)
        $saved_culture = $storage->read(self::CULTURE_NAMESPACE);
        // and temporary writes a dummy one.
        $storage->write(self::CULTURE_NAMESPACE, $this->dummy_culture);

        parent::initialize($context, $parameters);

        // Restores previously saved culture
        $storage->write(self::CULTURE_NAMESPACE, $saved_culture);
        // before finally performing the real culture detection:
        $this->setCulture($this->detectCulture());
    }

    /**
     * @see sfUser::setCulture()
     */
    public function setCulture($culture)
    {
        // dummy culture is a fake language code indicating that culture setting must be
        // bypassed at this point.
        if ($culture == $this->dummy_culture)
        {
            return;
        }

        parent::setCulture($culture);
    }

    /**
     * Tries to determine what culture to use to display the website.
     * Reuses some of the code of sfUser::initialize()
     * @see sfUser::initialize()
     * @return string
     */
    protected function detectCulture()
    {
        // In this project, URLs never contain interface culture parameters.
        // So it's no use trying to get this info from the URL

        // Tries to get culture from session
        if (!($culture = $this->context->getStorage()->read(self::CULTURE_NAMESPACE)))
        {
            // else tries to get culture from browser accepted languages header
            if (!($culture = $this->getCultureFromAcceptedLanguages()))
            {
                // else uses config default culture
                $culture = sfConfig::get('sf_i18n_default_culture', 'en');
            }
        }

        return $culture;
    }

    /**
     * Gets culture from browser accepted languages header.
     * @return string
     */
    protected function getCultureFromAcceptedLanguages()
    {
        $accepted_languages = $this->context->getRequest()->getLanguages();
        $project_languages  = array_keys(sfConfig::get('app_languages_c2c'));

        $user_languages = array();
        foreach ($accepted_languages as $lang)
        {
            $lang = substr($lang, 0, 2);
            if (!in_array($lang, $user_languages))
            {
                $user_languages[] = $lang;
            }
        }

        $languages = array_intersect($user_languages, $project_languages);
        // returns first possible language. NULL if list is empty.
        return array_shift($languages);
    }


    /**
     * Returns a list of cultures by order of preference for document reading
     * ... whether the user is connected or not.
     *
     * @return array
     */
    public function getCulturesForDocuments()
    {
        if ($this->isConnected()) // user is logged, and has set a list of prefered languages.
        {
            $languages = $this->getPreferedLanguageList();
        }
        else // user is not connected, surfing anonymously on the web site
        {
            $il_array = array(0 => $this->getCulture());
            // we get the project languages ordered by "logical" preference.
            $project_languages = array_keys(Language::getAll());
            // we suppress the current interface language from this list :
            $tmparray = array_diff($project_languages, $il_array);
            // we reconstruct an array of preferences, the first one being the current interface language.
            $languages = array_merge($il_array, $tmparray);
        }
        return $languages;
    }

    public function signIn($login_name, $password, $remember = false, $password_is_hashed = false)
    {
        c2cTools::log('in signin function from myUser class');

        $return = false;

        $hashed_pwd = Punbb::punHash($password);

        if ($password_is_hashed)
        {
            $user = User::retrieve($login_name, $password);
        }
        else
        {
            $user = User::retrieve($login_name, $hashed_pwd);
        }

        if (!$user && !$password_is_hashed)
        {
            // This block is not used when password is hashed. Indeed password is hashed only
            // when performing an automatic signIn ("remember me").
            // In that case, no temp password is used.
            
            c2cTools::log('base login failed, start trying with password_temp');

            // user not found, try with tmp password
            $user = User::retrieveTmp($login_name, $hashed_pwd);

            if ($user)
            {
                c2cTools::log('user found, make temp password the new password');

                // user used his tmp password
                $user_private_data = $user->get('private_data');
                // set password to tmp password
                $user_private_data->set('password', $password);
                // delete tmp password
                $user_private_data->set('password_tmp', null);

                $user->save();
            }
        }

        if ($user)
        {
            c2cTools::log('user found, continue to test if active');

            if ($user->isActive())
            {
                c2cTools::log('user is active');

                $user_id = $user->get('id');
                $user_culture = $user->get('private_data')->getPreferedCulture();

                // when user signs-in it confirms his signup
                if ($user->isConfirmationPending())
                {
                    c2cTools::log('remove user from pending group');
                    $user->removeFromGroup('pending');
                }

                // login punbb
                if ($password_is_hashed)
                {
                    Punbb::signIn($user_id, $password);
                }
                else
                {
                    Punbb::signIn($user_id, $hashed_pwd);
                }
                
                c2cTools::log('logged in punbb');

                // remember?
                if ($remember)
                {
                    c2cTools::log('remember me requested');

                    // TODO : move remove old keys in a batch
                    // remove old keys
                    RememberKey::deleteOldKeys();

                    // remove other keys from this user (and only matching this IP ? what about the people whose IP changes every day ?)
                    //RememberKey::deleteOtherKeysForUserId($user_id);
                    // this is no more useful, since key is now specific to user (see warning below).
                    
                    // generate new keys
                    //$key = $this->generateRandomKey();
                    // this was wrong : the remember key should be specific at least to this user, not random.
                    // because logging oneself to another machine and invoking remember_me would cause losing remember_me on the first machine
                    // yet, someone might want to be auto-identified on several computers (@home, @work ...)
                    
                    // (warning : this is a potential security breach for users if they use a shared computer)
                    $key = md5($user->get('private_data')->get('password') . sfConfig::get('app_remember_key_random') . $user_id);
                    // using id+pwd, it should be unique for each user.

                    // save key if it does not already exist
                    if (!RememberKey::existsKey($key))
                    {
                        $rk = new RememberKey();
                        $rk->set('remember_key', $key);
                        $rk->set('user', $user);
                        $rk->set('ip_address', $_SERVER[ 'REMOTE_ADDR' ] );
                        $rk->save();
                    }

                    // make key as a cookie
                    $remember_cookie = sfConfig::get('app_remember_key_cookie_name', 'c2corg_remember');
                    $expiration_age = sfConfig::get('app_remember_key_expiration_age', 30 * 24 * 3600);
                    sfContext::getInstance()
                                 ->getResponse()
                                 ->setCookie($remember_cookie, $key, time() + $expiration_age);
                }
                else
                {
                    // user is authenticated but has not checked "remember me" option
                    // let's add a cookie to indicate his/her session should not be reset while his/her browser is open
                    sfContext::getInstance()->getResponse()->setCookie('temp_remember', 1);
                }

                c2cTools::log('add some information in user session');

                // give credentials
                $this->addCredentials($user->getAllPermissionNames());

                // login session symfony
                $this->setAttribute('username', $user->get('private_data')->get('topo_name'));
                $this->setAttribute('id', $user_id);

                // set the prefered language for user session
                // and the list of languages ordered by preference
                $this->saveLanguageListInSession($user->get('private_data')->getDocumentCulture());

                // set logged
                $this->setAuthenticated(true);
                $return = true;

                // change language session if needed
                if ($this->getCulture() != $user_culture)
                {
                    $this->setCulture($user_culture);
                }
            }
        }

        return $return;
    }

    protected function generateRandomKey( $len = 20 )
    {
        $string = '';
        $pool = 'abcdefghijklmnopqrstuvwzyzABCDEFGHIJKLMNOPQRSTUVWZYZ0123456789';
        for ( $i = 1; $i <= $len; $i++ )
        {
            $string .= substr( $pool, rand( 0, 61 ), 1 );
        }

        return md5( $string );
    }

    public function signOut()
    {
        // remove cookie if exist
        $remember_cookie = sfConfig::get( 'app_remember_key_cookie_name', 'c2corg_remember' );
        sfContext::getInstance()->getResponse()->setCookie($remember_cookie, '');
        sfContext::getInstance()->getResponse()->setCookie('temp_remember', '');

        // delete attributes in session == remove credentials
        $this->getAttributeHolder()->clear();

        // quit punbb session
        Punbb::signOut();

        // remove logged
        $this->setAuthenticated(false);
    }

    public function signUp($login_name, $password, $email)
    {
        // to improve DB access it's better to pass id for object save
        // save user
        $user = new User();
        $user->setCulture($this->getCulture());
        $user->setName($login_name); // absolutely needed for i18n record creation here !

        // Get data from YML config file.
        $master_user_id = sfConfig::get('app_user_creation_master_id');
        $is_minor = sfConfig::get('app_user_creation_is_minor');
        $comment = sfConfig::get('app_user_creation_comment');

        $user->doSaveWithMetadata($master_user_id, $is_minor, $comment);

        // save private data
        $private_data = new UserPrivateData();
        $private_data->setLoginName(strtolower($login_name));
        $private_data->setUsername($login_name); // username is used as nickname in forum, need to be set
        $private_data->setTopoName($user->getName());
        $private_data->setPassword($password);
        $private_data->setEmail($email);
        $private_data->setPreferedLanguageList($this->getCulturesForDocuments());
        $private_data->setId($user->getId());
        $private_data->setRegistered(time());
        $private_data->save();

        // add this user to pending users and logged
        $user->addToGroups(array('pending', 'logged'));

        return true;
    }

    /**
     * Return the language that the user prefers when reading documents.
     * @return string
     */
    public function getPreferedLanguage()
    {
        if ($this->hasAttribute('prefered_language'))
        {
            $lang = $this->getAttribute('prefered_language');
        }
        else
        {
            $lang = sfConfig::get('sf_i18n_default_culture', 'en');
        }
        return $lang;
    }

     /**
     * Return an array of culture ordered by preference
     * @return array document_culture
     */
    public function getPreferedLanguageList()
    {
    	$prefered_language_list = $this->getAttribute('prefered_languages_list', null);

        if(is_null($prefered_language_list) && $this->isConnected())
        {
             c2cTools::log('no prefered languages list setted');
        	 // get list from database
             $prefered_language_list = explode(',', sfDoctrine::getTable('UserPrivateData')
                                                ->find($this->getId())
                                                ->get('document_culture'));
        }

        if(count($prefered_language_list) <= 1)
        {
        	// if it is not a list... we get the symfony default language list
            $prefered_language_list = array_keys(Language::getAll());
        }

        // save in session
        $this->saveLanguageListInSession($prefered_language_list);

    	return $prefered_language_list;
    }

    public function setPreferedLanguage($prefered_language)
    {
    	if(!in_array($prefered_language, array_keys(Language::getAll())))
        {
        	throw new exception('bad language code');
        }

    	$user_languages = $this->getPreferedLanguageList();
        $user_new_languages[] = $prefered_language;

        if($user_languages[0] != $prefered_language)
        {
            for($i=0, $n=count($user_languages); $i < $n; $i++)
            {
                if($user_languages[$i] != $prefered_language)
                {
                    $user_new_languages[] = $user_languages[$i];
                }
            }
        }
        else
        {
        	$user_new_languages = $user_languages;
        }

        // save in bdd and session
        $this->savePreferedLanguageList($user_new_languages);
    }
    /**
     * Save prefered language list in user session and bdd
     *
     * @param array ordered language list
     * @author Mickael Kurmann
     **/
    public function savePreferedLanguageList($language_list)
    {
        // save in session
        $this->saveLanguageListInSession($language_list);

        // save it too in database
        $user_private_data = sfDoctrine::getTable('UserPrivateData')->find($this->getId());
        $user_private_data->setPreferedLanguageList($language_list);
        $user_private_data->save();
    }

    public function saveLanguageListInSession($language_list)
    {
        $this->setAttribute('prefered_language', $language_list[0]);
        $this->setAttribute('prefered_languages_list', $language_list);
    }

    /**
     * @return boolean
     */
    public function isConnected()
    {
        if (!$this->isAuthenticated())
        {
            // if user is not authenticated, delete PunBB cookie if any.
            Punbb::signOut();
            return false;
        }
        return true;
    }

    /**
     * Returns username (login)
     * @return string
     */
    public function getUsername()
    {
        return $this->getAttribute('username');
    }

    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * Check if the profile display is the connected
     * user profile
     *
     * @return boolean
     */
    public function isPersonalPage($id)
    {
        return ($id == $this->getId());
    }
    
    public function isDocumentOwner($document_id)
    {
         $result = Doctrine_Query::create()
                                          ->select('h.user_id')
                                          ->from('HistoryMetadata h')
                                          ->where('h.history_metadata_id IN (SELECT d.history_metadata_id
                                                                              FROM DocumentVersion d
                                                                              WHERE d.document_id = ?)
                                                  AND user_id = ?')
                                          ->limit(1)
                                          ->execute(array($document_id, $this->getId()), Doctrine::FETCH_ARRAY);
                                  
        return count($result) > 0;
    }
    
    public function belongsTo($users)
    {
        $id = $this->getId();
        foreach ($users as $user)
        {
            if ( $user['id'] == $id ) return true;
        }
        return false;
    }
    
    /**
     * Sets the main filters switch to $status
     * This is the method to use in order to set main filter switch 
     * (do not mess with cookies : they are handled by FiltersSwitchFilter)
     *
     * @param $status boolean
     */   
    public function setFiltersSwitch($status)
    {
        $this->setAttribute('filters_switch', $status);
    }
    
}
