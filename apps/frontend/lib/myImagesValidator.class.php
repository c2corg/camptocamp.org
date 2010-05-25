<?php
/**
 * Checks that uploaded images meet config requirements (size, type, etc.)
 * $Id$
 */
class myImagesValidator extends sfValidator
{
    public function execute (&$value, &$error)
    {
        // file upload check
        foreach ($value['error'] as $error_code)
        {
            if ($error_code)
            {
                $error = $this->getParameter('upload_error');
                return false;
            }
        }

        $validation = sfConfig::get('app_images_validation');

        // weight check
        foreach ($value['size'] as $file_size)
        {
            if ($file_size > $validation['weight'])
            {
                $error = $this->getParameter('weight_error');
                return false;
            }
        }

        // type check
        // FIXME with symfony 1.0, the type is the one given by the browser
        // we prefer to use or own mime type checker (this is what is done in further
        // versions of symfony, using system file check)
        foreach ($value['tmp_name'] as $file)
        {
            $file_type = c2cTools::getMimeType($file);
            if (!in_array($file_type, $validation['mime_types']))
            {
                $error = $this->getParameter('type_error');
                return false;
            }
        }

        foreach ($value['tmp_name'] as $k => $filename)
        {
            if ($value['type'][$k] != 'image/svg+xml')
            {
                list($width, $height) = getimagesize($filename);
            }
            else
            {
                // are there any script?
                if (SVG::hasScript($filename))
                {
                    $error = $this->getParameter('svg_script_error');
                    return false;
                }

                // dimensions
                $dimensions = SVG::getSize($filename);
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
