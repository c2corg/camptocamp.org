<?php
/**
 * $Id: Sympa.class.php 2446 2007-11-29 14:03:55Z alex $
 */
class Sympa extends BaseSympa
{
    /**
     * Returns the list of mailing lists a given email belongs to.
     * @param string email
     * @return array
     */
    public static function getSubscribedLists($email)
    {
        $results = Doctrine_Query::create()
                                 ->select('s.list_subscriber')
                                 ->from('Sympa s')
                                 ->where('s.user_subscriber = ?', array($email))
                                 ->execute();
        
        $lists = array();
        foreach ($results as $result)
        {
            $lists[] = $result->list_subscriber;
        }

        return $lists;
    }

    /**
     * Adds given address to given mailing list.
     * @param string listname
     * @param string email
     * @return boolean status (true=success, false=failure)
     */
    public static function subscribe($listname, $email)
    {
        $conn = sfDoctrine::Connection();

        try {
            $conn->beginTransaction();

            $sympa = new Sympa;
            $sympa->list_subscriber = $listname;
            $sympa->user_subscriber = $email;
            $sympa->save();

            $conn->commit();
            return true;
        }
        catch (Exception $e)
        {
            // Subscription failed! For instance because email address was already registered.
            $conn->rollback();
            c2cTools::log("Failed adding address $email to list $listname: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Removes given address from given mailing list.
     * @param string listname
     * @param string email
     */
    public static function unsubscribe($listname, $email)
    {
        Doctrine_Query::create()->delete()
                                ->from('Sympa s')
                                ->where('s.list_subscriber = ? AND s.user_subscriber = ?',
                                        array($listname, $email))
                                ->execute();
    }

    /**
     * Updates email address in every subscribed lists.
     * @param string old email
     * @param string new email
     */
    public static function updateEmail($old_email, $new_email)
    {
        Doctrine_Query::create()->update('Sympa s')
                                ->set('s.user_subscriber', '?')
                                ->where('s.user_subscriber = ?')
                                ->execute(array($new_email, $old_email));
    }
}
