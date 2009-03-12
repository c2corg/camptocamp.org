<?php
class sfSVN
{
    public static function getHeadRevision($file)
    {
        if ($info = file_get_contents(SF_ROOT_DIR . '/css_js_versions'))
        {
            $lines = explode("\n", $info);
            foreach ($lines as $line)
            {
              $l = explode(': ', $line);
              if ($l[0] === $file)
              {
                  return 'v=r' . $l[1];
              }
            }
        }
        return '';
    }
}