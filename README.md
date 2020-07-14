# MySQL Backup System V2.2.0 for the Berman Consulting Group

- Project: Website Backup System
- Code Version: 2.2.0
- Author: Benjamin Sommer - @remmosnimajneb
- For: The Berman Consulting Group - BermanGroup.com

## Installation

1. Move all files to your webserver
2. Fill out Config.json
3. Enter sites as outlined in Sites.json
4. Setup RunMySQLDiskBackup.php as a CRON job as you wish
5. That's it!

### Config.php Variables
- $DB_Host - SQL Database Host (localhost, database.example.com)
- $DB_User - SQL Username
- $DB_Password - SQL Password (Minimum permissions "SELECT" and "LOCK TABLES")

- $FTP_Server - FTP Hostname - DO NOT include ftp://
- $FTP_Folder - Remote folder for backups, no leading or trailing '/'
- $FTP_Username - FTP Username - Needs write permissions
- $FTP_Password - FTP Password

- $ServerName - Used in Email Subject line, just for refrence

- $SendTo - Email to send run logs to
- $SendFrom - Email to send logs from (Also reply to address)

### Changelog
Version 2.2.0
- Changed FTP from ftp_put to cURL FTP
- Added Server Name to Email header for easy filtering by Server
- Added Error Handler, if the backup doesn't go well, outputs errors to email

Version 2.0.1
- Intial Version