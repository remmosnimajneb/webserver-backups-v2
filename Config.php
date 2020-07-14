<?php
/********************************
* Project: Website Backup System
* Code Version: 2.2.0
* Author: Benjamin Sommer - BenSommer.net
* Company: The Berman Consulting Group - BermanGroup.com
***************************************************************************************/

/*
* Config File
*/

/* MySQL Login Info */
$DB_Host = "";
$DB_User = "";
$DB_Password = "";

/* FTP Info */
$FTP_Server = "";
$FTP_Folder = "";
$FTP_Username = "";
$FTP_Password = "";

/* Server Name */
$ServerName = "";

/* Email Config */
$SendTo = "";
$SendFrom = "";

/*------------That's it! Stop Editing!------------*/

/*
* Function SendDebug
* @Description: Sends debug log to Admin on backup run
* @Param $Success - (Boolean) If ran ok or error
* @Param $Logger - (String) Text of logger
* @Return None
*/
function SendDebug($Success, $Logger){
	if($Success){
		$Subject = $GLOBALS['ServerName'] . " - SUCCESS: Website Backup System - MySQL";
	} else {
		$Subject = $GLOBALS['ServerName'] . " - FAILED: Website Backup System - MySQL";
	}
	mail($GLOBALS['SendTo'], $Subject, $Logger, 'From: ' . $GLOBALS['SendFrom'] . "\r\n" . 'Reply-To: ' . $GLOBALS['SendFrom'] . "\r\n");
}