<?php

// includes
require_once('S3.php');

//Setup variables
$MYSQL_OPTIONS = '--quote-names --quick --add-drop-table --add-locks --allow-keywords --disable-keys --extended-insert --single-transaction --create-options --comments --net_buffer_length=16384';
$BACKUP_BUCKET = 'igoblue_backup';

//Setup S3 class
$s3 = new S3(awsAccessKey, awsSecretKey);

//delete old backups
deleteBackups($BACKUP_BUCKET);

// Backup functions

// Backup files and compress for storage
function backupFiles($targets, $prefix = '') {
	global $BACKUP_BUCKET, $s3;
		
	foreach ($targets as $target) {
		// compress local files
		$cleanTarget = urlencode($target);
		`tar cjf $prefix-$cleanTarget.tar.bz2 $target`;

		// upload to s3
		$s3->putObjectFile("$prefix-$cleanTarget.tar.bz2",$BACKUP_BUCKET,s3Path($prefix,$target."-backup.tar.bz2"));
		
		// remove temp file
		`rm -rf $prefix-$cleanTarget.tar.bz2`;
	}
}

// Backup all Mysql DBs using mysqldump
function backupDBs($hostname, $username, $password, $prefix = '') {
	global $MYSQL_OPTIONS, $DATE, $s3;
	global $BACKUP_BUCKET;
	
	// Connecting, selecting database
	$link = mysql_connect($hostname, $username, $password) or die('Could not connect: ' . mysql_error());
	
	$query = 'SHOW DATABASES';
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	
	$databases = array();
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	    foreach ($row as $database) {
			$databases[] = $database;
	    }
	}
	
	// Free resultset
	mysql_free_result($result);

	// Closing connection
	mysql_close($link);
		
	//Run backups on each DB found
	foreach ($databases as $database) {
		`/usr/bin/mysqldump $MYSQL_OPTIONS --no-data --host=$hostname --user=$username --password='$password' $database | bzip2  > $database-structure-backup.sql.bz2`;
		`/usr/bin/mysqldump $MYSQL_OPTIONS --host=$hostname --user=$username --password='$password' $database | bzip2 > $database-data-backup.sql.bz2`;
		$s3->putObjectFile("$database-structure-backup.sql.bz2",$BACKUP_BUCKET,s3Path($prefix,"/".$database."-structure-backup.sql.bz2"));
		$s3->putObjectFile("$database-data-backup.sql.bz2",$BACKUP_BUCKET,s3Path($prefix,"/".$database."-data-backup.sql.bz2"));
		
		`rm -rf $database-structure-backup.sql.bz2 $database-data-backup.sql.bz2`;
	}
	
}

function deleteBackups($bucket) {
	global $s3;
	
	//delete the backup from 2 months ago	
	$set_date = strtotime('-2 months');
	
	//only if it wasn't the first of the month
	if ((int)date('j',$set_date) === 1) return true;
	
	//set s3 "dir" to delete
	$prefix = s3Path('','',$set_date);
	
	//find files to delete
	$keys = $s3->getBucket($bucket,$prefix);

	//delete each key found
	foreach ($keys as $key => $meta) {
		$s3->deleteObject($bucket,$key);
	}
	
	//echo $prefix."\n";
	
	//delete the backup from 2 weeks ago
	$set_date = strtotime('-2 weeks');
	
	//only if it wasn't a saturday or the 1st
	if ((int)date('j',$set_date) === 1 || (string)date('l',$set_date) === "Saturday") return true;
	
	//set s3 "dir" to delete
	$prefix = s3Path('','',$set_date);
	
	//find files to delete
	$keys = $s3->getBucket($bucket,$prefix);

	//delete each key found
	foreach ($keys as $key => $meta) {
		$s3->deleteObject($bucket,$key);
	}
	
	//debug
	//print_r($keys);
	//echo $prefix."\n";
}

function s3Path($prefix, $name, $timestamp = null) {
	if (is_null($timestamp)) $timestamp = time();
	
	$date = date("Y/m/d/",$timestamp);
	
	return "backups/".$date.$prefix.$name;
}

?>