<?php
/********************************
* Project: Website Backup System
* Code Version: 2.0.1
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
$FTP_Username = "";
$FTP_Password = "";

/* Admin Email */
$AdminEmail = "";

/* Emails Debug log to Admin */
function SendDebug($Success, $Logger){
	if($Success){
		$Subject = "SUCCESS: Website Backup System - MySQL";
	} else {
		$Subject = "FAILED: Website Backup System - MySQL";
	}

	mail($AdminEmail, $Subject, $Logger, 'To: ' . $AdminEmail . "\r\n" . 'From: ' . $AdminEmail . "\r\n" . 'Reply-To: ' . $AdminEmail . "\r\n");
}