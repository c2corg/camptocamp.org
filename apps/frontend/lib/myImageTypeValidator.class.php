<?php

class myImageTypeValidator extends sfValidator
{
  public function execute (&$value, &$error)
  {
    $error = 'you have no right to switch from collaborative to personal picture';
    $was_collaborative = $this->getParameter('was_collaborative');
    $is_moderator = $this->getParameter('is_moderator');
    if (!$is_moderator && $was_collaborative && $value == 2)
    {
      return false;
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
    $collaborative_image = ($document->get('image_type') == 1);
    $this->setParameter('is_moderator', $user->hasCredential('moderator'));
    $this->setParameter('was_collaborative', $collaborative_image);

    $this->getParameterHolder()->add($parameters);
  }
}