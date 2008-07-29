<?php

require_once('include/backup.inc.php');

//backupDBs - hostname, username, password, prefix
//backupFiles - array of paths, prefix

backupDBs('localhost','username','password','my-database-backup');

backupFiles(array('/home/myuser'),'me');
backupFiles(array('/etc'),'config-files');
backupFiles(array('/var/www'),'web files');
?>