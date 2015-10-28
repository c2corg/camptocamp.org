<?php
include_partial(c2cTools::mobileVersion() ? 'common/mobile_header' : 'common/header');

if (sfConfig::get('app_production') != 1)
{
    include_partial('common/dev_env');
}

// FIXME: hack to retrieve symfony user, sometimes lost for some unknown raison
if (empty($sf_user))
{
    $sf_user = sfContext::getInstance()->getUser();
}

if ($sf_user->getCulture() == 'fr' && !c2cTools::mobileVersion())
{
    include_partial('common/donate');
}

// we need to remove flash messages because the flash filter does not get executed in the forum
$userAttributeHolder = $sf_user->getAttributeHolder();
$names = $userAttributeHolder->getNames('symfony/flash');
if ($names)
{
    foreach ($names as $name)
    {
        $userAttributeHolder->remove($name, 'symfony/flash');
    }
}
