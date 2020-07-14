<?php
/********************************
* Project: Website Backup System
* Code Version: 2.2.0
* Author: Benjamin Sommer
* Github: @remmosnimajneb
* Company: The Berman Consulting Group - BermanGroup.com
***************************************************************************************/

/*
* Run MySQL Backups
* Go through all sites and pull the backups, then FTP all files in the /mysql/ folder and then delete them
*/

$Logger  = "[Staring Website Backup System - V2.2.0]\n";
$Logger .= "[Author: Benjamin Sommer (@remmosnimajneb)]\n";
$Logger .= "[For: The Berman Consulting Group (BermanGroup.com)]\n\n";
$Logger .= "Starting MySQL Backup.... Current Time: " . date("Y-m-d - h:i:s") . "\n\n";

/* Include Config File */
require_once('Config.php');

$Logger .= "Opening JSON Sites File\n";

/* Try and open JSON Config File */
if(!file_exists(dirname(__FILE__) . "/Sites.json")){
	$Logger .= "ERROR Can't open Sites.json file! Exiting Program!";
	SendDebug(false, $Logger);
	exit();
}
$AllSites = json_decode(file_get_contents(dirname(__FILE__) . "/Sites.json"), true);
$AllSites = $AllSites["Sites"];

$Logger .= "Opened Sites.json file successfully!\n\n";

/* Keep toggle so we know if backup worked or not */
$OverallTransferredOk = true;

foreach ($AllSites as $Site) {
	$Logger .= "Backing up Website: " . $Site['NiceName'] ."\n";
	
	// Now dump each Database to the /mysql/ directory
	foreach ($Site['Databases'] as $DB) {
		
		$IndividualBackupTransfer = true;
		
		if($DB['Database'] != "NULL"){

			$Logger .= "Backing up MySQL Database: " . $DB['Database'] ."\n";
			
			/* File Name */
			$FileName = '['  . $Site['SiteName'] . ']_[' . $DB['Database'] . ']_' . date("Y-m-d_h:i:s") . '.sql';

			/* Local File */
			$LocalFilePath = dirname(__FILE__) . '/' . $FileName;

			/* Remote Path */
			$RemoteFilePath = $FTP_Folder . '/' . $FileName;

		    exec('mysqldump --user=' . $DB_User . ' --password=' . $DB_Password . ' --host=' . $DB_Host . ' ' . $DB['Database'] . ' > ' . $LocalFilePath, $output, $result);
		    
		    /* If running backup failed, show an error */
		    if($result){
		    	$OverallTransferredOk = false;

		    	$Logger .= "*ERROR: Backup attempt for " . $Site['SiteName'] . " " . $DB['Database'] . " has failed! Please see below for futher information.\n";
		    	$Logger .= $output . "\n";
		    }

		    $Logger .= "Attempting to FTP file backup for database " . $DB['Database'] ."\n";
		    
		    // Now FTP the File	
		    $ch = curl_init();
		    $fp = fopen($LocalFilePath, 'r');
		    curl_setopt($ch, CURLOPT_URL, 'ftp://' . $FTP_Server . '/' . $RemoteFilePath);
		    curl_setopt($ch, CURLOPT_USERPWD, $FTP_Username . ":" . $FTP_Password);
		    curl_setopt($ch, CURLOPT_UPLOAD, 1);
		    curl_setopt($ch, CURLOPT_INFILE, $fp);
		    curl_setopt($ch, CURLOPT_INFILESIZE, filesize($fp));
		    curl_exec($ch);
		    curl_close ($ch);
		    
	        if (!curl_errno($ch) == 0) {
	           $OverallTransferredOk = false; $IndividualBackupTransfer = false;
		    }

		    if($IndividualBackupTransfer){
    			$Logger .= "Transfer of Backup file for Database " . $DB['Database'] ." result: SUCCESS! \n\n";
    		} else {
    			$Logger .= "Transfer of Backup file for Database " . $DB['Database'] ." result: FAILED! \n\n";
    		}
    		// Delete File
    		unlink($LocalFilePath);
    	}
	}
}

$Logger .= "Ending Website Backup System V2.2.0, Sending email\n";
SendDebug($OverallTransferredOk, $Logger);
?>