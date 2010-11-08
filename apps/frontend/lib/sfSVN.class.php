<?php
class sfSVN
{
    /**
     * This function is used to compute the latest revision of
     * the $files given as input.
     * If none of the file is versionned, the head revision
     * is returned
     */
    public static function getHeadRevision($files)
    {
        $files = is_array($files) ? $files : array($files);
        $counter = count($files);
        $max = 0;

        if ($info = file_get_contents(SF_ROOT_DIR . '/VERSION'))
        {
            $lines = explode("\n", $info);
            foreach ($lines as $line)
            {
                $l = explode(': ', $line);
                if (in_array($l[0], $files, true))
                {
                    $max = max($max, ($l[1] != 'unknown' ? intval($l[1]) : 0));
                    $counter--;
                }

                if (!$counter) return $max;
            }
        }
        return ($max == 0) ? self::getHeadRevision('head') : $max;
    }
}
