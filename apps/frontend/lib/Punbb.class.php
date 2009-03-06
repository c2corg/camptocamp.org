<?php
/**
 * PunBB connector.
 * @version $Id: Punbb.class.php 2416 2007-11-23 15:48:36Z fvanderbiest $
 */

class Punbb
{
    private static $cookie_name;
    private static $cookie_path;
    private static $cookie_domain;
    private static $cookie_secure;
    private static $cookie_seed;

    private static $config_loaded = false;

    public static function setLanguage($lang)
    {
        setcookie('language', Language::translateForPunBB($lang), time() + 86400, '/');
    }
    
    /**
     * Home-made function to sign in PunBB session (not available in PunBB)
     * @param int user_id
     * @param string password_hash
     *
     * Warning: it always returns true, whether the ids passed are good or not !
     */
    public static function signIn($user_id, $password_hash)
    {
  		self::punSetcookie($user_id, $password_hash, time() + 31536000);
  		return true;
    }

    public static function signOut()
    {
        self::punSetcookie(1, self::randomPass(8), time() + 31536000);
    }
    
    
    // BRIDGE FILES

    private static function loadConfig()
    {
        if (!self::$config_loaded)
        {
            include(sfConfig::get('app_forum_config_path'));
            self::$cookie_name = $cookie_name;
            self::$cookie_path = $cookie_path;
            self::$cookie_domain = $cookie_domain;
            self::$cookie_secure = $cookie_secure;
            self::$cookie_seed = $cookie_seed;

            self::$config_loaded = true;
        }
    }

    /**
     * @return string
     */
    public static function punHash($str)
    {
        if (function_exists('sha1'))
        {
    	    return sha1($str);
        }
    	elseif (function_exists('mhash')) // Only if Mhash library is loaded
        {
    	    return bin2hex(mhash(MHASH_SHA1, $str));
        }

        return md5($str);
    }

    private static function punSetcookie($user_id, $password_hash, $expire)
    {
  	    // the global from punbb original function
  	    self::loadConfig();

        // Enable sending of a P3P header by removing // from the following line
        // (try this if login is failing in IE6)
        //@header('P3P: CP="CUR ADM"');

        if (version_compare(PHP_VERSION, '5.2.0', '>='))
        {
    	    setcookie(self::$cookie_name,
                      serialize(array($user_id, md5(self::$cookie_seed . $password_hash))),
                      $expire,
                      self::$cookie_path,
                      self::$cookie_domain,
                      self::$cookie_secure,
                      true);
        }
    	else
        {
    		setcookie(self::$cookie_name,
                      serialize(array($user_id, md5(self::$cookie_seed . $password_hash))),
                      $expire,
                      self::$cookie_path . '; HttpOnly',
                      self::$cookie_domain,
                      self::$cookie_secure);
        }
    }

    /**
     * Randomly generate a password with the given number of characters.
     * @param integer
     * @return string
     */
    private static function randomPass($length)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        $password = '';
        for ($i = 0; $i < $length; ++$i)
        {
            $password .= substr($chars, (mt_rand() % strlen($chars)), 1);
        }

        return $password;
    }
    
    public static function getNbMessages($subjects)
    {
        // warning: 2 seq scans:
        $sql = 'SELECT COUNT(p.id), p2.subject FROM punbb_posts p LEFT JOIN punbb_topics p2 ON p.topic_id = p2.id WHERE (p2.id = p.topic_id AND p2.subject IN ( ' ."'". implode($subjects, "', '") ."'". ')) GROUP BY p2.subject';
        
        return sfDoctrine::connection()->standaloneQuery($sql)->fetchAll();
    }

    public static function getNickname($id)
    {
        if (!is_numeric($id))
            return null;
        $sql = "SELECT username FROM punbb_users WHERE id='$id';";

        return sfDoctrine::connection()->standaloneQuery($sql)->fetchAll();
    }

}
