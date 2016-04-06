<?php

/* check that email adress is not from a temp domain */
class myTempEmailValidator extends sfValidator
{
    public function execute(&$value, &$error)
    {
        $regex = '/@((([^.]+)\.)+)([a-zA-Z]{3,}|[a-zA-Z.]{5,})/';

        if (!preg_match($regex, $value, $matches) || in_array(ltrim($matches[0], '@'), array(
            'harakirimail.com', 'maildrop.cc', 'spam4.me', 'sharklasers.com', 'cuvox.de',
            'clipmail.eu', 'boximail.com', 'pjjkp.com', 'ephemail.com', 'ephemail.org',
            'ephemail.net', 'jetable.org', 'jetable.net', 'jetable.com', 'yopmail.com',
            'haltospam.com', 'tempinbox.com', 'brefemail.com', '0-mail.com', 'link2mail.net',
            'mailexpire.com', 'kasmail.com', 'spambox.info', 'mytrashmail.com', 'mailinator.com',
            'dontreg.com', 'maileater.com', 'brefemail.com', '0-mail.com', 'brefemail.com',
            'ephemail.net', 'guerrillamail.com', 'guerrillamail.info', 'haltospam.com',
            'iximail.com', 'jetable.net', 'jetable.org', 'kasmail.com', 'klassmaster.com',
            'kleemail.com', 'link2mail.net', 'mailin8r.com', 'mailinator.com', 'mailinator.net',
            'mailinator2.com', 'myamail.com', 'mytrashmail.com', 'nyms.net', 'shortmail.net',
            'sogetthis.com', 'spambox.us', 'spamday.com', 'Spamfr.com', 'spamgourmet.com',
            'spammotel.com', 'tempinbox.com', 'yopmail.fr', 'guerrillamail.org', 'guerrillamail.biz',
            'guerrillamail.de', 'guerrillamail.net', 'guerrillamail.block.com','temporaryinbox.com',
            'spamcorptastic.com', 'filzmail.com', 'lifebyfood.com', 'tempemail.net', 'spamfree24.org',
            'spamfree24.com', 'spamfree24.net', 'spamfree24.de', 'spamfree24.eu', 'spamfree24.info',
            'spamherelots.com', 'thisisnotmyrealemail.com', 'slopsbox.com', 'trashmail.net',
            'myamail.com', 'tyldd.com', 'safetymail.info', 'brefmail.com', 'bofthew.com',
            'trash-mail.com'
        )))
        {
            $error = $this->getParameterHolder()->get('temp_email_error');
            return false;
        }

        return true;
    }

    public function initialize($context, $parameters = null)
    {
        // initialize parent
        parent::initialize($context);

        // set defaults
        $this->getParameterHolder()->set('temp_email_error',  'Invalid email domain');

        $this->getParameterHolder()->add($parameters);

        return true;
    }
}
