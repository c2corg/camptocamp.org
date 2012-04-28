<?php

class sfTimestamp
{
    /**
     * This function was initially used to compute the timestamp
     * of the last modification of $files
     *
     * Nevertheless, we are now computing a 'hash' value, which is
     * more efficient. Feel free to change the name if you find a good one :)
     *
     * If file is not found, an empty string is returned
     * If one of the file is not found, it is not used for 
     * computing the timestamp
     */
    public static function getTimestamp($files)
    {
        if (is_array($files))
        {
            $c = '';
            foreach ($files as $file)
            {
                $h = sfConfig::get('app_versions_'.$file);
                if ($h)
                {
                    $c .= $h;
                }
            }
            return substr(md5($c), 0, 8);
        }
        else
        {
            return sfConfig::get('app_versions_'.$files, '');
        }
    }
}
