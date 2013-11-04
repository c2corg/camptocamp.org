<?php
/**
 * $Id: UserPrivateData.class.php 2349 2007-11-15 15:00:05Z fvanderbiest $
 */

class UserPrivateData extends BaseUserPrivateData
{
    /**
     * hash the user pwd
     *
     * @param string $pwd
     * @return hashed string
     */
    public static function hash($pwd, $salt = null)
    {
        // if salt is null, this use the old hash function
        if (empty($salt))
        {
            return Punbb::punHash($pwd);
        }
        else
        {
            return hash('sha256', $salt.$pwd);
        }
    }

    public static function filterSetPassword_tmp($pwd)
    {
        if (is_null($pwd))
        {
            return NULL;
        }

        return self::hash($pwd);
    }

    public static function find($id)
    {
        return sfDoctrine::getTable('UserPrivateData')->find($id);
    }

    public static function findByUsername($username)
    {
        return Doctrine_Query::create()
                             ->select('u.username')
                             ->from('UserPrivateData u')
                             ->where('u.username = ?', $username)
                             ->limit(1)
                             ->execute()
                             ->getFirst();
    }

    public static function retrieveSalt($login_name)
    {
         return Doctrine_Query::create()
                              ->select('u.salt')
                              ->from('UserPrivateData u')
                              ->where('u.login_name = ?', $login_name)
                              ->limit(1)
                              ->execute()
                              ->getFirst();
    }

    public static function hasPublicProfile($id)
    {
        $user = self::find($id);
        return $user->getIsProfilePublic();
    }

    public static function isForumModerator($id)
    {
        if (!$id) return false;
        $user = self::find($id);
        return ($user->getGroupId() <= 2);
    }

    public static function generatePwd($pwd_length = 8)
    {
        $string = 'abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ23456789@#';
        $str_length = strlen($string);

        $pwd = '';

        for ($i = 0; $i < $pwd_length; $i++)
        {
            // get a nb between 0 and string length - 1
            $nb = mt_rand(0, $str_length - 1);
            // add it to the pass
            $pwd .= $string{$nb};
        }

        return $pwd;
    }

    public static function generateSalt()
    {
        return base64_encode(openssl_random_pseudo_bytes(128));
    }

    public static function retrieveByLoginNameOrEmail($loginNameOrEmail)
    {
        $loginNameOrEmail = strtolower($loginNameOrEmail);
        return Doctrine_Query::create()
                             ->from('UserPrivateData u')
                             ->where('u.login_name = ? OR u.email = ?',
                                     array($loginNameOrEmail, $loginNameOrEmail))
                             ->limit(1)
                             ->execute()
                             ->getFirst();
    }

    /**
     * Set a prefered list for document culture display
     *
     * @param array document_culture
     */
    public static function filterSetDocument_Culture($document_culture)
    {
    	if (is_array($document_culture))
        {
        	$document_culture = implode(',', $document_culture);
        }

        return $document_culture;
    }

    public function getPreferedCulture()
    {
        $cultures = $this->getDocumentCulture();
        return $cultures[0];
    }

    /**
     * Return an array instead of a String
     */
    public static function filterGetDocument_Culture($value)
    {
        return explode(',', $value);
    }

    public static function filterGetPref_cookies($value)
    {
        return unserialize($value);
    }

    public static function filterSetPref_cookies($cookie_values)
    {
        return serialize($cookie_values);
    }

    public static function filterSetLogin_name($value) {
        return strtolower($value);
    }

    public static function filterSetEmail($value) {
        return strtolower($value);
    }

    /**
     * Set the document culture field and the language field for forum
     * @param String language codes, separate by comas
     */
    public function setPreferedLanguageList($prefered_languages_list)
    {
        $this->setDocumentCulture($prefered_languages_list);

        // set the culture for the forum
        $this->set('language', Language::translateForPunBB($prefered_languages_list[0]));
    }
    
}
