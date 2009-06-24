<?php
// AWS access info
define('awsAccessKey', '');
define('awsSecretKey', '');
define('awsBucket', '');
define('mysqlOptions', '--quote-names --quick --add-drop-table --add-locks --allow-keywords --disable-keys --extended-insert --single-transaction --create-options --comments --net_buffer_length=16384');

require_once('include/backup.inc.php');

/*

backupDBs - hostname, username, password, prefix, post backup query

  hostname = hostname of your MySQL server
  username = username to access your MySQL server (make sure the user has SELECT privliges)
  password = your password
  prefix = backup filenames will contain this prefix, this prevents overwriting other backups when you have more than one server backing up at once.
  post backup query = Any SQL statement you want to execute after the backups are completed. For example: PURGE BINARY LOGS BEFORE NOW() - INTERVAL 14 DAY;

*/

//backupFiles - array of paths, prefix

backupDBs('localhost','username','password','my-database-backup','');

backupFiles(array('/home/myuser', '/etc'),'me');
backupFiles(array('/var/www'),'web files');
?>
