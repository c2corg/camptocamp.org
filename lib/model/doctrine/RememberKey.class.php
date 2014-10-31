<?php
class RememberKey extends BaseRememberKey
{
    public static function deleteOtherKeysForUserId($user_id)
    {
        Doctrine_Query::create()
                      ->delete('RememberKey')
                      ->from('RememberKey rk')
                      ->where('rk.user_id = ?', $user_id)
                      ->execute();
    }
    
    public static function deleteOldKeys()
    {
        // Get a new date formatter
        $dateFormat = new sfDateFormat();
    
        $expiration_age = sfConfig::get('app_remember_key_expiration_age', 31 * 24 * 3600 );
        $expiration_time_value = $dateFormat->format(time() - $expiration_age, 'I');
        Doctrine_Query::create()
                      ->delete('RememberKey')
                      ->from('RememberKey rk')
                      ->where('rk.created_at < ?', $expiration_time_value )
                      ->execute();
    }
    
    public static function existsKey($key)
    {
        $res = Doctrine_Query::create()
                             ->select('COUNT(rk.user_id) nb')
                             ->from('RememberKey rk')
                             ->where('rk.remember_key = ?', $key)
                             ->execute()
                             ->getFirst()->nb;

        return isset($res);
    }

    public static function generateRandomKey()
    {
        return md5(base64_encode(openssl_random_pseudo_bytes(30)));
    }

    public static function deleteKey($key, $userid)
    {
        Doctrine_Query::create()
                      ->delete()
                      ->from('RememberKey rk')
                      ->where('rk.remember_key = ? AND rk.user_id = ?', array($key, $userid))
                      ->execute();
    }

    // delete all keys of a user
    public static function deleteKeys($userid)
    {
        Doctrine_Query::create()
                      ->delete()
                      ->from('RememberKey rk')
                      ->where('rk.user_id = ?', $userid)
                      ->execute();
    }
}
