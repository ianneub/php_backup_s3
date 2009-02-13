<?php
// AWS access info
define('awsAccessKey', '');
define('awsSecretKey', '');
define('awsBucket', '');

require_once('include/backup.inc.php');

//backupDBs - hostname, username, password, prefix
//backupFiles - array of paths, prefix

backupDBs('localhost','username','password','my-database-backup');

backupFiles(array('/home/myuser', '/etc'),'me');
backupFiles(array('/var/www'),'web files');
?>
