<?php
/********************************
* Project: Website Backup System
* Code Version: 2.0.1
* Author: Benjamin Sommer - BenSommer.net
* Company: The Berman Consulting Group - BermanGroup.com
***************************************************************************************/

/*
* Run MySQL Backups
* Go through all sites and pull the backups, then FTP all files in the /mysql/ folder and then delete them
*/

$Logger = "[STARTING DEBUG LOGGER]\n";
$Logger .= "[Staring Website Backup System - V2.0.1]\n";
$Logger .= "[Author: Benjamin Sommer (BenSommer.net)]\n";
$Logger .= "[For: The Berman Consulting Group (BermanGroup.com)]\n";
$Logger .= "Starting MySQL Backup.... Current Time:" . date("Y-m-d_h:i:s") . "\n";

/* Include Configuration */
require_once('Config.php');

$Logger .= "Opening JSON Sites File\n";

/* Try and open the Sites.Json file */
if(!file_exists(dirname(__FILE__) . "/Sites.json")){
	$Logger .= "ERROR Can't open Sites.json file! Exiting Program!";
	SendDebug(false, $Logger);
	exit();
}

$Logger .= "Opened Sites.json file successfully!\n";

$AllSites = json_decode(file_get_contents(dirname(__FILE__) . "/Sites.json"), true);
$AllSites = $AllSites["Sites"];

$OverallTransferredOk = true;

/* Go through each site and backup the SQL Databases */
foreach ($AllSites as $Site) {
	$Logger .= "Backing up Website: " . $Site['NiceName'] ."\n";

	// Now dump each Database to the /mysql/ directory
	foreach ($Site['Databases'] as $DB) {
	$IndividualBackupTransfer = true;
		if($DB['Database'] != "NULL"){
			$Logger .= "Backing up MySQL Database: " . $DB['Database'] ."\n";

			$FileName =  dirname(__FILE__) . '/' .'['  . $Site['SiteName'] . ']_[' . $DB['Database'] . ']_' . date("Y-m-d_h:i:s") . '.sql';
		    
		    $RemoteFilePath = 'mysql/' . '['  . $Site['SiteName'] . ']_[' . $DB['Database'] . ']_' . date("Y-m-d_h:i:s") . '.sql';

		    /* Run SQL Dump */
		    exec('mysqldump --user=' . $DB_User . ' --password=' . $DB_Password . ' --host=' . $DB_Host . ' ' . $DB['Database'] . ' > ' . $FileName);

		    $Logger .= "Attempting to FTP file backup for database " . $DB['Database'] ."\n";

		    // Now FTP the File	
		    $FTP_Connection = ftp_connect($FTP_Server) or die("Could not connect to $FTP_Server");
    		$Login = ftp_login($FTP_Connection, $FTP_Username, $FTP_Password);
    			ftp_pasv($FTP_Connection, true);
    		if(!ftp_put($FTP_Connection, $RemoteFilePath, $FileName, FTP_ASCII)){ $OverallTransferredOk = false; $IndividualBackupTransfer = false; }

    		if($IndividualBackupTransfer){
    			$Logger .= "Transfer of Backup file for Database " . $DB['Database'] ." result: SUCCESS! \n";
    		} else {
    			$Logger .= "Transfer of Backup file for Database " . $DB['Database'] ." result: FAILED! \n";
    		}

    		// Delete Local File
    		unlink($FileName);
    	}
	}
}

$Logger .= "Ending Website Backup System V2.0.1, Sending email\n";

/* Send Debug Log */
SendDebug($OverallTransferredOk, $Logger);
?>