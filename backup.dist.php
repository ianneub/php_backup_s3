<?php
// AWS access info
define('awsAccessKey', '');
define('awsSecretKey', '');
define('awsBucket', '');
define('debug',false);

//Pass these options to mysqldump
define('mysqlDumpOptions', '--quote-names --quick --add-drop-table --add-locks --allow-keywords --disable-keys --extended-insert --single-transaction --create-options --comments --net_buffer_length=16384');

define('hourly',false); // Will this script run daily or hourly?

require_once('include/backup.inc.php');

/*

backupDBs - hostname, username, password, prefix, [post backup query]

  hostname = hostname of your MySQL server
  username = username to access your MySQL server (make sure the user has SELECT privliges)
  password = your password
  prefix = backup filenames will contain this prefix, this prevents overwriting other backups when you have more than one server backing up at once.
  post backup query = Optional: Any SQL statement you want to execute after the backups are completed. For example: PURGE BINARY LOGS BEFORE NOW() - INTERVAL 14 DAY;

*/
backupDBs('localhost','username','password','my-database-backup','');

/*

backupFiles - array of paths, [prefix]
  
  array of paths = An array of one or more file paths that you want backed up
  prefix = Optional: backup filenames will contain this prefix, this prevents overwriting other backups when you have more than one server backing up at once.

*/
backupFiles(array('/home/myuser', '/etc'),'me');
backupFiles(array('/var/www'),'web files');
?>