<?php
class RememberKey extends BaseRememberKey
{
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

    // we assume we won't have any collision... 
    public static function getKey($key)
    {
        return Doctrine_Query::create()
                             ->from('RememberKey rk')
                             ->where('rk.remember_key = ?', $key)
                             ->execute()
                             ->getFirst();
    }

    public static function generateRandomKey()
    {
        return md5(base64_encode(openssl_random_pseudo_bytes(30)));
    }

    // we assume we won't have any collision...
    public static function deleteKey($key)
    {
        Doctrine_Query::create()
                      ->delete()
                      ->from('RememberKey rk')
                      ->where('rk.remember_key = ?', $key)
                      ->execute();
    }

    // delete all keys of a user
    public static function deleteUserKeys($userid)
    {
        Doctrine_Query::create()
                      ->delete()
                      ->from('RememberKey rk')
                      ->where('rk.user_id = ?', $userid)
                      ->execute();
    }
}
