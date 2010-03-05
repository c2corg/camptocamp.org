<?php
/**
 * Checks that uploaded file is of correct mime type
 */
class myFileMimeTypeValidator extends sfValidator
{
    public function execute (&$value, &$error)
    {
        $allowed_mime_types = $this->getParameterHolder()->get('allowed');

        $allowed_mime_types = is_array($allowed_mime_types) ? $allowed_mime_types : array($allowed_mime_types);

        // FIXME with symfony 1.0, the type is the one given by the browser
        // we prefer to use or own mime type checker (this is what is done in further
        // versions of symfony, using system file check)
        $mime_type = c2cTools::getMimeType($value['tmp_name']);
        if (!in_array($mime_type, $allowed_mime_types))
        {
            $error = $this->getParameter('type_error');
            return false;
        }

        return true;
    }

    public function initialize ($context, $parameters = null)
    {
        // Initialize parent
        parent::initialize($context);

        // Set parameters
        $this->getParameterHolder()->add($parameters);

        return true;
    }
}
