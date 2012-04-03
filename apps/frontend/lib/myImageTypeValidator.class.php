<?php

/** be sure that no unauthorized user will change the license of an image */
class myImageTypeValidator extends sfValidator
{
  public function execute (&$value, &$error)
  {
    $was = $this->getParameter('was');
    $is_moderator = $this->getParameter('is_moderator');
    if (!$is_moderator && $value != $was)
    {
        if ($was != 2)
        {
            if ($was == 1) // (collaborative)
            {
                $error = 'you have no right to switch from collaborative picture';
            }
            else // $was == 3 (copyright)
            {
                $error = 'you have no right to switch from copyright picture';
            }
            return false;
        }
        elseif ($value == 3)
        {
            $error = 'you have no right to switch from personnal to copyright picture';
            return false;
        }
    }
    return true;
  }

  public function initialize ($context, $parameters = null)
  {
    parent::initialize($context);

    $user = $context->getUser();
    $id = $context->getRequest()->getParameter('id');
    $lang = $context->getRequest()->getParameter('lang');
    $document = Document::find('Image', $id);
    $type = $document->get('image_type');
    $this->setParameter('is_moderator', $user->hasCredential('moderator'));
    $this->setParameter('was', $type);

    $this->getParameterHolder()->add($parameters);
  }
}
