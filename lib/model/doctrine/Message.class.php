<?php
/**
 * $Id$
 */

class Message extends BaseMessage
{
    
    /*
     * gets welcome message in a given culture.
     *
     */
    public static function find($culture)
    {
        $obj = sfDoctrine::getTable('Message')->find($culture);

        if ($obj) 
        {
            return $obj->get('message');
        }
        else
        {
            return null;
        }
    }

    
    /*
     * sets welcome message in a given culture.
     *
     */
    public static function doSave($msg, $culture)
    {
        $obj = sfDoctrine::getTable('Message')->find($culture);

        if ($obj) 
        {
            $obj->set('message', $msg);

            try
            {
                $obj->save();
                return true;
            }
            catch (Exception $e)
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

}