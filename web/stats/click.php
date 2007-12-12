<?php
# PHP click counter (CCount)
# Version: 1.1
# File name: click.php
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

error_reporting(E_ALL ^ E_NOTICE);

// Get settings from the settings.php file
require "settings.php";

// First check if the ID is set and if it is valid (contains nothing but numbers)
$id=$_GET['id'];
if(empty($id) || preg_match("/\D/",$id)) {die("Invalid ID, numbers (0-9) only!");}

// Different systems use different line endings
if($settings['system'] == 2) {$newline="\r\n";}
elseif($settings['system'] == 3) {$newline="\r";}
else {$newline="\n";}

// Get lines from file
$lines=file($settings['logfile']);

// Let's found the line that starts with our ID number
$found=0;
$i=0;
foreach ($lines as $thisline) {
    if (preg_match("/^($id\%\%)/",$thisline)) {
    	$thisline=chop($thisline);
    	// We found the line, now we get URL and count from the line
        list($id,$added,$url,$count,$name)=explode("%%",$thisline);
        // Increase count by 1 and update this line
        $count++;
        $lines[$i]=$id."%%".$added."%%".$url."%%".$count."%%".$name.$newline;
        $found=1;
        break;
    }
    // This line didn't start with ID, lets go to the next one
    $i++;
}
if($found != 1) {die("This ID doesn't exist!");}

// Rewrite the log file with the updated line
$content = implode('', $lines);
$fp = fopen($settings['logfile'],"wb") or die("Can't write to log file! Please Change the file permissions (CHMOD to 666 on UNIX machines!)");
fputs($fp,$content);
fclose($fp);

// Redirect to the link URL
Header("Location: $url");
exit();
?>