<?php
//include_once(SF_ROOT_DIR . '/VERSION');

class sfTimestamp
{
    //include_once(SF_ROOT_DIR . '/VERSION');
    /**
     * This function is used to compute the timestamp of the
     * last modification of $files
     * If file is not found, an empty string
     * is returned
     */
    public static function getTimestamp($files)
    {
        $files = is_array($files) ? $files : array($files);
        $counter = count($files);
        $max = 0;

        foreach ($files as $file)
        {
            $max = max($max, sfConfig::get('app_versions_'.$file, 0));
        }
        return ($max == 0) ? '' : $max;
    }
}
