<?php

// Class that checks for potential vandalism on document edition
// It is very very basic at the moment, and should be improved
class Vandalism
{
    private static $doc;

    public static function check($document)
    {
        self::$doc = $document;

        // Don't do anything if this is a new document or a user document
        if (!$document->getVersion() || $document->getModule() === 'users') return;

        if (self::fields_removed() >= 4 || self::i18n_fields_removed() >= 3)
        {
            self::signal();
        }
    }

    // number of i18n fields that where emptied
    // A better metric could be to compare text size of all i18n fields before and after modification
    private static function i18n_fields_removed()
    {
        return count(array_filter(self::$doc->getCurrentI18nObject()->getModified(), function($item) {
            return  $item instanceof Doctrine_Null || strlen($item) <= 3;
        }));
    }

    // number of fields that were blanked
    private static function fields_removed()
    {
        return count(array_filter(self::$doc->getModified(), function($item) {
            return $item instanceof Doctrine_Null || $item === '{0}';
        }));
    }

    private static function signal()
    {
        $i18n = sfContext::getInstance()->getI18N();

        // send an email for potential vandalism
        $email_recipient = UserPrivateData::find(108544)->getEmail(); // for now, topo-fr 108544
        $email_subject = $i18n->__('Potential vandalism');
        $server = $_SERVER['SERVER_NAME'];
        $link = "http://$server/outings/" . self::$doc->getId();
        $htmlBody = $i18n->__('The document "%1%" has been potentially vandalised',
            array('%1%' => '<a href="'.$link.'">'.self::$doc->getCurrentI18nObject()->getName().'</a>'));

        $mail = new sfMail();
        $mail->setCharset('utf-8');
        // definition of the required parameters
        $mail->setSender(sfConfig::get('app_outgoing_emails_sender'));
        $mail->setFrom(sfConfig::get('app_outgoing_emails_from'));
        $mail->addReplyTo(sfConfig::get('app_outgoing_emails_reply_to'));
        $mail->addAddress($email_recipient);
        $mail->setSubject($email_subject);
        $mail->setContentType('text/html');
        $mail->setBody($htmlBody);
        $mail->setAltBody(strip_tags($htmlBody));

        $mail->send();
    }
}
