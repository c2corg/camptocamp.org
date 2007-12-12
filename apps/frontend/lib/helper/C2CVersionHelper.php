<?php
/**
 * $Id: C2CVersionHelper.php 651 2007-06-26 12:49:10Z alex $
 */

function c2c_revision()
{
    if ($version_info = file_get_contents(SF_ROOT_DIR . '/VERSION'))
    {
        // VERSION file is a multiline text file containing keywords depending
        // on the SVN tool language setting. Revision is available at line 5.
        $info = explode("\n", $version_info);
        $revision_string = $info[4];
        $revision_array = explode(': ', $revision_string);
        return $revision_array[1];
    }

    return '';
}
