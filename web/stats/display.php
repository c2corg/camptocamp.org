<?php
# PHP click counter (CCount) - display number of clicks on a link
# Version: 1.1
# File name: display.php
# Written 22nd January 2005 by Klemen Stirn (info@phpjunkyard.com)
# http://www.PHPJunkYard.com

##############################################################################
# COPYRIGHT NOTICE                                                           #
# Copyright 2004-2005 PHPJunkYard All Rights Reserved.                       #
#                                                                            #
# The CCount may be used and modified free of charge by anyone so long as    #
# this copyright notice and the comments above remain intact. By using this  #
# code you agree to indemnify Klemen Stirn from any liability that might     #
# arise from it's use.                                                       #
#                                                                            #
# Selling the code for this program without prior written consent is         #
# expressly forbidden. In other words, please ask first before you try and   #
# make money off this program.                                               #
#                                                                            #
# Obtain permission before redistributing this software over the Internet or #
# in any other medium. In all cases copyright and header must remain intact. #
# This Copyright is in full effect in any country that has International     #
# Trade Agreements with the United States of America or with                 #
# the European Union.                                                        #
##############################################################################

#############################
#     DO NOT EDIT BELOW     #
#############################

require_once "settings.php";

if($settings['system'] == 2) {$settings['newline']="\r\n";}
elseif($settings['system'] == 3) {$settings['newline']="\r";}
else {$settings['newline']="\n";}

echo "var ccount_link = new Array();\n";

$lines = file($settings['logfile']);

foreach ($lines as $thisline) {
	trim($thisline);
	list($id,$added,$url,$count,$linkname)=explode("%%",$thisline);
    echo "ccount_link[$id]=$count;\n";
}

echo "
function ccount_display(id)
{
document.write(ccount_link[id]);
}
";

exit();
?>