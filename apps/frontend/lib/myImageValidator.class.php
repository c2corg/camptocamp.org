<?php
/**
 * Checks that uploaded image meet config requirements (size, type, etc.)
 */
class myImageValidator extends sfValidator
{
    public function execute (&$value, &$error)
    {
        // file upload check
        if ($value['error'])
        {
            $error = $this->getParameter('upload_error');
            return false;
        }

        $validation = sfConfig::get('app_images_validation');

        if ($value['size'] > $validation['weight'])
        {
            $error = $this->getParameter('weight_error');
            return false;
        }

        // type check
        // with symfony 1.0, the type is the one given by the browser
        // we prefer to use or own mime type checker (this is what is done in further
        // versions of symfony, using system file check)
        $mime_type = c2cTools::getMimeType($value['tmp_name']);
        if (!in_array($mime_type, $validation['mime_types']))
        {
            $error = $this->getParameter('type_error');
            return false;
        }

        if ($mime_type != 'image/svg+xml')
        {
            list($width, $height) = getimagesize($value['tmp_name']);
        }
        else
        {
            // are there any script?
            if (SVG::hasScript($value['tmp_name']))
            {
                $error = $this->getParameter('svg_script_error');
                return false;
            }

            // dimensions
            $dimensions = SVG::getSize($value['tmp_name']);
            if ($dimensions === false)
            {
                $error = $this->getParameter('svg_error');
                return false;
            }
            else
            {
                list($width, $height) = $dimensions;
            }
        }
        // height/width check
        if ($width > $validation['max_size']['width'] ||
            $height > $validation['max_size']['height'])
        {
            $error = $this->getParameter('max_dim_error');
            return false;
        }

        if ($width < $validation['min_size']['width'] ||
            $height < $validation['min_size']['height'])
        {
            $error = $this->getParameter('min_dim_error');
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
