<?php
# PHP click counter (CCount) - admin panel
# Version: 1.1
# File name: admin.php
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

require_once "settings.php";
if($settings['system'] == 2) {$settings['newline']="\r\n";}
elseif($settings['system'] == 3) {$settings['newline']="\r";}
else {$settings['newline']="\n";}

/* Start user session or output an error */
if (!session_start())
{
error("Cannot start a new PHP session. Please contact server administrator or webmaster!");
}

/* If no action parameter is set let's force visitor to login */
if (empty($_REQUEST['action']))
{
    if (isset($_SESSION['logged']) && $_SESSION['logged'] == "Y")
    {
        mainpage();
    }
    else
    {
        login();
    }
}
else
{
$action=htmlspecialchars($_REQUEST['action']);
}

/* Do the action that is set in $action variable */
if ($action == "login")
    {
    checkpassword();
    $_SESSION['logged']="Y";
    mainpage("welcome");
    }
elseif ($action == "remove")
    {
    checklogin();
    $id=checkid();
    removelink($id);
    }
elseif ($action == "reset")
    {
    checklogin();
    $id=checkid();
    resetlink($id);
    }
elseif ($action == "add")
    {
    checklogin();
    $url=checkurl($_POST['url']);
    add($url);
    }
elseif ($action == "restore")
    {
    checklogin();
    restore();
    }
elseif ($action == "logout")
    {
    logout();
    }
else {login();}
exit();

function restore() {
global $settings;
$ext = strtolower(substr(strrchr($_FILES['backup']['name'], "."), 1));
if ($ext != "txt") {error("This doesn't seem to be the right backup file. CCount
backup file should be named <b>$settings[logfile]</b>!");}

    if (@move_uploaded_file($_FILES['backup']['tmp_name'], $settings['logfile']))
    {
    }
    else
    {
        error("There has been an error uploading the backup file! Please make
        sure your CCount directory is world-writable. On UNIX machines CHMOD
        it to 777 (rwx-rwx-rwx)!");
    }

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
<link rel="STYLESHEET" type="text/css" href="style.css">
<title>PHP Click counter admin panel</title>
</head>
<body>
<div align="center"><center>
<table border="0" width="700">
<tr>
<td align="center" class="glava"><font class="header">PHP Click counter <?php echo($settings['verzija']); ?><br>-- Admin panel --</font></td>
</tr>
<tr>
<td class="vmes"><p>&nbsp;</p>
<div align="center"><center>
<table width="400" cellpadding="3"> <tr>
<td align="center" class="head">Backup restored: <?php echo $_FILES['backup']['name']; ?></td>
</tr>
<tr>
<td class="dol">
<form>
<p>&nbsp;</p>
<p align="center"><b>Backup successfully restored!</b></p>
<p>Your backup has been successfully restored. If this was a valid CCount
backup file your counter should work OK now!</p>
<p>&nbsp;</p>
<p align="center">
<a href="admin.php?<?php echo strip_tags (SID)?>">
Click to continue</a></p>
<p>&nbsp;</p>
</td>
</tr> </table>
</div></center>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
</tr>
<tr>
<!--
Changing the "Powered by" credit sentence without purchasing a licence is illegal!
Please visit http://www.phpjunkyard.com/copyright-removal.php for more information.
-->
<td align="center" class="copyright">Powered by <a href="http://www.phpjunkyard.com/php-click-counter.php" target="_new">PHP click counter</a> <?php echo($settings['verzija']); ?><br>
(c) Copyright 2004-2005 <a href="http://www.phpjunkyard.com/" target="_new">PHPjunkyard - Free PHP scripts</a></td>
</tr>
</table>
</div></center>
</body>
</html>
<?php
exit();
} // END restore() //


function add($url) {
global $settings;

$name=$_POST['name'];
if (empty($name) || !preg_match("/\S/",$name)) {$name="";}
if (strlen($name)>40) {error("Your link name is too long! Please choose a shorter name (max 40 chars).");}

$fp = fopen($settings['idfile'],"rb") or die("Can't open the id file ($settings[idfile]) for reading!");
$previd=fread($fp,filesize($settings['idfile']));
fclose($fp);
$previd = trim(chop($previd));
$previd++;
$fp = fopen($settings['idfile'],"wb") or die("Can't open the id file ($settings[idfile]) for reading!");
fputs($fp,$previd);
fclose($fp);

$today = date('Y/m/d');
$addline = $previd . "%%" . $today . "%%" . $url . "%%0%%" . $name . $settings['newline'];

$fp = fopen($settings['logfile'],"ab") or die("Can't write to log file! Please Change the file permissions (CHMOD to 666 on UNIX machines!)");
fputs($fp,$addline);
fclose($fp);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
<link rel="STYLESHEET" type="text/css" href="style.css">
<title>PHP Click counter admin panel</title>
</head>
<body>
<div align="center"><center>
<table border="0" width="700">
<tr>
<td align="center" class="glava"><font class="header">PHP Click counter <?php echo($settings['verzija']); ?><br>-- Admin panel --</font></td>
</tr>
<tr>
<td class="vmes"><p>&nbsp;</p>
<div align="center"><center>
<table width="400" cellpadding="3"> <tr>
<td align="center" class="head">Link added</td>
</tr>
<tr>
<td class="dol">
<form>
<p>&nbsp;</p>
<p align="center"><b>New link successfully added!</b></p>
<p>A new link with ID <?php echo($previd); ?> has been successfully added.</p>
<p>To count clicks on this link use this URL:<br><br>
<b><?php echo("$settings[click_url]?id=$previd"); ?></b><br><br>
instead of the old one:<br><br>
<?php echo($url); ?></p>
<p>&nbsp;</p>
<p align="center">
<a href="admin.php?<?php echo strip_tags (SID)?>">
Click to continue</a></p>
<p>&nbsp;</p>
</td>
</tr> </table>
</div></center>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
</tr>
<tr>
<!--
Changing the "Powered by" credit sentence without purchasing a licence is illegal!
Please visit http://www.phpjunkyard.com/copyright-removal.php for more information.
-->
<td align="center" class="copyright">Powered by <a href="http://www.phpjunkyard.com/php-click-counter.php" target="_new">PHP click counter</a> <?php echo($settings['verzija']); ?><br>
(c) Copyright 2004-2005 <a href="http://www.phpjunkyard.com/" target="_new">PHPjunkyard - Free PHP scripts</a></td>
</tr>
</table>
</div></center>
</body>
</html>
<?php
exit();
}
// END add() //

function resetlink($id) {
global $settings;
$fp = fopen($settings['logfile'],"rb") or die("Can't open the log file ($settings[logfile]) for reading!");
$content=fread($fp,filesize($settings['logfile']));
fclose($fp);

$content = trim(chop($content));
$lines = explode($settings['newline'],$content);

$found=0;

$fp = fopen($settings['logfile'],"wb") or die("Can't write to log file! Please Change the file permissions (CHMOD to 666 on UNIX machines!)");
    foreach ($lines as $thisline) {
        if (preg_match("/^($id\%\%)/",$thisline)) {
            $found=1;
            list($id,$added,$url,$count,$name)=explode("%%",$thisline);
            $thisline=$id."%%".$added."%%".$url."%%0%%".$name;
        }
    $thisline .= $settings['newline'];
    fputs($fp,$thisline);
    }
fclose($fp);

if($found != 1) {error("This ID doesn't exist!");}

mainpage("Link with ID $id was successfully reset to 0 clicks!");
}
// END resetlink() //

function removelink($id) {
global $settings;
$fp = fopen($settings['logfile'],"rb") or die("Can't open the log file ($settings[logfile]) for reading!");
$content=fread($fp,filesize($settings['logfile']));
fclose($fp);

$content = trim(chop($content));
$lines = explode($settings['newline'],$content);

$found=0;

$fp = fopen($settings['logfile'],"wb") or die("Can't write to log file! Please Change the file permissions (CHMOD to 666 on UNIX machines!)");
    foreach ($lines as $thisline) {
        if (preg_match("/^($id\%\%)/",$thisline)) {$found=1; continue;}
    $thisline .= $settings['newline'];
    fputs($fp,$thisline);
    }
fclose($fp);

if($found != 1) {error("This ID doesn't exist!");}

mainpage("Link with ID $id was successfully removed!");
}
// END removelink() //

function mainpage($notice="") {
global $settings;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
<link rel="STYLESHEET" type="text/css" href="style.css">
<title>PHP Click counter admin panel</title>
<script language="Javascript" type="text/javascript"><!--
function doconfirm(message) {
    if (confirm(message)) {return true;}
    else {return false;}
}
//-->
</script>
</head>
<body>
<div align="center"><center>
<table border="0" width="700" cellpadding="5">
<tr>
<td align="center" class="glava"><font class="header">PHP Click counter <?php echo($settings['verzija']); ?><br>-- Admin panel --</font></td>
</tr>
<tr>
<td class="vmes">
<?php
if ($notice == "welcome") {
    echo "<p align=\"center\"><b>Welcome to admin panel!</b></p>";
    }
else {
    echo "<p align=\"center\"><b>Admin panel</b></p>
    <p align=\"center\"><font color=\"#FF0000\">$notice</font></p>";
    }
?>
<p><a href="#addlink">Add a new link</a><br>
<a href="admin.php?action=logout">LOGOUT</a></p>
<hr>
<p><b>Link statistics</b></p>
<?php
$lines=array();
$totalclicks="";
$linewidth="";
$maxclicks=0;
$maxid=0;
$noyet=0;

$fp = @fopen($settings['logfile'],"rb") or die("Can't open the log file ($settings[logfile]) for reading!");
$content=@fread($fp,filesize($settings['logfile']));
fclose($fp);
$content = trim(chop($content));
    if (strlen($content) == 0) {$noyet=1;}
$lines = explode($settings['newline'],$content);

if ($noyet == 1)
    {
    ?>
<p>Not counting any links. Use the form below to add new links to be counted.</p>
    <?php
    }
else {
    $i=0;
        foreach ($lines as $thisline) {
            list($id,$added,$url,$count,$linkname)=explode("%%",$thisline);
            $totalclicks += $count;
                if($count > $maxclicks) {$maxclicks = $count;$maxid=$id;}
            $i++;
        }
    $average = $totalclicks/$i;
    $average = number_format($average,1);
    echo "<p>Total links: <b>$i</b><br>
    Total clicks: <b>$totalclicks</b><br>
    Average clicks per link: <b>$average</b>";
        if ($maxclicks != 0) {
        echo "<br>Maximum clicks per link: <b>$maxclicks</b> (Link ID $maxid)";
        }
    echo "</p>\n";
}

if ($maxclicks == 0 || $totalclicks == 0) {$totalimagewidth=0;}
else {
$maxlinewidth=($maxclicks/$totalclicks)*100;
$maxlinewidth=round($maxlinewidth);
    if ($maxlinewidth < 20) {$totalimagewidth=1000;}
    elseif ($maxlinewidth < 40) {$totalimagewidth=500;}
    elseif ($maxlinewidth < 60) {$totalimagewidth=334;}
    elseif ($maxlinewidth < 80) {$totalimagewidth=250;}
    else {$totalimagewidth=200;}
}

if ($noyet == 0) {
echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\"><tr>
<td algin=\"center\" valgin=\"center\" class=\"first\">&nbsp;</td>
<td algin=\"center\" valgin=\"center\" class=\"first\"> <b>ID</b> </td>
<td algin=\"center\" valgin=\"center\" class=\"second\"> <b>Clicks</b> </td>
<td algin=\"center\" valgin=\"center\" class=\"first\"> <b>Added</b> </td>
<td algin=\"center\" valgin=\"center\" class=\"second\"> <b>Web page</b> </td>
<td valgin=\"center\" class=\"first\"> <b>Graph</b> </td>
</tr>";

    foreach ($lines as $thisline) {
        trim($thisline);
        list($id,$added,$url,$count,$linkname)=explode("%%",$thisline);
            if ($count==0 || $totalclicks==0) {$linewidth="1";}
            else {
            $linewidth=($count/$totalclicks)*$totalimagewidth;
            $linewidth=round($linewidth);
                if ($linewidth == 0) {$linewidth="1";}
            }

            $linkname=rtrim($linkname);
            if (empty($linkname))
            {
                if (strlen($url) > 40)
                {
                    $linkname = substr($url, 0, 20);
                    $linkname .= "...";
                    $linkname .= substr($url, -17);
                }
                else
                {
                    $linkname=$url;
                }
            }

        echo "<tr>
<td algin=\"center\" valgin=\"center\" class=\"first\"><a href=\"admin.php?".SID."&action=remove&id=$id\" onclick=\"return doconfirm('Are you sure you want to remove link ID $id? This cannot be undone!');\"><img src=\"delete.gif\" height=\"14\" width=\"16\" border=\"0\" alt=\"Remove this link\"></a>
<a href=\"admin.php?".SID."&action=reset&id=$id\" onclick=\"return doconfirm('Are you sure you want to reset clicks for link ID $id to 0? This cannot be undone!');\"><img src=\"reset.gif\" height=\"14\" width=\"16\" border=\"0\" alt=\"Reset number of clicks to 0\"></a></td>
<td algin=\"center\" valgin=\"center\" class=\"first\"> $id </td>
<td algin=\"center\" valgin=\"center\" class=\"second\"> <b>$count</b> </td>
<td algin=\"center\" valgin=\"center\" class=\"first\"> $added </td>
<td algin=\"center\" valgin=\"center\" class=\"second\"> <a href=\"$url\" target=\"_new\" class=\"link\">$linkname</a> </td>
<td valgin=\"center\" class=\"first\"> <img src=\"line.gif\" height=\"5\" width=\"$linewidth\" border=\"0\"> </td>
        </tr>";
    }
echo "</table>
<p>Note: For best performance you should remove links you don't use anymore by
clicking the <img src=\"delete.gif\" height=\"14\" width=\"16\" border=\"0\"> button.
You can reset number of clicks on a link to 0 by clicking the
<img src=\"reset.gif\" height=\"14\" width=\"16\" border=\"0\" alt=\"Remove this link\"> button.</p>
";
}

?>
<hr>
<form action="admin.php?<?php echo strip_tags (SID)?>" method="POST">
<p><a name="#addlink"></a><b>Add a link</b></p>
<p>Use this form to add a new URL link to track clicks. Please type in the
<b>full URL</b> of the link below:<br>
<input type="hidden" name="action" value="add">
<input type="hidden" name="pass" value="<?php echo($settings['apass']); ?>">
<input type="text" name="url" value="http://" size="50">
</p>
<p><b>Link name:</b> (Optional)<br>
Choose a unique name for this link (max 40 chars). This name will be displayed
as &quot;Web page&quot; in the statistics. If you don't choose a name the link
URL will be displayed (if longer than 40 chars it will be shorten to 40 chars).<br>
<input type="text" name="name" size="40" maxlength="40"> </p>
<p><input type="submit" value=" Add link "></p>
</form>
<hr>
<p><b>Download backup</b></p>
<p>You may download a backup of the link database and restore it in the future
should your link file be corrupted for any reason. To download a backup
click the link below and select &quot;File &gt; Save as&quot; or &quot;Save to
disk&quot; in the window that will open:</p>
<p><a href="<?php echo $settings['logfile']; ?>" target="_new">Click to download backup</a></p>
<p><b>Restore backup</b></p>
<form action="admin.php?<?php echo strip_tags (SID)?>" method="POST" enctype="multipart/form-data">
<p><input type="hidden" name="action" value="restore">
Use this form to restore a previously downloaded backup file. This action cannot be undone!</p>
<p>Select your backup file:<br>
<input type="file" name="backup" size="30"></p>
<p><input type="submit" value="Click to restore this backup"></p>
</form>
<hr>
<p><b>Usage</b></p>
<p>To track clicks on a link, use this URL instead of the link original URL:</p>
<p><?php echo($settings['click_url']); ?>?id=<b>ID</b></p>
<p>Replace <b>ID</b> with the ID number of the URL, for example:</p>
<p><?php echo($settings['click_url']); ?>?id=13</p>
<hr>
<p><b>Rate this script</b></p>
<p>If you like this script please rate it or even write a review at:</p>
<p><a href="http://www.hotscripts.com/Detailed/36874.html" target="_new">Rate
this Script @ Hot Scripts</a></p>
<p><a href="http://php.resourceindex.com/rate?05375" target="_new">Rate
this Script @ The PHP Resource Index</a></p>
<hr>
<p><b>Stay updated</b></p>
<p>Join my FREE newsletter and you will be notified about new scripts, new
versions of the existing scripts and other important news from PHPJunkYard.<br>
<a href="http://www.phpjunkyard.com/newsletter.php"
target="_new">Click here for more info</a></p>
<hr>
<p>&nbsp;</p>
</td>
</tr>
<tr>
<!--
Changing the "Powered by" credit sentence without purchasing a licence is illegal!
Please visit http://www.phpjunkyard.com/copyright-removal.php for more information.
-->
<td align="center" class="copyright">Powered by <a href="http://www.phpjunkyard.com/php-click-counter.php" target="_new">PHP click counter</a> <?php echo($settings['verzija']); ?><br>
(c) Copyright 2004-2005 <a href="http://www.phpjunkyard.com/" target="_new">PHPjunkyard - Free PHP scripts</a></td>
</tr>
</table>
</div></center>
</body>
</html>
<?php
exit();
}
// END mainpage() //

function checkurl($url) {
    if (empty($url) || $url == "http://" || $url == "https://")
    {
    error("Please enter URL of the link you wish to add!");
    }
    return $url;
}
// END checkurl() //

function checkid() {
    if(empty($_REQUEST['id']))
    {
    error("Please enter a link ID number!");
    }
    else
    {
    $id=htmlentities($_REQUEST['id']);
        if (preg_match("/\D/",$id))
        {
        error("This is not a valid link ID, use numbers (0-9) only!");
        }
    }
    return $id;
}
// END checkid() //

function checklogin() {
    if (isset($_SESSION['logged']) && $_SESSION['logged'] == "Y")
    {
        return true;
    }
    else
    {
        error("You are not authorized to view this page!");
    }
}
// END checklogin() //

function checkpassword() {
global $settings;

    if(empty($_POST['pass']))
    {
    error("Please enter your admin password!");
    }
    else
    {
    $pass=htmlspecialchars($_POST['pass']);
    }

    if ($pass != $settings['apass'])
    {
    error("Wrong password!");
    }

}
// END checkpassword() //

function logout() {
session_unset();
session_destroy();
global $settings;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
<link rel="STYLESHEET" type="text/css" href="style.css">
<title>PHP Click counter admin panel</title>
</head>
<body>
<div align="center"><center>
<table border="0" width="700">
<tr>
<td align="center" class="glava"><font class="header">PHP Click counter <?php echo($settings['verzija']); ?><br>-- Admin panel --</font></td>
</tr>
<tr>
<td class="vmes"><p>&nbsp;</p>
<div align="center"><center>
<table width="400"> <tr>
<td align="center" class="head">LOGGED OUT</td>
</tr>
<tr>
<td align="center" class="dol">
<p>&nbsp;</p>
<p><b>You have been successfully logged out.</b></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
</tr> </table>
</div></center>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
</tr>
<tr>
<!--
Changing the "Powered by" credit sentence without purchasing a licence is illegal!
Please visit http://www.phpjunkyard.com/copyright-removal.php for more information.
-->
<td align="center" class="copyright">Powered by <a href="http://www.phpjunkyard.com/php-click-counter.php" target="_new">PHP click counter</a> <?php echo($settings['verzija']); ?><br>
(c) Copyright 2004-2005 <a href="http://www.phpjunkyard.com/" target="_new">PHPjunkyard - Free PHP scripts</a></td>
</tr>
</table>
</div></center>
</body>
</html>
<?php
exit();
}
// END logout() //

function login() {
global $settings;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
<link rel="STYLESHEET" type="text/css" href="style.css">
<title>PHP Click counter admin panel</title>
</head>
<body>
<div align="center"><center>
<table border="0" width="700">
<tr>
<td align="center" class="glava"><font class="header">PHP Click counter <?php echo($settings['verzija']); ?><br>-- Admin panel --</font></td>
</tr>
<tr>
<td class="vmes"><p>&nbsp;</p>
<div align="center"><center>
<table width="400"> <tr>
<td align="center" class="head">Enter admin panel</td>
</tr>
<tr>
<td align="center" class="dol"><form method="POST" action="admin.php?<?php echo strip_tags (SID)?>"><p>&nbsp;<br><b>Please type in your admin password</b><br><br>
<input type="password" name="pass" size="20"><input type="hidden" name="action" value="login"></p>
<p><input type="submit" name="enter" value="Enter admin panel"></p>
</form>
</td>
</tr> </table>
</div></center>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
</tr>
<tr>
<!--
Changing the "Powered by" credit sentence without purchasing a licence is illegal!
Please visit http://www.phpjunkyard.com/copyright-removal.php for more information.
-->
<td align="center" class="copyright">Powered by <a href="http://www.phpjunkyard.com/php-click-counter.php" target="_new">PHP click counter</a> <?php echo($settings['verzija']); ?><br>
(c) Copyright 2004-2005 <a href="http://www.phpjunkyard.com/" target="_new">PHPjunkyard - Free PHP scripts</a></td>
</tr>
</table>
</div></center>
</body>
</html>
<?php
exit();
}
// END login() //

function error($myproblem) {
global $settings;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
<link rel="STYLESHEET" type="text/css" href="style.css">
<title>PHP Click counter admin panel</title>
</head>
<body>
<div align="center"><center>
<table border="0" width="700">
<tr>
<td align="center" class="glava"><font class="header">PHP Click counter <?php echo($settings['verzija']); ?><br>-- Admin panel --</font></td>
</tr>
<tr>
<td class="vmes"><p>&nbsp;</p>
<div align="center"><center>
<table width="400"> <tr>
<td align="center" class="head">ERROR</td>
</tr>
<tr>
<td align="center" class="dol">
<p>&nbsp;</p>
<p><b>An error occured:</b></p>
<p><?php echo($myproblem); ?></p>
<p>&nbsp;</p>
<p><a href="admin.php?<?php echo strip_tags (SID)?>">Back to the previous page</a></p>
<p>&nbsp;</p>
</td>
</tr> </table>
</div></center>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
</tr>
<tr>
<!--
Changing the "Powered by" credit sentence without purchasing a licence is illegal!
Please visit http://www.phpjunkyard.com/copyright-removal.php for more information.
-->
<td align="center" class="copyright">Powered by <a href="http://www.phpjunkyard.com/php-click-counter.php" target="_new">PHP click counter</a> <?php echo($settings['verzija']); ?><br>
(c) Copyright 2004-2005 <a href="http://www.phpjunkyard.com/" target="_new">PHPjunkyard - Free PHP scripts</a></td>
</tr>
</table>
</div></center>
</body>
</html>
<?php
exit();
}
// END error() //

?>
