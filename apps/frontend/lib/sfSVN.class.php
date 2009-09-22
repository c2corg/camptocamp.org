<?php
class sfSVN
{
    public static function getHeadRevision($file)
    {
        if ($info = file_get_contents(SF_ROOT_DIR . '/VERSION'))
        {
            $lines = explode("\n", $info);
            foreach ($lines as $line)
            {
              $l = explode(': ', $line);
              if ($l[0] === $file)
              {
                  return $l[1] != 'unknown' ? $l[1] : 0;
              }
            }
        }
        return '';
    }
}
