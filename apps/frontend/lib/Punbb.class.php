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
        self::loadConfig();
        setcookie('language', Language::translateForPunBB($lang), time() + 86400, self::$cookie_path);
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
        self::punSetcookie($user_id, $password_hash, 0);
        return true;
    }

    public static function signOut()
    {
        self::loadConfig();
        sfContext::getInstance()->getResponse()->setCookie(self::$cookie_name, '', null, self::$cookie_path);
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
     * old punbb hash function
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

    public static function getNickname($id)
    {
        if (!is_numeric($id))
            return null;
        $sql = "SELECT username FROM punbb_users WHERE id='$id';";

        return sfDoctrine::connection()->standaloneQuery($sql)->fetchAll();
    }


    public static function MarkTopicAsread($topic_id, $last_post_time)
    {
        // let's reproduce behaviour of mark_topic_read() from punbb
        // note that forum_id is always 1 for comments

        $user_id = sfContext::getInstance()->getUser()->getId();

        $sql = "SELECT last_visit, read_topics FROM punbb_users WHERE id='$user_id';";
        $result = sfDoctrine::connection()->standaloneQuery($sql)->fetchAll();
        $last_visit = $result[0]['last_visit'];
        $read_topics = unserialize($result[0]['read_topics']);

        if ($last_visit >= $last_post_time)
        {
            return;
        }
        else if (!empty($read_topics['f'][1]) && $read_topics['f'][1] >= $last_post_time)
        {
            return;
        }
        else if (!empty($read_topics['t'][$topic_id]) && $read_topics['t'][$topic_id] >= $last_post_time)
        {
            return;
        }
        else // topic is new
        {
            $read_topics['t'][$topic_id] = $last_post_time;
            $sql = "UPDATE punbb_users SET read_topics='".pg_escape_string(serialize($read_topics))."' WHERE id='$user_id';";
            sfDoctrine::connection()->standaloneQuery($sql);
        }
    }
}
